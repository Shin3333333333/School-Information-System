<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $roleName = $user->role ? strtolower($user->role->name) : null;

        return match($roleName) {
            'teacher' => redirect()->route('teacher.dashboard'),
            'student' => redirect()->route('student.dashboard'),
            'admin'   => view('dashboard', ['userRole' => $roleName]),
            default   => redirect()->route('login')->with('error', 'Invalid role.'),
        };
    }
}