<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClassListController extends Controller
{
    // ── Display the class list page ───────────────────────────────────────────
    public function index()
    {
        return view('teacher.class-list');
    }

    // ── Fetch the teacher's schedule grouped by subject + section ─────────────
    // usp_get_data MODE 13 — teacher's schedule (subject + section + students)
    // scoped by U_ID = auth()->id()
    //
    // Returns rows with:
    //   subject_id, subject_name,
    //   section_id, section_name, grade_level_name,
    //   day, time_start, time_end, room,
    //   student_count
    public function schedule(Request $request)
    {
        try {
            $rows = DB::select("CALL usp_get_data(?, ?)", [13, (int) auth()->id()]);
            return response()->json(['status' => 'success', 'data' => $rows]);
        } catch (\Exception $e) {
            Log::error("Class List Schedule Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    // ── Fetch students enrolled in a specific section ─────────────────────────
    // usp_get_data MODE 14 — students in section, scoped to teacher's subject
    // params: section_id (required), subject_id (optional — for context)
    public function students(Request $request)
    {
        $request->validate([
            'section_id' => 'required|integer',
        ]);

        try {
            $rows = DB::select("CALL usp_get_data(?, ?)", [14, (int) $request->section_id]);
            return response()->json(['status' => 'success', 'data' => $rows]);
        } catch (\Exception $e) {
            Log::error("Class List Students Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }
}
