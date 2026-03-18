<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\AcademicYear;

class DashboardController extends Controller
{
    /**
     * Display the appropriate dashboard based on user role
     * 
     * Routes to:
     * - Admin: adminDashboard()
     * - Teacher: teacher.dashboard view
     * - Student: student.dashboard view
     */
    public function index()
    {
        $user     = Auth::user();
        $roleName = $user->role ? strtolower($user->role->name) : null;

        // Get active academic year for all dashboards
        $activeAcademicYear = AcademicYear::active();

        return match($roleName) {
            'teacher' => view('dashboard', [
                'roleName' => $roleName,
                'activeAcademicYear' => $activeAcademicYear,
            ]),
            'student' => view('dashboard', [
                'roleName' => $roleName,
                'activeAcademicYear' => $activeAcademicYear,
            ]),
            'admin'   => $this->adminDashboard($roleName, $activeAcademicYear),
            default   => redirect()->route('login')->with('error', 'Invalid role.'),
        };
    }

    /**
     * Generate the admin dashboard with all metrics and charts
     * 
     * @param string $roleName
     * @param AcademicYear|null $activeAcademicYear
     * @return \Illuminate\View\View
     */
    private function adminDashboard($roleName, $activeAcademicYear = null)
    {
        // ════════════════════════════════════════════════════════════════════════
        // STUDENT COUNTS
        // ════════════════════════════════════════════════════════════════════════
        
        $totalStudents = DB::table('users')
                            ->where('role_id', 2)
                            ->where('status', 'Active')
                            ->count();

        $newThisMonth = DB::table('users')
                            ->where('role_id', 2)
                            ->where('status', 'Active')
                            ->whereMonth('created_at', now()->month)
                            ->whereYear('created_at', now()->year)
                            ->count();

        // ════════════════════════════════════════════════════════════════════════
        // TEACHER COUNTS
        // ════════════════════════════════════════════════════════════════════════
        
        $totalTeachers = DB::table('users')
                            ->where('role_id', 1)
                            ->where('status', 'Active')
                            ->count();

        $newTeachersThisMonth = DB::table('users')
                            ->where('role_id', 1)
                            ->where('status', 'Active')
                            ->whereMonth('created_at', now()->month)
                            ->whereYear('created_at', now()->year)
                            ->count();

        // ════════════════════════════════════════════════════════════════════════
        // OTHER COUNTS
        // ════════════════════════════════════════════════════════════════════════
        
        $totalSections       = DB::table('section')->count();
        $totalAnnouncements  = DB::table('announcements')->count();
        $totalEvents         = DB::table('events')->count();
        $totalActivePolicies = DB::table('policies')->where('status', 'Active')->count();

        // ════════════════════════════════════════════════════════════════════════
        // STUDENTS PER GRADE LEVEL (FOR BAR CHART)
        // ════════════════════════════════════════════════════════════════════════
        
        $studentsPerGrade = $this->getStudentsPerGrade();

        // ════════════════════════════════════════════════════════════════════════
        // ENROLLMENT TREND (FOR LINE CHART - LAST 6 MONTHS)
        // ════════════════════════════════════════════════════════════════════════
        
        $enrollmentTrend = $this->getEnrollmentTrend();

        // ════════════════════════════════════════════════════════════════════════
        // RECENT ANNOUNCEMENTS
        // ════════════════════════════════════════════════════════════════════════
        
        $upcomingEvents = $this->getRecentAnnouncements();

        // ════════════════════════════════════════════════════════════════════════
        // RECENTLY ADDED USERS
        // ════════════════════════════════════════════════════════════════════════
        
        $recentUsers = $this->getRecentUsers();

        // ════════════════════════════════════════════════════════════════════════
        // RETURN VIEW WITH ALL DATA
        // ════════════════════════════════════════════════════════════════════════
        
        return view('dashboard', compact(
            'roleName',
            'activeAcademicYear',
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
            'enrollmentTrend'
        ));
    }

    /**
     * Get the number of students per grade level
     * Used for bar chart on admin dashboard
     * 
     * @return \Illuminate\Support\Collection
     */
    private function getStudentsPerGrade()
    {
        try {
            return DB::table('users as u')
                        ->join('user_details as d', 'u.details_id', '=', 'd.id')
                        ->join('grade_level as gl', 'd.grade_level_id', '=', 'gl.id')
                        ->where('u.role_id', 2) // Student role
                        ->where('u.status', 'Active')
                        ->groupBy('gl.id', 'gl.grade_level_name')
                        ->orderBy('gl.id')
                        ->get([
                            'gl.grade_level_name',
                            DB::raw('COUNT(u.id) as total')
                        ]);
        } catch (\Exception $e) {
            // Return empty collection if query fails
            return collect();
        }
    }

    /**
     * Get enrollment trend for the last 6 months
     * Used for line chart on admin dashboard
     * 
     * @return \Illuminate\Support\Collection
     */
    private function getEnrollmentTrend()
    {
        try {
            return DB::table('users')
                        ->where('role_id', 2) // Students only
                        ->where('status', 'Active')
                        ->where('created_at', '>=', now()->subMonths(6))
                        ->selectRaw("DATE_FORMAT(created_at, '%b') as month, COUNT(*) as total")
                        ->groupBy(DB::raw("DATE_FORMAT(created_at, '%b %Y')"))
                        ->orderBy('created_at')
                        ->get();
        } catch (\Exception $e) {
            // Return empty collection if query fails
            return collect();
        }
    }

