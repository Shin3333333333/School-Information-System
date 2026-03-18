<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use App\Models\AcademicYear;

class DashboardController extends Controller
{
    /**
     * Name of the schedule table in your database.
     */
    const SCHEDULE_TABLE = 'schedule';

    /**
     * Display the appropriate dashboard based on user role
     */
    public function index()
    {
        $user     = Auth::user();
        $roleName = $user->role ? strtolower($user->role->name) : null;

        // Get active academic year for all dashboards
        $activeAcademicYear = AcademicYear::active();

        return match($roleName) {
            'teacher' => view('dashboard', array_merge(
                [
                    'roleName' => $roleName,
                    'activeAcademicYear' => $activeAcademicYear,
                ],
                $this->teacherDashboardData($user->id)
            )),
            'student' => view('dashboard', array_merge(
                [
                    'roleName' => $roleName,
                    'activeAcademicYear' => $activeAcademicYear,
                ],
                $this->studentDashboardData($user->id)
            )),
            'admin'   => $this->adminDashboard($roleName, $activeAcademicYear),
            default   => redirect()->route('login')->with('error', 'Invalid role.'),
        };
    }

    /**
     * Gather all data needed for the teacher dashboard
     */
    private function teacherDashboardData($teacherId)
    {
        return [
            'teacherClasses'       => $this->getTeacherClasses($teacherId),
            'totalStudents'        => $this->getTeacherTotalStudents($teacherId),
            'recentAnnouncements'  => $this->getRecentAnnouncements(),
            'teacherWeeklyClasses' => $this->getTeacherWeeklyClasses($teacherId),
        ];
    }

    /**
     * Gather all data needed for the student dashboard
     */
    private function studentDashboardData($studentId)
    {
        return [
            'studentClasses'        => $this->getStudentClasses($studentId),
            'unreadAnnouncements'   => $this->getStudentUnreadAnnouncements($studentId),
            'recentAnnouncements'   => $this->getRecentAnnouncements(),
            'studentWeeklyClasses'  => $this->getStudentWeeklyClasses($studentId),
        ];
    }

