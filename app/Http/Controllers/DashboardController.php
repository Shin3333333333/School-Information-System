<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user     = Auth::user();
        $roleName = $user->role ? strtolower($user->role->name) : null;

        return match($roleName) {
            'teacher' => redirect()->route('teacher.dashboard'),
            'student' => redirect()->route('student.dashboard'),
            'admin'   => $this->adminDashboard($roleName),
            default   => redirect()->route('login')->with('error', 'Invalid role.'),
        };
    }

    private function adminDashboard($roleName)
    {
        $totalStudents = DB::table('users')->where('role_id', 2)->where('status', 'Active')->count();
        $totalTeachers = DB::table('users')->where('role_id', 1)->where('status', 'Active')->count();
        $newThisMonth  = DB::table('users')->where('role_id', 2)->where('status', 'Active')
                            ->whereMonth('created_at', now()->month)
                            ->whereYear('created_at', now()->year)
                            ->count();

        try {
            $upcomingEvents = DB::select("CALL usp_get_data(?, ?)", [4, 0]);
        } catch (\Exception $e) {
            $upcomingEvents = [];
        }

        return view('dashboard', compact(
            'roleName',
            'totalStudents',
            'totalTeachers',
            'newThisMonth',
            'upcomingEvents'
        ));
}
}