<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GradeController extends Controller
{
    // ── Display the grades view ───────────────────────────────────────────────
    // All roles share resources/views/grades.blade.php.
    // $role is passed to the view to show/hide CRUD controls:
    //   role_id 0 = Admin   → full CRUD
    //   role_id 1 = Teacher → full CRUD  (SP scopes their sections)
    //   role_id 2 = Student → read-only  (SP scopes their own grades)
    public function index()
    {
        // usp_populate_fields MODE 1 → subjects
        // usp_populate_fields MODE 2 → sections (with grade_level_id)
        // usp_populate_fields MODE 4 → grade levels
        $subjects    = DB::select("CALL usp_populate_fields(?, ?)", [1, null]);
        $sections    = DB::select("CALL usp_populate_fields(?, ?)", [2, null]);
        $gradeLevels = DB::select("CALL usp_populate_fields(?, ?)", [4, null]);

        $role = auth()->user()->role_id ?? null;

        return view('grades', compact('gradeLevels', 'sections', 'subjects', 'role'));
    }

    // ── Fetch grades ──────────────────────────────────────────────────────────
    // usp_get_data MODE 10 → all grades (Admin,   2nd param = 0 / ignored)
    // usp_get_data MODE 11 → teacher's grades  (2nd param = teacher user_id)
    // usp_get_data MODE 12 → student's own grades (2nd param = student user_id)
    //
    // NOTE: Modes 10-12 are NEW — add them to usp_get_data (see grades_sp.sql).
    // Existing modes 1-9 are untouched.
    public function list(Request $request)
    {
        try {
            $user   = auth()->user();
            $roleId = $user->role_id ?? null;

            if ($roleId == 2) {
                // Student — SP returns only this student's grades
                $mode    = 12;
                $context = (int) $user->id;
            } elseif ($roleId == 1) {
                // Teacher — SP returns grades for sections they handle
                $mode    = 11;
                $context = (int) $user->id;
            } else {
                // Admin — SP returns all grades
                $mode    = 10;
                $context = 0;
            }

            $rows = collect(DB::select("CALL usp_get_data(?, ?)", [$mode, $context]));

            // Optional server-side filters (admin / teacher; student scope is already in SP)
            if ($request->filled('grade_level_id')) {
                $rows = $rows->filter(fn($r) => $r->grade_level_id == $request->grade_level_id);
            }
            if ($request->filled('section_id')) {
                $rows = $rows->filter(fn($r) => $r->section_id == $request->section_id);
            }
            if ($request->filled('subject_id')) {
                $rows = $rows->filter(fn($r) => $r->subject_id == $request->subject_id);
            }

            return response()->json(['status' => 'success', 'data' => $rows->values()]);

        } catch (\Exception $e) {
            Log::error("Grade List Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    // ── Insert grade — usp_sql_actions MODE 15 ────────────────────────────────
    // Students cannot reach this route (guarded in web.php).
    public function store(Request $request)
    {
        $request->validate([
            'student_id'     => 'required|integer',
            'subject_id'     => 'required|integer',
            'section_id'     => 'required|integer',
            'grade_level_id' => 'required|integer',
            'quarter'        => 'required|integer|in:1,2,3,4',
            'grade'          => 'required|numeric|min:60|max:100',
            'remarks'        => 'nullable|string|max:255',
        ]);

        try {
            $data = [
                'student_id'     => (int)   $request->student_id,
                'subject_id'     => (int)   $request->subject_id,
                'section_id'     => (int)   $request->section_id,
                'grade_level_id' => (int)   $request->grade_level_id,
                'quarter'        => (int)   $request->quarter,
                'grade'          => (float) $request->grade,
                'remarks'        => $request->remarks,
                'encoded_by'     => (int)   auth()->id(),
            ];
            DB::select("CALL usp_sql_actions(?, ?)", [15, json_encode($data)]);
            return response()->json(['status' => 'success', 'message' => 'Grade added successfully.']);
        } catch (\Exception $e) {
            Log::error("Grade Store Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }
    }

    // ── Update grade — usp_sql_actions MODE 16 ────────────────────────────────
    // Students cannot reach this route (guarded in web.php).
    public function update(Request $request)
    {
        $request->validate([
            'id'             => 'required|integer',
            'student_id'     => 'required|integer',
            'subject_id'     => 'required|integer',
            'section_id'     => 'required|integer',
            'grade_level_id' => 'required|integer',
            'quarter'        => 'required|integer|in:1,2,3,4',
            'grade'          => 'required|numeric|min:60|max:100',
            'remarks'        => 'nullable|string|max:255',
        ]);

        try {
            $data = [
                'id'             => (int)   $request->id,
                'student_id'     => (int)   $request->student_id,
                'subject_id'     => (int)   $request->subject_id,
                'section_id'     => (int)   $request->section_id,
                'grade_level_id' => (int)   $request->grade_level_id,
                'quarter'        => (int)   $request->quarter,
                'grade'          => (float) $request->grade,
                'remarks'        => $request->remarks,
                'updated_by'     => (int)   auth()->id(),
            ];
            DB::select("CALL usp_sql_actions(?, ?)", [16, json_encode($data)]);
            return response()->json(['status' => 'success', 'message' => 'Grade updated successfully.']);
        } catch (\Exception $e) {
            Log::error("Grade Update Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }
    }

    // ── Delete grade — usp_sql_actions MODE 17 ────────────────────────────────
    // Students cannot reach this route (guarded in web.php).
    public function destroy(Request $request)
    {
        $request->validate(['id' => 'required|integer']);

        try {
            DB::select("CALL usp_sql_actions(?, ?)", [17, json_encode(['id' => (int) $request->id])]);
            return response()->json(['status' => 'success', 'message' => 'Grade deleted successfully.']);
        } catch (\Exception $e) {
            Log::error("Grade Delete Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }
    }

    // ── Students by section — usp_populate_fields MODE 6 ─────────────────────
    // Populates the student dropdown in the Add/Edit modal.
    // MODE 6 is NEW — add it to usp_populate_fields (see grades_sp.sql).
    public function studentsBySection(Request $request)
    {
        $request->validate(['section_id' => 'required|integer']);

        try {
            $students = DB::select("CALL usp_populate_fields(?, ?)", [6, (int) $request->section_id]);
            return response()->json(['status' => 'success', 'data' => $students]);
        } catch (\Exception $e) {
            Log::error("Students By Section Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }
}