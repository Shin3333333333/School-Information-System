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
    $validated = $request->validate([
        'last_name' => 'required|string|max:80',
        'first_name' => 'required|string|max:80',
        'middle_name' => 'nullable|string|max:80',
        'dob' => 'required|date',
        'sex' => 'required|in:Male,Female',
        'civil_status' => 'required|in:Single,Married',
        'address' => 'required|string|max:255',
        'grade_level' => 'required|string',
        'section' => 'required|string',
        'student_type' => 'required|integer',
        'lrn' => 'nullable|digits:12',
        'contact' => 'required|string|max:20',
        'email' => 'nullable|email|max:120',
    ]);

    try {
        DB::statement("CALL usp_sql_actions(?, ?)", [1, json_encode($validated)]);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Database error: ' . $e->getMessage()], 500);
    }

    return response()->json([
        'message' => "Student {$validated['first_name']} {$validated['last_name']} enrolled successfully."
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
