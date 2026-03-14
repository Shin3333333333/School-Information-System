<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ScheduleController extends Controller
{
    // ── Display the schedule page ─────────────────────────────────────────────
    public function index()
    {
        $gradeLevels = DB::table('grade_level')->orderBy('id')->get();
        $sections    = DB::table('section as s')
                         ->join('grade_level as gl', 's.grade_level_id', '=', 'gl.id')
                         ->select('s.id', 's.section_name', 'gl.grade_level_name', 's.grade_level_id')
                         ->orderBy('gl.id')->orderBy('s.section_name')
                         ->get();
        $subjects    = DB::table('subject')->orderBy('subject_name')->get();
        $teachers    = DB::table('users as u')
                         ->join('teacher_details as td', 'u.details_id', '=', 'td.id')
                         ->where('u.role_id', 1)
                         ->where('u.status', 'Active')
                         ->select('u.id', DB::raw("CONCAT(td.fname, ' ', td.lname) AS teacher_name"))
                         ->orderBy('teacher_name')
                         ->get();

        return view('admin.schedule', compact('gradeLevels', 'sections', 'subjects', 'teachers'));
    }

    // ── Fetch all schedules via SP MODE 9 ─────────────────────────────────────
    public function list(Request $request)
    {
        try {
            $rows = collect(DB::select("CALL usp_get_data(?, ?)", [9, 0]));

            // Optional filters
            if ($request->filled('section_id')) {
                $rows = $rows->filter(fn($r) => $r->section_id == $request->section_id);
            }
            if ($request->filled('grade_level_id')) {
                $rows = $rows->filter(fn($r) => $r->grade_level_id == $request->grade_level_id);
            }
            if ($request->filled('teacher_id')) {
                $rows = $rows->filter(fn($r) => $r->user_id == $request->teacher_id);
            }

            return response()->json(['status' => 'success', 'data' => $rows->values()]);
        } catch (\Exception $e) {
            Log::error("Schedule List Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    // ── Insert via SP MODE 12 ─────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'subject_id'    => 'required|exists:subject,id',
            'user_id'       => 'required|exists:users,id',
            'section_id'    => 'required|exists:section,id',
            'grade_level_id'=> 'required|exists:grade_level,id',
            'room'          => 'required|string|max:100',
            'day'           => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday',
            'time_start'    => 'required|date_format:H:i',
            'time_end'      => 'required|date_format:H:i|after:time_start',
        ]);

        try {
            $data = [
                'subject_id'     => (int) $request->subject_id,
                'user_id'        => (int) $request->user_id,
                'section_id'     => (int) $request->section_id,
                'grade_level_id' => (int) $request->grade_level_id,
                'room'           => $request->room,
                'day'            => $request->day,
                'time_start'     => $request->time_start,
                'time_end'       => $request->time_end,
            ];
            DB::select("CALL usp_sql_actions(?, ?)", [12, json_encode($data)]);
            return response()->json(['status' => 'success', 'message' => 'Schedule added successfully.']);
        } catch (\Exception $e) {
            Log::error("Schedule Store Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }
    }

    // ── Update via SP MODE 13 ─────────────────────────────────────────────────
    public function update(Request $request)
    {
        $request->validate([
            'id'            => 'required|exists:schedule,id',
            'subject_id'    => 'required|exists:subject,id',
            'user_id'       => 'required|exists:users,id',
            'section_id'    => 'required|exists:section,id',
            'grade_level_id'=> 'required|exists:grade_level,id',
            'room'          => 'required|string|max:100',
            'day'           => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday',
            'time_start'    => 'required|date_format:H:i',
            'time_end'      => 'required|date_format:H:i|after:time_start',
        ]);

        try {
            $data = [
                'id'             => (int) $request->id,
                'subject_id'     => (int) $request->subject_id,
                'user_id'        => (int) $request->user_id,
                'section_id'     => (int) $request->section_id,
                'grade_level_id' => (int) $request->grade_level_id,
                'room'           => $request->room,
                'day'            => $request->day,
                'time_start'     => $request->time_start,
                'time_end'       => $request->time_end,
            ];
            DB::select("CALL usp_sql_actions(?, ?)", [13, json_encode($data)]);
            return response()->json(['status' => 'success', 'message' => 'Schedule updated successfully.']);
        } catch (\Exception $e) {
            Log::error("Schedule Update Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }
    }

    // ── Delete via SP MODE 14 ─────────────────────────────────────────────────
    public function destroy(Request $request)
    {
        $request->validate(['id' => 'required|exists:schedule,id']);

        try {
            DB::select("CALL usp_sql_actions(?, ?)", [14, json_encode(['id' => (int) $request->id])]);
            return response()->json(['status' => 'success', 'message' => 'Schedule deleted successfully.']);
        } catch (\Exception $e) {
            Log::error("Schedule Delete Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }
    }
}
