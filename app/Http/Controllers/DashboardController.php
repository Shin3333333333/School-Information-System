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
        // ── Counts ────────────────────────────────────────────────────────────
        $totalStudents = DB::table('users')
                            ->where('role_id', 2)
                            ->where('status', 'Active')
                            ->count();

        $totalTeachers = DB::table('users')
                            ->where('role_id', 1)
                            ->where('status', 'Active')
                            ->count();

        $newThisMonth = DB::table('users')
                            ->where('role_id', 2)
                            ->where('status', 'Active')
                            ->whereMonth('created_at', now()->month)
                            ->whereYear('created_at', now()->year)
                            ->count();

        $newTeachersThisMonth = DB::table('users')
                            ->where('role_id', 1)
                            ->where('status', 'Active')
                            ->whereMonth('created_at', now()->month)
                            ->whereYear('created_at', now()->year)
                            ->count();

        $totalSections       = DB::table('section')->count();
        $totalAnnouncements  = DB::table('announcements')->count();
        $totalEvents         = DB::table('events')->count();
        $totalActivePolicies = DB::table('policies')->where('status', 'Active')->count();

        // ── Students per Grade Level ──────────────────────────────────────────
        try {
            $studentsPerGrade = DB::table('users as u')
                                    ->join('user_details as d', 'u.details_id', '=', 'd.id')
                                    ->join('grade_level as gl', 'd.grade_level_id', '=', 'gl.id')
                                    ->where('u.role_id', 2)
                                    ->where('u.status', 'Active')
                                    ->groupBy('gl.id', 'gl.grade_level_name')
                                    ->orderBy('gl.id')
                                    ->get([
                                        'gl.grade_level_name',
                                        DB::raw('COUNT(u.id) as total')
                                    ]);
        } catch (\Exception $e) {
            $studentsPerGrade = collect();
        }

        // ── Recent Announcements ──────────────────────────────────────────────
        try {
            $upcomingEvents = DB::select("CALL usp_get_data(?, ?)", [4, 0]);
        } catch (\Exception $e) {
            $upcomingEvents = [];
        }

        // ── Recent Users ──────────────────────────────────────────────────────
        try {
            $recentUsers = DB::table('users as u')
                                ->join('roles as r', 'u.role_id', '=', 'r.id')
                                ->orderBy('u.created_at', 'desc')
                                ->limit(5)
                                ->get([
                                    'u.name',
                                    'u.email',
                                    'r.name as role_name',
                                    'u.created_at',
                                    'u.status'
                                ]);

            $users = DB::table('users as u')
                        ->join('roles as r', 'u.role_id', '=', 'r.id')
                        ->select('u.id', 'u.name', 'u.email', 'r.name as role', 'u.status', 'u.created_at')
                        ->get();
        } catch (\Exception $e) {
            $recentUsers = collect();
            $users       = collect();
        }

        // ── Enrollment Trend (last 6 months) ──────────────────────────────────
        try {
            $enrollmentTrend = DB::table('users')
                                    ->where('role_id', 2)
                                    ->where('status', 'Active')
                                    ->where('created_at', '>=', now()->subMonths(6))
                                    ->selectRaw("DATE_FORMAT(created_at, '%b %Y') as month, DATE_FORMAT(created_at, '%Y-%m') as sort_key, COUNT(*) as total")
                                    ->groupBy('month', 'sort_key')
                                    ->orderBy('sort_key')
                                    ->get();
        } catch (\Exception $e) {
            $enrollmentTrend = collect();
        }

        return view('dashboard', compact(
            'roleName',
            'totalStudents',
            'totalTeachers',
            'newThisMonth',
            'newTeachersThisMonth',
            'totalSections',
            'totalAnnouncements',
            'totalEvents',
            'totalActivePolicies',
            'upcomingEvents',
            'studentsPerGrade',
            'recentUsers',
            'enrollmentTrend',
            'users'
        ));
    }
}