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

            return response()->json([
                'status' => 'success',
                'data'   => $data
            ]);

        } catch (\Exception $e) {
            Log::error("Announcement Portal Error: " . $e->getMessage());

            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    return view('announcements');
}
public function store(Request $request)
{
    $request->validate([
        'title'         => 'required|string|max:255',
        'description'   => 'required|string',
        'subject_id'    => 'required|integer',
        'date_posted'   => 'required|date',
        'section_ids'   => 'required|array|min:1',
        'section_ids.*' => 'integer',
    ]);

    $announcementData = [
        'title'       => $request->title,
        'description' => $request->description,
        'subject_id'  => (int) $request->subject_id,
        'date_posted' => $request->date_posted,
        'user_id'     => auth()->id(),
    ];

    try {
        $result = DB::select("CALL usp_sql_actions(?, ?)", [2, json_encode($announcementData)]);
    } catch (\Exception $e) {
        Log::error("Announcement Insert Error: " . $e->getMessage());
        return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
    }

    $announcementId = $result[0]->announcement_id ?? null;

    if (!$announcementId) {
        return response()->json(['status' => 'error', 'message' => 'Failed to retrieve announcement ID.'], 500);
    }

    // Insert each section link via MODE 3 — grade_level_id is resolved inside the SP
    try {
        foreach ($request->section_ids as $sectionId) {
            DB::select("CALL usp_sql_actions(?, ?)", [3, json_encode([
                'announcement_id' => (int) $announcementId,
                'section_id'      => (int) $sectionId,
            ])]);
        }
    } catch (\Exception $e) {
        Log::error("Section Link Error: " . $e->getMessage());
        return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
    }

    return response()->json(['status' => 'success', 'message' => 'Announcement posted successfully.']);
}
public function update(Request $request)
{
    $request->validate([
        'id'            => 'required|integer',
        'title'         => 'required|string|max:255',
        'description'   => 'required|string',
        'subject_id'    => 'required|integer',
        'date_posted'   => 'required|date',
        'section_ids'   => 'required|array|min:1',
        'section_ids.*' => 'integer',
    ]);

    try {
        // Step 1: Update the announcement
        $data = [
            'id'          => (int) $request->id,
            'title'       => $request->title,
            'description' => $request->description,
            'subject_id'  => (int) $request->subject_id,
            'date_posted' => $request->date_posted,
            'user_id'     => auth()->id(),
        ];

        DB::select("CALL usp_sql_actions(?, ?)", [4, json_encode($data)]);

        // Step 2: Delete old section links then re-insert
        DB::table('announcement_sections')
            ->where('announcement_id', $request->id)
            ->delete();

        foreach ($request->section_ids as $sectionId) {
            DB::table('announcement_sections')->insert([
                'announcement_id' => (int) $request->id,
                'section_id'      => (int) $sectionId,
            ]);
        }

    } catch (\Exception $e) {
        Log::error("Announcement Update Error: " . $e->getMessage());
        return response()->json([
            'status'  => 'error',
            'message' => 'Database error: ' . $e->getMessage()
        ], 500);
    }

    return response()->json([
        'status'  => 'success',
        'message' => 'Announcement updated successfully.'
    ]);
}
public function destroy(Request $request)
{
    $request->validate([
        'id' => 'required|integer',
    ]);

    try {
        // Delete section links first (cascade)
        DB::table('announcement_sections')
            ->where('announcement_id', $request->id)
            ->delete();

        // Delete the announcement
        DB::table('announcements')
            ->where('id', $request->id)
            ->where('user_id', auth()->id()) // ensure teacher can only delete their own
            ->delete();

    } catch (\Exception $e) {
        Log::error("Announcement Delete Error: " . $e->getMessage());
        return response()->json([
            'status'  => 'error',
            'message' => 'Database error: ' . $e->getMessage()
        ], 500);
    }

    return response()->json([
        'status'  => 'success',
        'message' => 'Announcement deleted successfully.'
    ]);
}
public function getAnnouncements()
{
    try {
        $userId = auth()->id();
        $data = DB::select("CALL usp_get_data(?, ?)", [3, $userId]);

        return response()->json([
            'status' => 'success',
            'data'   => $data
        ]);

    } catch (\Exception $e) {
        Log::error("Announcement Fetch Error: " . $e->getMessage());

        return response()->json([
            'status'  => 'error',
            'message' => $e->getMessage()
        ], 400);
    }
}
// public function getSections()
// {
//     try {
//         $sections = DB::select("CALL usp_populate_fields(?, ?)", [2, null]); // MODE 2
//         return response()->json(['status' => 'success', 'data' => $sections]);
//     } catch (\Exception $e) {
//         return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
//     }
// }
public function getAllAnnouncements()
{
    try {
        $data = DB::select("CALL usp_get_data(?, ?)", [5, 0]);
        return response()->json(['status' => 'success', 'data' => $data]);
    } catch (\Exception $e) {
        Log::error("Admin Announcements Fetch Error: " . $e->getMessage());
        return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
    }
}
public function adminIndex()
{
    return view('admin.announcements');
}
}