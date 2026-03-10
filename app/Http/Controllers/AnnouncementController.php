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
            $data = DB::select("CALL usp_populate_fields(?)", [1]);

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
    $rules = [
        'title'       => 'required|string|max:255',
        'description' => 'required|string',
        'subject_id'  => 'required|integer',
        'date_posted' => 'required|date',
    ];

    $validated = $request->validate($rules);
    $validated['user_id'] = auth()->id();
    try {
        DB::statement("CALL usp_sql_actions(?, ?)", [2, json_encode($validated)]);
    } catch (\Exception $e) {
        Log::error("Announcement Store Error: " . $e->getMessage());
        return response()->json([
            'status'  => 'error',
            'message' => 'Database error: ' . $e->getMessage()
        ], 500);
    }

    return response()->json([
        'status'  => 'success',
        'message' => 'Announcement posted successfully.'
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
}