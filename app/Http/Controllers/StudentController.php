<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Log; 
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

    // Common rules for both types
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

    // Student-specific rules
    if ($type == 2) {
        $rules += [
            'grade_level' => 'required|string',
            'section'     => 'required|string',
            'lrn'         => 'nullable|digits:12',
        ];
    }

    // Teacher-specific rules
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

    try {
        DB::statement("CALL usp_sql_actions(?, ?)", [1, json_encode($validated)]);
    } catch (\Exception $e) {
        Log::error("Store User Error: " . $e->getMessage());
        return response()->json(['message' => 'Database error: ' . $e->getMessage()], 500);
    }

    return response()->json([
        'message' => "{$label} {$validated['first_name']} {$validated['last_name']} saved successfully."
    ]);
}
    public function show($id)
    {
        // $student = Student::findOrFail($id);
        return view('students.show', ['studentId' => $id]);
    }

    /* edit form */
    public function edit($id)
    {
        // $student = Student::findOrFail($id);
        return view('students.edit', ['studentId' => $id]);
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('students.index')->with('success', 'Student record updated.');
    }

    /* soft-delete / archive a student */
    public function destroy($id)
    {

        return redirect()->route('students.index')->with('success', 'Student archived.');
    }
}
