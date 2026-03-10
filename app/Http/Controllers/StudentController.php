<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            try {
                $data = DB::select("CALL usp_get_data(?,?)", [2, null]);

                return response()->json([
                    'status' => 'success',
                    'data'   => $data
                ]);

            } catch (\Exception $e) {
                Log::error("Student Portal Error: " . $e->getMessage());

                return response()->json([
                    'status'  => 'error',
                    'message' => $e->getMessage()
                ], 400);
            }
        }

        return view('students.index');
    }

    public function create()
    {
        return view('students.create');
    }

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

        // Generate default password
        // Students: LRN as password, fallback to random if LRN is null
        // Teachers: employee_id as password
        $rawPassword = $type == 2
            ? ($validated['lrn'] ?? Str::random(10))
            : $validated['employee_id'];

        $validated['password'] = Hash::make($rawPassword);

        // Strip raw_password, convert empty strings to null
        // so JSON null values arrive as SQL NULL in the SP
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

    public function show($id)
    {
        return view('students.show', ['studentId' => $id]);
    }

    public function edit($id)
    {
        return view('students.edit', ['studentId' => $id]);
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('students.index')->with('success', 'Student record updated.');
    }

    public function destroy($id)
    {
        return redirect()->route('students.index')->with('success', 'Student archived.');
    }
}