<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\FeeController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\PoliciesController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\PopulateFieldsController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\ClassListController;

// =============================================================================
// PUBLIC ROUTES
// =============================================================================

Route::get('/', fn() => redirect()->route('login'));
Route::get('login',  [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login'])->name('login.submit');
Route::post('logout',[LoginController::class, 'logout'])->name('logout');

// =============================================================================
// AUTHENTICATED ROUTES
// =============================================================================

Route::middleware('auth')->group(function () {

    // ── Dashboard ─────────────────────────────────────────────────────────────
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ── Resources ─────────────────────────────────────────────────────────────
    Route::resource('students',   StudentController::class);
    Route::resource('enrollment', EnrollmentController::class)->only(['index','show']);
    Route::resource('fees',       FeeController::class)->only(['index','create','store']);

    // Admin index — flat route, avoids duplicate prefix group conflict
    Route::get('admin', [AdminController::class, 'index'])->name('admin.index');

    // ── Populate Fields ───────────────────────────────────────────────────────
    Route::get('fields/subjects',              [PopulateFieldsController::class, 'getSubjects'])->name('fields.subjects');
    Route::get('fields/sections',              [PopulateFieldsController::class, 'getSections'])->name('fields.sections');
    Route::get('fields/sections/{gradeLevel}', [PopulateFieldsController::class, 'getSectionsByGrade'])->name('fields.sections.byGrade');
    Route::get('fields/grade-levels',          [PopulateFieldsController::class, 'getGradeLevels'])->name('fields.gradeLevels');
    Route::get('fields/students-per-grade',    [PopulateFieldsController::class, 'getStudentsPerGrade'])->name('fields.studentsPerGrade');

    // ── Chat ──────────────────────────────────────────────────────────────────
    Route::post('chat/send',  [ChatController::class, 'send'])->name('chat.send');
    Route::post('chat/clear', [ChatController::class, 'clearHistory'])->name('chat.clear');

    // ── Academic Years ────────────────────────────────────────────────────────
    Route::get('academic-years',             [AcademicYearController::class, 'index'])->name('academic-years.index');
    Route::post('academic-years/set-active', [AcademicYearController::class, 'setActive'])->name('academic-years.setActive');
    Route::post('academic-years/store',      [AcademicYearController::class, 'store'])->name('academic-years.store');
    Route::post('academic-years/destroy',    [AcademicYearController::class, 'destroy'])->name('academic-years.destroy');

    // ── Policies ──────────────────────────────────────────────────────────────
    Route::get('admin/policies',              [PoliciesController::class, 'index'])->name('admin.policies');
    Route::get('admin/policies/list',         [PoliciesController::class, 'list'])->name('admin.policies.list');
    Route::get('admin/policies/info',         [PoliciesController::class, 'getInfo'])->name('admin.policies.info');
    Route::post('admin/policies/info/update', [PoliciesController::class, 'updateInfo'])->name('admin.policies.info.update');
    Route::post('admin/policies/store',       [PoliciesController::class, 'store'])->name('admin.policies.store');
    Route::post('admin/policies/update',      [PoliciesController::class, 'update'])->name('admin.policies.update');
    Route::post('admin/policies/destroy',     [PoliciesController::class, 'destroy'])->name('admin.policies.destroy');
    Route::get('policies/list',               [PoliciesController::class, 'list'])->name('policies.list');
    Route::get('policies/info',               [PoliciesController::class, 'getInfo'])->name('policies.info');

    // ── Sections ──────────────────────────────────────────────────────────────
    Route::get('admin/sections',          [SectionController::class, 'index'])->name('admin.sections');
    Route::get('admin/sections/list',     [SectionController::class, 'list'])->name('admin.sections.list');
    Route::post('admin/sections/store',   [SectionController::class, 'store'])->name('admin.sections.store');
    Route::post('admin/sections/update',  [SectionController::class, 'update'])->name('admin.sections.update');
    Route::post('admin/sections/destroy', [SectionController::class, 'destroy'])->name('admin.sections.destroy');

    // ── Subjects ──────────────────────────────────────────────────────────────
    Route::get('admin/subjects',          [SubjectController::class, 'index'])->name('admin.subjects');
    Route::get('admin/subjects/list',     [SubjectController::class, 'list'])->name('admin.subjects.list');
    Route::post('admin/subjects/store',   [SubjectController::class, 'store'])->name('admin.subjects.store');
    Route::post('admin/subjects/update',  [SubjectController::class, 'update'])->name('admin.subjects.update');
    Route::post('admin/subjects/destroy', [SubjectController::class, 'destroy'])->name('admin.subjects.destroy');

    // ── Schedule ──────────────────────────────────────────────────────────────
    // All three roles share ScheduleController@index (passes $role to the unified blade).
    // Flat routes — no nested prefix groups to avoid duplicate admin.index conflict.
    Route::get('admin/schedule',          [ScheduleController::class, 'index'])->name('admin.schedule');
    Route::get('admin/schedule/list',     [ScheduleController::class, 'list'])->name('admin.schedule.list');
    Route::post('admin/schedule/store',   [ScheduleController::class, 'store'])->name('admin.schedule.store');
    Route::post('admin/schedule/update',  [ScheduleController::class, 'update'])->name('admin.schedule.update');
    Route::post('admin/schedule/destroy', [ScheduleController::class, 'destroy'])->name('admin.schedule.destroy');

    Route::get('teacher/schedule',        [ScheduleController::class, 'index'])->name('teacher.schedule');
    Route::get('teacher/schedule/list',   [ScheduleController::class, 'teacherList'])->name('teacher.schedule.list');

    Route::get('student/schedule',        [ScheduleController::class, 'index'])->name('student.schedule');
    Route::get('student/schedule/list',   [ScheduleController::class, 'studentList'])->name('student.schedule.list');

    // ── Grades ────────────────────────────────────────────────────────────────
    Route::get('admin/grades',                     [GradeController::class, 'index'])->name('admin.grades');
    Route::get('admin/grades/list',                [GradeController::class, 'list'])->name('admin.grades.list');
    Route::post('admin/grades/store',              [GradeController::class, 'store'])->name('admin.grades.store');
    Route::post('admin/grades/update',             [GradeController::class, 'update'])->name('admin.grades.update');
    Route::post('admin/grades/destroy',            [GradeController::class, 'destroy'])->name('admin.grades.destroy');
    Route::get('admin/grades/students-by-section', [GradeController::class, 'studentsBySection'])->name('admin.grades.studentsBySection');
    Route::get('grades',                           [GradeController::class, 'index'])->name('grades.index');
    Route::get('grades/list',                      [GradeController::class, 'list'])->name('grades.list');
    Route::get('grades/students-by-section',       [GradeController::class, 'studentsBySection'])->name('grades.studentsBySection');
    Route::post('grades/store',                    [GradeController::class, 'store'])->name('grades.store');
    Route::post('grades/update',                   [GradeController::class, 'update'])->name('grades.update');
    Route::post('grades/destroy',                  [GradeController::class, 'destroy'])->name('grades.destroy');

    // ── Announcements ─────────────────────────────────────────────────────────
    // Static sub-paths (all, list) defined BEFORE the {id} wildcard
    Route::get('admin/announcements',         [AnnouncementController::class, 'adminIndex'])->name('admin.announcements');
    Route::get('announcements/all',           [AnnouncementController::class, 'getAllAnnouncements'])->name('announcements.all');
    Route::get('announcements/list',          [AnnouncementController::class, 'getAnnouncements'])->name('announcements.list');
    Route::get('announcements',               [AnnouncementController::class, 'index'])->name('announcements.index');
    Route::post('announcements/store',        [AnnouncementController::class, 'store'])->name('announcements.store');
    Route::post('announcements/update',       [AnnouncementController::class, 'update'])->name('announcements.update');
    Route::post('announcements/destroy',      [AnnouncementController::class, 'destroy'])->name('announcements.destroy');
    Route::get('announcements/{id}/sections', [AnnouncementController::class, 'getSections'])
        ->where('id', '[0-9]+')
        ->name('announcements.sections');
    Route::get('announcements/{id}', [AnnouncementController::class, 'show'])->name('announcements.show');
    // ── Calendar ──────────────────────────────────────────────────────────────
    // All three use CalendarController@index which passes $role to the blade
    Route::get('admin/calendar',       [CalendarController::class, 'index'])->name('admin.calendar');
    Route::get('teacher/calendar',     [CalendarController::class, 'index'])->name('teacher.calendar');
    Route::get('student/calendar',     [CalendarController::class, 'index'])->name('student.calendar');
    Route::get('calendar/list',        [CalendarController::class, 'list'])->name('calendar.list');
    Route::post('calendar/store',      [CalendarController::class, 'store'])->name('calendar.store');
    Route::post('calendar/update',     [CalendarController::class, 'update'])->name('calendar.update');
    Route::post('calendar/destroy',    [CalendarController::class, 'destroy'])->name('calendar.destroy');

    // ── Class List (teacher) ──────────────────────────────────────────────────
    Route::get('teacher/class-list',          [ClassListController::class, 'index'])->name('teacher.class-list');
    Route::get('teacher/class-list/schedule', [ClassListController::class, 'schedule'])->name('teacher.class-list.schedule');
    Route::get('teacher/class-list/students', [ClassListController::class, 'students'])->name('teacher.class-list.students');

    // ── Teacher nav pages ─────────────────────────────────────────────────────
    Route::get('teacher/dashboard',     fn() => view('dashboard'))->name('teacher.dashboard');
    Route::get('teacher/announcements', fn() => view('announcements'))->name('teacher.announcements');
    Route::get('teacher/policies',      fn() => view('policies'))->name('teacher.policies');

    // ── Student nav pages ─────────────────────────────────────────────────────
    Route::get('student/dashboard',     fn() => view('dashboard'))->name('student.dashboard');
    Route::get('student/announcements', fn() => view('announcements'))->name('student.announcements');
    Route::get('student/policies',      fn() => view('policies'))->name('student.policies');


    // ── Profile (all roles) ───────────────────────────────────────────────────
    Route::get('profile',          [App\Http\Controllers\ProfileController::class, 'index'])->name('profile.index');
    Route::post('profile/update',  [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::post('profile/password',[App\Http\Controllers\ProfileController::class, 'changePassword'])->name('profile.password');
Route::delete('students/hard-delete/{id}', [StudentController::class, 'hardDestroy'])->name('students.hard-delete');
Route::get('announcements/{id}', [AnnouncementController::class, 'show'])->name('announcements.show');
});