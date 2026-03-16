<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ScheduleController extends Controller
{
    // ── Display the schedule page based on user role ─────────────────────
    public function index()
    {
        $user = auth()->user();
        $role = $user->role_id ?? null;

        // Admin (role_id = 3)
        if ($role == 3) {
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

            return view('schedule', compact('role', 'gradeLevels', 'sections', 'subjects', 'teachers'));
        }

        // Teacher (role_id = 1) and Student (role_id = 2) share the same view
        // Only $role is needed to determine which endpoint to call
        return view('schedule', compact('role'));
    }

    // ── Unified list method that routes based on role ───────────────────
    public function list(Request $request)
    {
        $user = auth()->user();
        $role = $user->role_id ?? null;

        try {
            // Route to appropriate method based on role
            if ($role == 3) {
                return $this->adminList($request);
            } elseif ($role == 1) {
                return $this->teacherList();
            } elseif ($role == 2) {
                return $this->studentList();
            } else {
                return response()->json([
                    'status' => 'error', 
                    'message' => 'Invalid user role: ' . $role
                ], 403);
            }
        } catch (\Exception $e) {
            Log::error("Schedule List Error: " . $e->getMessage());
            return response()->json([
                'status' => 'error', 
                'message' => $e->getMessage()
            ], 400);
        }
    }

    // ── Admin: fetch all schedules via usp_get_data MODE 9 ───────────────
    private function adminList(Request $request)
    {
        try {
            $rows = collect(DB::select("CALL usp_get_data(?, ?)", [9, 0]));

            // Apply filters if provided
            if ($request->filled('section_id')) {
                $rows = $rows->filter(fn($r) => $r->section_id == $request->section_id);
            }
            if ($request->filled('grade_level_id')) {
                $rows = $rows->filter(fn($r) => $r->grade_level_id == $request->grade_level_id);
            }
            if ($request->filled('teacher_id')) {
                $rows = $rows->filter(fn($r) => $r->user_id == $request->teacher_id);
            }

            return response()->json([
                'status' => 'success', 
                'data' => $rows->values()
            ]);
        } catch (\Exception $e) {
            Log::error("Admin Schedule List Error: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    // ── Teacher: their own schedule — usp_get_teacher_schedule ───────────────
    public function teacherList()
    {
        try {
            $userId = (int) auth()->id();
            Log::info('Teacher schedule list called', ['user_id' => $userId]);
            
            $rows = DB::select("CALL usp_get_teacher_schedule(?)", [$userId]);
            
            return response()->json([
                'status' => 'success', 
                'data' => $rows
            ]);
        } catch (\Exception $e) {
            Log::error("Teacher Schedule Error: " . $e->getMessage());
            return response()->json([
                'status' => 'error', 
                'message' => $e->getMessage()
            ], 400);
        }
    }

    // ── Student: their section's schedule — usp_get_student_schedule ─────────
    public function studentList()
    {
        try {
            $userId = (int) auth()->id();
            Log::info('Student schedule list called', ['user_id' => $userId]);
            
            $rows = DB::select("CALL usp_get_student_schedule(?)", [$userId]);
            
            Log::info('Student schedule data', ['count' => count($rows)]);
            
            return response()->json([
                'status' => 'success', 
                'data' => $rows
            ]);
        } catch (\Exception $e) {
            Log::error("Student Schedule Error: " . $e->getMessage());
            return response()->json([
                'status' => 'error', 
                'message' => $e->getMessage()
            ], 400);
        }
    }

    // ── Admin: store new schedule ────────────────────────────────────────
    public function store(Request $request)
    {
        // Verify admin access
        $user = auth()->user();
        $role = $user->role_id ?? null;
        
        if ($role != 3) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Unauthorized - Admin access required'
            ], 403);
        }

        $request->validate([
            'subject_id'     => 'required|exists:subject,id',
            'user_id'        => 'required|exists:users,id',
            'section_id'     => 'required|exists:section,id',
            'grade_level_id' => 'required|exists:grade_level,id',
            'room'           => 'required|string|max:100',
            'day'            => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday',
            'time_start'     => 'required|date_format:H:i',
            'time_end'       => 'required|date_format:H:i|after:time_start',
        ]);

        try {
            DB::select("CALL usp_sql_actions(?, ?)", [12, json_encode([
                'subject_id'     => (int) $request->subject_id,
                'user_id'        => (int) $request->user_id,
                'section_id'     => (int) $request->section_id,
                'grade_level_id' => (int) $request->grade_level_id,
                'room'           => $request->room,
                'day'            => $request->day,
                'time_start'     => $request->time_start,
                'time_end'       => $request->time_end,
            ])]);
            
            return response()->json([
                'status' => 'success', 
                'message' => 'Schedule added successfully.'
            ]);
        } catch (\Exception $e) {
            Log::error("Schedule Store Error: " . $e->getMessage());
            return response()->json([
                'status' => 'error', 
                'message' => $e->getMessage()
            ], 422);
        }
    }

    // ── Admin: update existing schedule ──────────────────────────────────
    public function update(Request $request)
    {
        // Verify admin access
        $user = auth()->user();
        $role = $user->role_id ?? null;
        
        if ($role != 3) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Unauthorized - Admin access required'
            ], 403);
        }

        $request->validate([
            'id'             => 'required|exists:schedule,id',
            'subject_id'     => 'required|exists:subject,id',
            'user_id'        => 'required|exists:users,id',
            'section_id'     => 'required|exists:section,id',
            'grade_level_id' => 'required|exists:grade_level,id',
            'room'           => 'required|string|max:100',
            'day'            => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday',
            'time_start'     => 'required|date_format:H:i',
            'time_end'       => 'required|date_format:H:i|after:time_start',
        ]);

        try {
            DB::select("CALL usp_sql_actions(?, ?)", [13, json_encode([
                'id'             => (int) $request->id,
                'subject_id'     => (int) $request->subject_id,
                'user_id'        => (int) $request->user_id,
                'section_id'     => (int) $request->section_id,
                'grade_level_id' => (int) $request->grade_level_id,
                'room'           => $request->room,
                'day'            => $request->day,
                'time_start'     => $request->time_start,
                'time_end'       => $request->time_end,
            ])]);
            
            return response()->json([
                'status' => 'success', 
                'message' => 'Schedule updated successfully.'
            ]);
        } catch (\Exception $e) {
            Log::error("Schedule Update Error: " . $e->getMessage());
            return response()->json([
                'status' => 'error', 
                'message' => $e->getMessage()
            ], 422);
        }
    }

    // ── Admin: delete schedule ───────────────────────────────────────────
    public function destroy(Request $request)
    {
        // Verify admin access
        $user = auth()->user();
        $role = $user->role_id ?? null;
        
        if ($role != 3) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Unauthorized - Admin access required'
            ], 403);
        }

        $request->validate(['id' => 'required|exists:schedule,id']);

        try {
            DB::select("CALL usp_sql_actions(?, ?)", [14, json_encode(['id' => (int) $request->id])]);
            
            return response()->json([
                'status' => 'success', 
                'message' => 'Schedule deleted successfully.'
            ]);
        } catch (\Exception $e) {
            Log::error("Schedule Delete Error: " . $e->getMessage());
            return response()->json([
                'status' => 'error', 
                'message' => $e->getMessage()
            ], 422);
        }
    }
}