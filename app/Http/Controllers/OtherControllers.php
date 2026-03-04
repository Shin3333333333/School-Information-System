<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FeeController extends Controller
{
    public function index()
    {
        return view('accounts.fees');
    }

    public function create()
    {
        return view('accounts.fees-create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id'   => 'required',
            'amount'       => 'required|numeric|min:1',
            'payment_mode' => 'required|string',
            'date_paid'    => 'required|date',
            'fee_type'     => 'required|string',
        ]);
        // Payment::create($validated);
        return redirect()->route('fees.index')->with('success', 'Payment recorded.');
    }
}

// ─────────────────────────────────────────────────────────────────────────────

namespace App\Http\Controllers;

class EnrollmentController extends Controller
{
    public function index()
    {
        return view('enrollment.index');
    }

    public function show($id)
    {
        return view('enrollment.show', ['id' => $id]);
    }
}

// ─────────────────────────────────────────────────────────────────────────────

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GradeController extends Controller
{
    public function index()
    {
        return view('grades.index');
    }

    public function edit($id)
    {
        return view('grades.edit', ['id' => $id]);
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('grades.index')->with('success', 'Grades updated.');
    }
}

// ─────────────────────────────────────────────────────────────────────────────

namespace App\Http\Controllers;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.index');
    }
}
