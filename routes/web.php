<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\FeeController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\PoliciesController;
use App\Http\Controllers\SectionController;
// ── Add this import at the top of web.php ────────────────────────────────────
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\PopulateFieldsController;
// ── Admin Calendar ────────────────────────────────────────────────────────────
Route::get('admin/calendar',         [CalendarController::class, 'index'])->name('admin.calendar');
Route::get('admin/calendar/list',    [CalendarController::class, 'list'])->name('admin.calendar.list');
Route::post('admin/calendar/store',  [CalendarController::class, 'store'])->name('admin.calendar.store');
Route::post('admin/calendar/update', [CalendarController::class, 'update'])->name('admin.calendar.update');
Route::post('admin/calendar/destroy',[CalendarController::class, 'destroy'])->name('admin.calendar.destroy');
// ── Add these routes inside web.php ──────────────────────────────────────────
Route::get('admin/sections',          [SectionController::class, 'index'])->name('admin.sections')->middleware('auth');
Route::get('admin/sections/list',     [SectionController::class, 'list'])->name('admin.sections.list')->middleware('auth');
Route::post('admin/sections/store',   [SectionController::class, 'store'])->name('admin.sections.store')->middleware('auth');
Route::post('admin/sections/update',  [SectionController::class, 'update'])->name('admin.sections.update')->middleware('auth');
Route::post('admin/sections/destroy', [SectionController::class, 'destroy'])->name('admin.sections.destroy')->middleware('auth');
// Routes
Route::get('admin/policies/info',         [PoliciesController::class, 'getInfo'])->name('admin.policies.info');
Route::get('policies/info',         [PoliciesController::class, 'getInfo'])->name('policies.info');
Route::post('admin/policies/info/update', [PoliciesController::class, 'updateInfo'])->name('admin.policies.info.update');
// ── Admin Policies ────────────────────────────────────────────────────────────
Route::get('admin/policies',         [PoliciesController::class, 'index'])->name('admin.policies');
Route::get('admin/policies/list',    [PoliciesController::class, 'list'])->name('admin.policies.list');
Route::get('policies/list',    [PoliciesController::class, 'list'])->name('policies.list');
Route::post('admin/policies/store',  [PoliciesController::class, 'store'])->name('admin.policies.store');
Route::post('admin/policies/update', [PoliciesController::class, 'update'])->name('admin.policies.update');
Route::post('admin/policies/destroy',[PoliciesController::class, 'destroy'])->name('admin.policies.destroy');

// ── Add these routes inside web.php ──────────────────────────────────────────
Route::get('admin/schedule',          [ScheduleController::class, 'index'])->name('admin.schedule')->middleware('auth');
Route::get('admin/schedule/list',     [ScheduleController::class, 'list'])->name('admin.schedule.list')->middleware('auth');
Route::post('admin/schedule/store',   [ScheduleController::class, 'store'])->name('admin.schedule.store')->middleware('auth');
Route::post('admin/schedule/update',  [ScheduleController::class, 'update'])->name('admin.schedule.update')->middleware('auth');
Route::post('admin/schedule/destroy', [ScheduleController::class, 'destroy'])->name('admin.schedule.destroy')->middleware('auth');
// Routes for Login and Logout
Route::get('/', function () {
    return redirect()->route('login');
});
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login'])->name('login.submit');
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('auth');


Route::resource('students', StudentController::class);
Route::resource('enrollment', EnrollmentController::class)->only(['index','show']);
Route::resource('fees', FeeController::class)->only(['index','create','store']);
Route::resource('grades', GradeController::class)->only(['index','edit','update']);
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('index');
});
Route::get('admin/announcements', [AnnouncementController::class, 'adminIndex'])->name('admin.announcements');
Route::get('announcements/all', [AnnouncementController::class, 'getAllAnnouncements'])->name('announcements.all');
/* 
This group of routes is for the teacher module. 
Includes dashboard, class list, and announcements pages. 
*/ 


Route::prefix('teacher')->name('teacher.')->group(function () { 
    Route::get('/dashboard', function () { 
        return view('dashboard'); 
    })->name('dashboard'); 


    Route::get('/class-list', function () { 
        return view('teacher.class-list'); 
    })->name('class-list'); 


    Route::get('/announcements', function () { 
        return view('announcements'); 
    })->name('announcements'); 

    Route::get('/calendar', function () { 
        return view('calendar'); 
    })->name('calendar'); 

    Route::get('/policies', function () { 
        return view('policies'); 
    })->name('policies'); 
    
});
Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
Route::post('/announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
Route::get('/announcements/list', [AnnouncementController::class, 'getAnnouncements'])->name('announcements.list');

Route::post('announcements/update', [AnnouncementController::class, 'update'])->name('announcements.update');
Route::post('announcements/destroy', [AnnouncementController::class, 'destroy'])->name('announcements.destroy');
/* 
This group of routes is for the student module. 
Includes dashboard, class schedule, and announcements pages. 
*/ 

Route::prefix('student')->name('student.')->middleware('auth')->group(function () { 
    Route::get('/dashboard', function () { 
        return view('dashboard'); 
    })->name('dashboard'); 

    Route::get('/class-schedule', function () { 
        return view('class-schedule'); 
    })->name('schedule'); 

    Route::get('/announcements', function () { 
        return view('announcements'); 
    })->name('announcements'); 
});


// Populate Fields (shared across pages)
Route::get('fields/subjects',              [PopulateFieldsController::class, 'getSubjects'])->name('fields.subjects');
Route::get('fields/sections',              [PopulateFieldsController::class, 'getSections'])->name('fields.sections');
Route::get('fields/sections/{gradeLevel}', [PopulateFieldsController::class, 'getSectionsByGrade'])->name('fields.sections.byGrade');
Route::get('fields/grade-levels',          [PopulateFieldsController::class, 'getGradeLevels'])->name('fields.gradeLevels');
Route::get('fields/students-per-grade', [PopulateFieldsController::class, 'getStudentsPerGrade'])->name('fields.studentsPerGrade');

use App\Http\Controllers\ChatController;

Route::post('chat/send', [ChatController::class, 'send'])->name('chat.send');
Route::post('/chat/clear', [ChatController::class, 'clearHistory'])->name('chat.clear');
use App\Http\Controllers\AcademicYearController;

Route::get('academic-years',             [AcademicYearController::class, 'index'])->name('academic-years.index')->middleware('auth');
Route::post('academic-years/set-active', [AcademicYearController::class, 'setActive'])->name('academic-years.setActive')->middleware('auth');
Route::post('academic-years/store',      [AcademicYearController::class, 'store'])->name('academic-years.store')->middleware('auth');
Route::post('academic-years/destroy',    [AcademicYearController::class, 'destroy'])->name('academic-years.destroy')->middleware('auth');