<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

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
        $userRole = session('user_role');

        switch ($userRole) {
            case 'Admin':
                return view('dashboard', ['userRole' => $userRole]);
            case 'Teacher':
                return view('teacher.dashboard', ['userRole' => $userRole]);
            default:
                Auth::logout();
                request()->session()->invalidate();
                request()->session()->regenerateToken();
                return redirect()->route('login')->with('error', 'Invalid role. Please log in again.');
        }
    }
}