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

            $announcementId = null;
            if (!empty($result) && isset($result[0]->announcement_id)) {
                $announcementId = $result[0]->announcement_id;
            }

            if (!$announcementId) {
                return response()->json(['status' => 'error', 'message' => 'Failed to retrieve announcement ID.'], 500);
            }

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

    // ── Fetch single announcement for editing ─────────────────────────────────
    public function show($id)
    {
        try {
            $data = DB::select("CALL usp_get_announcements_data(?, ?)", [4, (int) $id]);

            if (empty($data)) {
                return response()->json(['status' => 'error', 'message' => 'Announcement not found.'], 404);
            }

            $announcement = (array) $data[0];

            // Get section IDs for this announcement
            $sectionIds = DB::table('announcement_sections')
                ->where('announcement_id', (int) $id)
                ->pluck('section_id')
                ->toArray();

            $announcement['section_ids'] = $sectionIds;

            return response()->json(['status' => 'success', 'data' => $announcement]);
        } catch (\Exception $e) {
            Log::error("Announcement Show Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
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

            // Delete existing sections (could be converted to SP mode 22 if desired)
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
            // Use stored procedure mode 21 to delete announcement and its sections
            DB::statement("CALL usp_sql_actions(?, ?)", [21, json_encode(['id' => (int) $request->id])]);

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

            if (empty($data)) {
                return response()->json(['status' => 'success', 'data' => []]);
            }

            return response()->json(['status' => 'success', 'data' => $data]);
        } catch (\Exception $e) {
            Log::error("Teacher Announcements Error: " . $e->getMessage());
            return response()->json(['status' => 'success', 'data' => []]);
        }
    }

    // ── Student: fetch announcements for their section ───────────────────
    public function studentAnnouncements()
    {
        try {
            $data = DB::select("CALL usp_get_announcements_data(?, ?)", [2, auth()->id()]);

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
            $data = DB::select("CALL usp_get_announcements_data(?, ?)", [5, (int) $id]);

            return response()->json(['status' => 'success', 'data' => $data]);
        } catch (\Exception $e) {
            Log::error("Announcement getSections Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }
}