<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request)
    {
    
        return view('students.index');
    }

    public function create()
    {
        return view('students.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'last_name'      => 'required|string|max:80',
            'first_name'     => 'required|string|max:80',
            'middle_name'    => 'nullable|string|max:80',
            'dob'            => 'required|date',
            'sex'            => 'required|in:Male,Female',
            'address'        => 'required|string|max:255',
            'academic_year'  => 'required|string',
            'grade_level'    => 'required|string',
            'section'        => 'required|string',
            'student_type'   => 'required|string',
            'date_enrolled'  => 'required|date',
            'lrn'            => 'nullable|digits:12',
            'guardian_name'  => 'required|string|max:120',
            'relationship'   => 'required|string',
            'contact'        => 'required|string|max:20',
            'email'          => 'nullable|email|max:120',
        ]);


        return redirect()->route('students.index')
            ->with('success', "Student {$validated['first_name']} {$validated['last_name']} enrolled successfully.");
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
