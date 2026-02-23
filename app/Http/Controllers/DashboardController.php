<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    public function index()
    {
        // $stats = [
        //     'total_students' => Student::count(),
        //     'active_students' => Student::where('status', 'active')->count(),
        //     'pending_fees' => Payment::where('status', 'unpaid')->count(),
        //     'new_this_month' => Student::whereMonth('created_at', now()->month)->count(),
        // ];
        return view('dashboard');
    }
}
