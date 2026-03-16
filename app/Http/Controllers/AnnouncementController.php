<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            try {
                $data = DB::select("CALL usp_populate_fields(?, ?)", [1, null]);
                return response()->json(['status' => 'success', 'data' => $data]);
            } catch (\Exception $e) {
                Log::error("Announcement Portal Error: " . $e->getMessage());
                return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
            }
        }

        return view('announcements');
    }

    // ── Store announcement ────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'subject_id'     => 'required|integer',
            'sections'       => 'required|array|min:1',
            'sections.*'     => 'integer',
            'add_to_calendar'=> 'nullable|boolean',
            'calendar_date'  => 'nullable|date|required_if:add_to_calendar,1',
        ]);

        // Insert announcement using your stored procedure
        try {
            $announcementData = [
                'title'           => $request->title,
                'description'     => $request->description ?? '',
                'subject_id'      => (int) $request->subject_id,
                'date_posted'     => now()->toDateString(),
                'user_id'         => auth()->id(),
                'add_to_calendar' => $request->input('add_to_calendar', 0) ? 1 : 0,
                'calendar_date'   => $request->input('calendar_date'),
            ];

            $result = DB::select("CALL usp_sql_actions(?, ?)", [2, json_encode($announcementData)]);
            
            // Get the announcement ID from the result
            $announcementId = null;
            if (!empty($result) && isset($result[0]->announcement_id)) {
                $announcementId = $result[0]->announcement_id;
            }

            if (!$announcementId) {
                return response()->json(['status' => 'error', 'message' => 'Failed to retrieve announcement ID.'], 500);
            }

            // Insert sections
            foreach ($request->sections as $sectionId) {
                $gradeLevel = DB::table('section')
                    ->where('id', (int) $sectionId)
                    ->value('grade_level_id');

                DB::select("CALL usp_sql_actions(?, ?)", [3, json_encode([
                    'announcement_id' => (int) $announcementId,
                    'section_id'      => (int) $sectionId,
                    'grade_level_id'  => (int) $gradeLevel,
                ])]);
            }

            return response()->json(['status' => 'success', 'message' => 'Announcement posted successfully.']);
        } catch (\Exception $e) {
            Log::error("Announcement Store Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()], 500);
        }
    }

    // ── Update announcement ───────────────────────────────────────────────────
    public function update(Request $request)
    {
        $request->validate([
            'id'              => 'required|integer',
            'title'           => 'required|string|max:255',
            'description'     => 'nullable|string',
            'subject_id'      => 'required|integer',
            'sections'        => 'required|array|min:1',
            'sections.*'      => 'integer',
            'add_to_calendar' => 'nullable|boolean',
            'calendar_date'   => 'nullable|date|required_if:add_to_calendar,1',
        ]);

        try {
            // Update announcement using stored procedure
            $updateData = [
                'id'              => (int) $request->id,
                'title'           => $request->title,
                'description'     => $request->description ?? '',
                'subject_id'      => (int) $request->subject_id,
                'date_posted'     => now()->toDateString(),
                'user_id'         => auth()->id(),
                'add_to_calendar' => $request->input('add_to_calendar', 0) ? 1 : 0,
                'calendar_date'   => $request->input('calendar_date'),
            ];

            DB::select("CALL usp_sql_actions(?, ?)", [4, json_encode($updateData)]);

            // Delete existing sections
            DB::table('announcement_sections')
                ->where('announcement_id', $request->id)
                ->delete();

            // Insert new sections
            foreach ($request->sections as $sectionId) {
                $gradeLevel = DB::table('section')
                    ->where('id', (int) $sectionId)
                    ->value('grade_level_id');

                DB::select("CALL usp_sql_actions(?, ?)", [3, json_encode([
                    'announcement_id' => (int) $request->id,
                    'section_id'      => (int) $sectionId,
                    'grade_level_id'  => (int) $gradeLevel,
                ])]);
            }

            return response()->json([
                'status'  => 'success',
                'message' => 'Announcement updated successfully.'
            ]);
        } catch (\Exception $e) {
            Log::error("Announcement Update Error: " . $e->getMessage());
            return response()->json([
                'status'  => 'error',
                'message' => 'Database error: ' . $e->getMessage()
            ], 500);
        }
    }

    // ── Delete announcement ───────────────────────────────────────────────────
    public function destroy(Request $request)
    {
        $request->validate(['id' => 'required|integer']);

        try {
            // Delete sections first (foreign key constraint)
            DB::table('announcement_sections')
                ->where('announcement_id', $request->id)
                ->delete();

            // Delete announcement
            DB::table('announcements')
                ->where('id', $request->id)
                ->where('user_id', auth()->id())
                ->delete();

            return response()->json(['status' => 'success', 'message' => 'Announcement deleted successfully.']);
        } catch (\Exception $e) {
            Log::error("Announcement Delete Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()], 500);
        }
    }

    // ── Teacher: fetch their own announcements ───────────────────
    public function getAnnouncements()
    {
        try {
            $data = DB::select("CALL usp_get_announcements_data(?, ?)", [1, auth()->id()]);
            
            // Handle case when no data
            if (empty($data)) {
                return response()->json(['status' => 'success', 'data' => []]);
            }
            
            return response()->json(['status' => 'success', 'data' => $data]);
        } catch (\Exception $e) {
            Log::error("Teacher Announcements Error: " . $e->getMessage());
            // Return empty array instead of error for teachers with no announcements
            return response()->json(['status' => 'success', 'data' => []]);
        }
    }

    // ── Student: fetch announcements for their section ───────────────────
    public function studentAnnouncements()
    {
        try {
            $data = DB::select("CALL usp_get_announcements_data(?, ?)", [2, auth()->id()]);
            
            // Handle case when no data
            if (empty($data)) {
                return response()->json(['status' => 'success', 'data' => []]);
            }
            
            return response()->json(['status' => 'success', 'data' => $data]);
        } catch (\Exception $e) {
            Log::error("Student Announcements Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    // ── Admin: fetch all announcements ────────────────────────
    public function getAllAnnouncements()
    {
        try {
            $data = DB::select("CALL usp_get_announcements_data(?, ?)", [3, 0]);
            
            // Handle case when no data
            if (empty($data)) {
                return response()->json(['status' => 'success', 'data' => []]);
            }
            
            return response()->json(['status' => 'success', 'data' => $data]);
        } catch (\Exception $e) {
            Log::error("Admin Announcements Fetch Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    public function adminIndex()
    {
        return view('announcements');
    }

    // ── Fetch section IDs + grade level for a specific announcement ───────────
    public function getSections($id)
    {
        try {
            $rows = DB::table('announcement_sections as ans')
                ->join('section as s',     's.id',  '=', 'ans.section_id')
                ->join('grade_level as gl', 'gl.id', '=', 's.grade_level_id')
                ->where('ans.announcement_id', (int) $id)
                ->orderBy('gl.id')
                ->orderBy('s.section_name')
                ->select(
                    'ans.section_id',
                    's.section_name',
                    's.grade_level_id',
                    'gl.grade_level_name'
                )
                ->get();

            return response()->json(['status' => 'success', 'data' => $rows]);
        } catch (\Exception $e) {
            Log::error("Announcement getSections Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }
}