    /**
     * Get recent announcements
     * Used in Recent Announcements widget
     * 
     * @return array
     */
    private function getRecentAnnouncements()
    {
        try {
            // Try stored procedure first (your existing implementation)
            return DB::select("CALL usp_get_data(?, ?)", [4, 0]);
        } catch (\Exception $e) {
            // Fallback to direct query if stored procedure fails
            try {
                return DB::table('announcements as a')
                            ->select(
                                'a.id',
                                'a.title',
                                'a.date_posted',
                                DB::raw("GROUP_CONCAT(DISTINCT s.name SEPARATOR ', ') as subject_name"),
                                DB::raw("GROUP_CONCAT(DISTINCT sec.name SEPARATOR ', ') as section_names")
                            )
                            ->leftJoin('announcement_subjects as asub', 'a.id', '=', 'asub.announcement_id')
                            ->leftJoin('subjects as s', 'asub.subject_id', '=', 's.id')
                            ->leftJoin('announcement_sections as asec', 'a.id', '=', 'asec.announcement_id')
                            ->leftJoin('sections as sec', 'asec.section_id', '=', 'sec.id')
                            ->orderBy('a.date_posted', 'desc')
                            ->limit(5)
                            ->groupBy('a.id')
                            ->get();
            } catch (\Exception $e) {
                // Return empty array if both fail
                return [];
            }
        }
    }

    /**
     * Get recently added users
     * Used in Recently Added Users widget
     * Shows last 5 users by creation date
     * 
     * @return \Illuminate\Support\Collection
     */
    private function getRecentUsers()
    {
        try {
            return DB::table('users as u')
                        ->join('roles as r', 'u.role_id', '=', 'r.id')
                        ->orderBy('u.created_at', 'desc')
                        ->limit(5)
                        ->get([
                            'u.id',
                            'u.name',
                            'u.email',
                            'r.name as role_name',
                            'u.created_at',
                            'u.status'
                        ]);
        } catch (\Exception $e) {
            // Return empty collection if query fails
            return collect();
        }
    }

    /**
     * Get all users (for admin reference)
     * Can be used for admin user management
     * 
     * @return \Illuminate\Support\Collection
     */
    public function getAllUsers()
    {
        try {
            return DB::table('users as u')
                        ->join('roles as r', 'u.role_id', '=', 'r.id')
                        ->select(
                            'u.id',
                            'u.name',
                            'u.email',
                            'r.name as role',
                            'u.status',
                            'u.created_at'
                        )
                        ->get();
        } catch (\Exception $e) {
            // Return empty collection if query fails
            return collect();
        }
    }

    /**
     * Get dashboard statistics
     * Can be used for API endpoints or export
     * 
     * @return array
     */
    public function getStatistics()
    {
        $activeAcademicYear = AcademicYear::active();

        $stats = [
            'academic_year' => $activeAcademicYear ? [
                'id' => $activeAcademicYear->id,
                'label' => $activeAcademicYear->year_label,
                'is_active' => $activeAcademicYear->is_active,
            ] : null,
            'students' => [
                'total' => DB::table('users')
                            ->where('role_id', 2)
                            ->where('status', 'Active')
                            ->count(),
                'new_this_month' => DB::table('users')
                            ->where('role_id', 2)
                            ->where('status', 'Active')
                            ->whereMonth('created_at', now()->month)
                            ->whereYear('created_at', now()->year)
                            ->count(),
            ],
            'teachers' => [
                'total' => DB::table('users')
                            ->where('role_id', 1)
                            ->where('status', 'Active')
                            ->count(),
                'new_this_month' => DB::table('users')
                            ->where('role_id', 1)
                            ->where('status', 'Active')
                            ->whereMonth('created_at', now()->month)
                            ->whereYear('created_at', now()->year)
                            ->count(),
            ],
            'sections' => [
                'total' => DB::table('section')->count(),
            ],
            'announcements' => [
                'total' => DB::table('announcements')->count(),
            ],
            'events' => [
                'total' => DB::table('events')->count(),
            ],
            'policies' => [
                'active' => DB::table('policies')->where('status', 'Active')->count(),
            ],
        ];

        return $stats;
    }

    /**
     * Get students by grade for reporting
     * Useful for generating reports
     * 
     * @param int|null $gradeId
     * @return \Illuminate\Support\Collection
     */
    public function getStudentsByGrade($gradeId = null)
    {
        $query = DB::table('users as u')
                    ->join('user_details as d', 'u.details_id', '=', 'd.id')
                    ->join('grade_level as gl', 'd.grade_level_id', '=', 'gl.id')
                    ->where('u.role_id', 2)
                    ->where('u.status', 'Active')
                    ->select(
                        'u.id',
                        'u.name',
                        'u.email',
                        'gl.grade_level_name',
                        'u.created_at'
                    );

        if ($gradeId) {
            $query->where('gl.id', $gradeId);
        }

        return $query->orderBy('gl.id')->orderBy('u.name')->get();
    }

    /**
     * Get enrollment data by month
     * Useful for trend analysis
     * 
     * @param int $months
     * @return \Illuminate\Support\Collection
     */
    public function getEnrollmentByMonth($months = 12)
    {
        try {
            return DB::table('users')
                        ->where('role_id', 2)
                        ->where('status', 'Active')
                        ->where('created_at', '>=', now()->subMonths($months))
                        ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as total")
                        ->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m')"))
                        ->orderBy('month')
                        ->get();
        } catch (\Exception $e) {
            return collect();
        }
    }

    /**
     * Get active academic year
     * Can be used in other controllers
     * 
     * @return AcademicYear|null
     */
    public function getActiveAcademicYear()
    {
        return AcademicYear::active();
    }
}