    /**
     * Get classes assigned to a teacher
     */
    private function getTeacherClasses($teacherId)
    {
        try {
            return DB::table(self::SCHEDULE_TABLE . ' as sched')
                ->join('subject as sub', 'sched.subject_id', '=', 'sub.id')
                ->join('section as sec', 'sched.section_id', '=', 'sec.id')
                ->join('grade_level as gl', 'sched.grade_level_id', '=', 'gl.id')
                ->where('sched.user_id', $teacherId)
                ->select(
                    'sched.id',
                    'sub.subject_name as name',
                    'gl.grade_level_name as grade_level',
                    DB::raw("CONCAT(sched.day, ' ', TIME_FORMAT(sched.time_start, '%H:%i'), '-', TIME_FORMAT(sched.time_end, '%H:%i')) as schedule"),
                    DB::raw("(
                        SELECT COUNT(DISTINCT u.id)
                        FROM users u
                        JOIN user_details ud ON u.details_id = ud.id
                        WHERE u.role_id = 2
                          AND u.status = 'Active'
                          AND ud.section_id = sched.section_id
                    ) as student_count")
                )
                ->orderBy(DB::raw("FIELD(sched.day, 'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday')"))
                ->orderBy('sched.time_start')
                ->get();
        } catch (\Exception $e) {
            Log::error('Teacher classes query failed: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Get total number of students taught by a teacher (across all classes)
     */
    private function getTeacherTotalStudents($teacherId)
    {
        try {
            return DB::table(self::SCHEDULE_TABLE . ' as sched')
                ->join('section as sec', 'sched.section_id', '=', 'sec.id')
                ->join('user_details as ud', 'sec.id', '=', 'ud.section_id')
                ->join('users as u', function ($join) {
                    $join->on('u.details_id', '=', 'ud.id')
                         ->where('u.role_id', '=', 2)
                         ->where('u.status', '=', 'Active');
                })
                ->where('sched.user_id', $teacherId)
                ->distinct('u.id')
                ->count('u.id');
        } catch (\Exception $e) {
            Log::error('Teacher total students query failed: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get weekly class counts for a teacher (Mon, Tue, etc.)
     */
    private function getTeacherWeeklyClasses($teacherId)
    {
        try {
            return DB::table(self::SCHEDULE_TABLE)
                ->where('user_id', $teacherId)
                ->select('day', DB::raw('COUNT(*) as count'))
                ->groupBy('day')
                ->orderBy(DB::raw("FIELD(day, 'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday')"))
                ->pluck('count', 'day');
        } catch (\Exception $e) {
            Log::error('Teacher weekly classes query failed: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Get classes for a student (their schedule)
     */
    private function getStudentClasses($studentId)
    {
        try {
            // First get the student's section
            $sectionId = DB::table('users as u')
                ->join('user_details as ud', 'u.details_id', '=', 'ud.id')
                ->where('u.id', $studentId)
                ->where('u.role_id', 2)
                ->value('ud.section_id');

            if (!$sectionId) {
                return collect();
            }

            return DB::table(self::SCHEDULE_TABLE . ' as sched')
                ->join('subject as sub', 'sched.subject_id', '=', 'sub.id')
                ->join('users as t', 'sched.user_id', '=', 't.id')
                ->join('teacher_details as td', 't.details_id', '=', 'td.id')
                ->where('sched.section_id', $sectionId)
                ->select(
                    'sub.subject_name',
                    DB::raw("CONCAT(td.fname, ' ', td.lname) as teacher_name"),
                    'sched.day',
                    'sched.time_start',
                    'sched.time_end',
                    'sched.room'
                )
                ->orderBy(DB::raw("FIELD(sched.day, 'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday')"))
                ->orderBy('sched.time_start')
                ->get();
        } catch (\Exception $e) {
            Log::error('Student classes query failed: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Get unread announcements count for a student
     */
    private function getStudentUnreadAnnouncements($studentId)
    {
        try {
            if (!Schema::hasTable('announcement_reads')) {
                return 0;
            }
            return DB::table('announcement_reads')
                ->where('user_id', $studentId)
                ->where('is_read', false)
                ->count();
        } catch (\Exception $e) {
            Log::error('Unread announcements query failed: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get weekly class counts for a student
     */
    private function getStudentWeeklyClasses($studentId)
    {
        try {
            $sectionId = DB::table('users as u')
                ->join('user_details as ud', 'u.details_id', '=', 'ud.id')
                ->where('u.id', $studentId)
                ->where('u.role_id', 2)
                ->value('ud.section_id');

            if (!$sectionId) {
                return collect();
            }

            return DB::table(self::SCHEDULE_TABLE)
                ->where('section_id', $sectionId)
                ->select('day', DB::raw('COUNT(*) as count'))
                ->groupBy('day')
                ->orderBy(DB::raw("FIELD(day, 'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday')"))
                ->pluck('count', 'day');
        } catch (\Exception $e) {
            Log::error('Student weekly classes query failed: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Get recent announcements (shared by all roles)
     */
    private function getRecentAnnouncements()
    {
        try {
            // Try stored procedure first (your existing implementation)
            return DB::select("CALL usp_get_data(?, ?)", [4, 0]);
        } catch (\Exception $e) {
            Log::error('Stored procedure failed, falling back to direct query: ' . $e->getMessage());
            try {
                return DB::table('announcements as a')
                    ->select(
                        'a.id',
                        'a.title',
                        'a.date_posted',
                        DB::raw("GROUP_CONCAT(DISTINCT s.subject_name SEPARATOR ', ') as subject_name"),
                        DB::raw("GROUP_CONCAT(DISTINCT sec.section_name SEPARATOR ', ') as section_names")
                    )
                    ->leftJoin('announcement_subjects as asub', 'a.id', '=', 'asub.announcement_id')
                    ->leftJoin('subject as s', 'asub.subject_id', '=', 's.id')
                    ->leftJoin('announcement_sections as asec', 'a.id', '=', 'asec.announcement_id')
                    ->leftJoin('section as sec', 'asec.section_id', '=', 'sec.id')
                    ->orderBy('a.date_posted', 'desc')
                    ->limit(5)
                    ->groupBy('a.id')
                    ->get();
            } catch (\Exception $e) {
                Log::error('Recent announcements query failed: ' . $e->getMessage());
                return [];
            }
        }
    }

    // ==================== ADMIN METHODS (unchanged) ====================

    private function adminDashboard($roleName, $activeAcademicYear = null)
    {
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

        $totalSections       = DB::table('section')->count();
        $totalAnnouncements  = DB::table('announcements')->count();
        $totalEvents         = DB::table('events')->count();
        $totalActivePolicies = DB::table('policies')->where('status', 'Active')->count();

        $studentsPerGrade = $this->getStudentsPerGrade();
        $enrollmentTrend  = $this->getEnrollmentTrend();
        $upcomingEvents   = $this->getRecentAnnouncements();
        $recentUsers      = $this->getRecentUsers();

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

    private function getStudentsPerGrade()
    {
        try {
            return DB::table('users as u')
                ->join('user_details as d', 'u.details_id', '=', 'd.id')
                ->join('grade_level as gl', 'd.grade_level_id', '=', 'gl.id')
                ->where('u.role_id', 2)
                ->where('u.status', 'Active')
                ->groupBy('gl.id', 'gl.grade_level_name')
                ->orderBy('gl.id')
                ->get(['gl.grade_level_name', DB::raw('COUNT(u.id) as total')]);
        } catch (\Exception $e) {
            Log::error('Students per grade query failed: ' . $e->getMessage());
            return collect();
        }
    }

    private function getEnrollmentTrend()
    {
        try {
            return DB::table('users')
                ->where('role_id', 2)
                ->where('status', 'Active')
                ->where('created_at', '>=', now()->subMonths(6))
                ->selectRaw("DATE_FORMAT(created_at, '%b') as month, COUNT(*) as total")
                ->groupBy(DB::raw("DATE_FORMAT(created_at, '%b %Y')"))
                ->orderBy('created_at')
                ->get();
        } catch (\Exception $e) {
            Log::error('Enrollment trend query failed: ' . $e->getMessage());
            return collect();
        }
    }

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
            Log::error('Recent users query failed: ' . $e->getMessage());
            return collect();
        }
    }

    // ==================== OTHER PUBLIC METHODS (unchanged) ====================

    public function getAllUsers()
    {
        try {
            return DB::table('users as u')
                ->join('roles as r', 'u.role_id', '=', 'r.id')
                ->select('u.id', 'u.name', 'u.email', 'r.name as role', 'u.status', 'u.created_at')
                ->get();
        } catch (\Exception $e) {
            Log::error('Get all users query failed: ' . $e->getMessage());
            return collect();
        }
    }

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
                'total' => DB::table('users')->where('role_id', 2)->where('status', 'Active')->count(),
                'new_this_month' => DB::table('users')->where('role_id', 2)->where('status', 'Active')
                    ->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
            ],
            'teachers' => [
                'total' => DB::table('users')->where('role_id', 1)->where('status', 'Active')->count(),
                'new_this_month' => DB::table('users')->where('role_id', 1)->where('status', 'Active')
                    ->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
            ],
            'sections' => ['total' => DB::table('section')->count()],
            'announcements' => ['total' => DB::table('announcements')->count()],
            'events' => ['total' => DB::table('events')->count()],
            'policies' => ['active' => DB::table('policies')->where('status', 'Active')->count()],
        ];

        return $stats;
    }

    public function getStudentsByGrade($gradeId = null)
    {
        try {
            $query = DB::table('users as u')
                ->join('user_details as d', 'u.details_id', '=', 'd.id')
                ->join('grade_level as gl', 'd.grade_level_id', '=', 'gl.id')
                ->where('u.role_id', 2)
                ->where('u.status', 'Active')
                ->select('u.id', 'u.name', 'u.email', 'gl.grade_level_name', 'u.created_at');

            if ($gradeId) {
                $query->where('gl.id', $gradeId);
            }

            return $query->orderBy('gl.id')->orderBy('u.name')->get();
        } catch (\Exception $e) {
            Log::error('Get students by grade query failed: ' . $e->getMessage());
            return collect();
        }
    }

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
            Log::error('Get enrollment by month query failed: ' . $e->getMessage());
            return collect();
        }
    }

    public function getActiveAcademicYear()
    {
        return AcademicYear::active();
    }
}