<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    // ── List all users — usp_get_data MODE 2 ─────────────────────────────────
    public function index(Request $request)
    {
        if ($request->ajax()) {
            try {
                $data = DB::select("CALL usp_get_data(?,?)", [2, null]);
                return response()->json(['status' => 'success', 'data' => $data]);
            } catch (\Exception $e) {
                Log::error("Student Portal Error: " . $e->getMessage());
                return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
            }
        }

        // Pass dropdown data for the edit modal
        $gradeLevels = DB::table('grade_level')->orderBy('id')->get();
        $sections    = DB::table('section as s')
                         ->join('grade_level as gl', 'gl.id', '=', 's.grade_level_id')
                         ->select('s.id', 's.section_name', 's.grade_level_id', 'gl.grade_level_name')
                         ->orderBy('gl.id')->orderBy('s.section_name')
                         ->get();

        return view('admin.index', compact('gradeLevels', 'sections'));
    }

    // ── Show create form ──────────────────────────────────────────────────────
    public function create()
    {
        return view('admin.create');
    }

    // ── Store new user — usp_sql_actions MODE 1 (unchanged) ───────────────────
    public function store(Request $request)
    {
        $type = $request->input('student_type');

        $rules = [
            'last_name'    => 'required|string|max:80',
            'first_name'   => 'required|string|max:80',
            'middle_name'  => 'nullable|string|max:80',
            'dob'          => 'required|date',
            'sex'          => 'required|in:Male,Female',
            'civil_status' => 'required|in:Single,Married',
            'address'      => 'required|string|max:255',
            'student_type' => 'required|integer|in:1,2',
            'contact'      => 'required|string|max:20',
            'email'        => 'nullable|email|max:120',
        ];

        if ($type == 2) {
            $rules += [
                'grade_level' => 'required|string',
                'section'     => 'required|string',
                'lrn'         => 'nullable|digits:12',
            ];
        }

        if ($type == 1) {
            $rules += [
                'employee_id'       => 'required|string|max:50',
                'department'        => 'required|string|max:100',
                'position'          => 'required|string|max:100',
                'specialization'    => 'nullable|string|max:100',
                'date_hired'        => 'nullable|date',
                'employment_status' => 'nullable|in:Permanent,Temporary,Contractual,Part-time',
            ];
        }

        $validated = $request->validate($rules);

        $label = $type == 1 ? 'Teacher' : 'Student';

        // Default password: LRN for students, employee_id for teachers
        $rawPassword = $type == 2
            ? ($validated['lrn'] ?? Str::random(10))
            : $validated['employee_id'];

        $validated['password'] = Hash::make($rawPassword);

        $spPayload = collect($validated)
            ->map(fn($v) => $v === '' ? null : $v)
            ->toArray();

        try {
            DB::statement("CALL usp_sql_actions(?, ?)", [1, json_encode($spPayload)]);
        } catch (\Exception $e) {
            Log::error("Store User Error: " . $e->getMessage());
            return response()->json(['message' => 'Database error: ' . $e->getMessage()], 500);
        }

        return response()->json([
            'message'            => "{$label} {$validated['first_name']} {$validated['last_name']} saved successfully.",
            'generated_password' => $rawPassword,
        ]);
    }

    // ── Fetch single user for edit modal ──────────────────────────────────────
    public function show(Request $request, $id)
    {
        // Return view for non-AJAX, JSON for AJAX
        if (!$request->ajax()) {
            return view('students.show', ['studentId' => $id]);
        }

        try {
            $user = DB::table('users')->where('id', $id)->first();
            if (!$user) {
                return response()->json(['status' => 'error', 'message' => 'User not found.'], 404);
            }

            $details = null;
            if ($user->role_id == 1) {
                // Teacher
                $details = DB::table('teacher_details')->where('id', $user->details_id)->first();
            } elseif ($user->role_id == 2) {
                // Student — also grab section/grade info
                $details = DB::table('user_details as ud')
                    ->leftJoin('section as s',      's.id',  '=', 'ud.section_id')
                    ->leftJoin('grade_level as gl', 'gl.id', '=', 's.grade_level_id')
                    ->where('ud.id', $user->details_id)
                    ->select('ud.*', 's.section_name', 'gl.grade_level_name', 'gl.id as grade_level_id')
                    ->first();
            }

            return response()->json([
                'status' => 'success',
                'data'   => ['user' => $user, 'details' => $details],
            ]);
        } catch (\Exception $e) {
            Log::error("Show User Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    // ── Update user — usp_user_management MODE 24 (student) / MODE 25 (teacher) ──
    public function update(Request $request, $id)
    {
        $type = $request->input('student_type');

        $rules = [
            'student_type' => 'required|integer|in:1,2',
            'last_name'    => 'required|string|max:80',
            'first_name'   => 'required|string|max:80',
            'middle_name'  => 'nullable|string|max:80',
            'dob'          => 'nullable|date',
            'sex'          => 'nullable|in:Male,Female',
            'civil_status' => 'nullable|in:Single,Married',
            'address'      => 'nullable|string|max:255',
            'contact'      => 'nullable|string|max:20',
            'email'        => 'nullable|email|max:120',
            'status'       => 'nullable|in:Active,Inactive',
        ];

        if ($type == 2) {
            $rules += [
                'lrn'         => 'nullable|digits:12',
                'grade_level' => 'nullable|integer|exists:grade_level,id',
                'section'     => 'nullable|integer|exists:section,id',
            ];
        }

        if ($type == 1) {
            $rules += [
                'employee_id'       => 'nullable|string|max:50',
                'department'        => 'nullable|string|max:100',
                'position'          => 'nullable|string|max:100',
                'specialization'    => 'nullable|string|max:100',
                'date_hired'        => 'nullable|date',
                'employment_status' => 'nullable|in:Permanent,Temporary,Contractual,Part-time',
            ];
        }

        try {
            $validated = $request->validate($rules);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => collect($e->errors())->flatten()->first(),
            ], 422);
        }

        // MODE 24 = update student, MODE 25 = update teacher
        $mode = $type == 1 ? 25 : 24;

        $spPayload = collect($validated)
            ->map(fn($v) => $v === '' ? null : $v)
            ->toArray();

        $spPayload['id'] = (int) $id;

        try {
            DB::statement("CALL usp_user_management(?, ?)", [$mode, json_encode($spPayload)]);

            return response()->json([
                'status'  => 'success',
                'message' => 'User updated successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error("Update User Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // ── Soft delete — usp_user_management MODE 26 ─────────────────────────────
    // Sets status = 'Inactive' — preserves grade/schedule/announcement history.
    public function destroy($id)
    {
        try {
            DB::statement("CALL usp_user_management(?, ?)", [26, json_encode(['id' => (int) $id])]);

            return response()->json([
                'status'  => 'success',
                'message' => 'User has been set to Inactive.',
            ]);
        } catch (\Exception $e) {
            Log::error("Delete User Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
public function hardDestroy($id)
{
    try {
        DB::statement("CALL usp_user_management(?, ?)", [27, json_encode(['id' => (int) $id])]);

        return response()->json([
            'status'  => 'success',
            'message' => 'User permanently deleted.',
        ]);
    } catch (\Exception $e) {
        Log::error("Hard Delete User Error: " . $e->getMessage());
        return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
    }
}
}