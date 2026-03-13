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

// ── Admin Calendar ────────────────────────────────────────────────────────────
Route::get('admin/calendar',         [CalendarController::class, 'index'])->name('admin.calendar');
Route::get('admin/calendar/list',    [CalendarController::class, 'list'])->name('admin.calendar.list');
Route::post('admin/calendar/store',  [CalendarController::class, 'store'])->name('admin.calendar.store');
Route::post('admin/calendar/update', [CalendarController::class, 'update'])->name('admin.calendar.update');
Route::post('admin/calendar/destroy',[CalendarController::class, 'destroy'])->name('admin.calendar.destroy');
// Routes
Route::get('admin/policies/info',         [PoliciesController::class, 'getInfo'])->name('admin.policies.info');
Route::post('admin/policies/info/update', [PoliciesController::class, 'updateInfo'])->name('admin.policies.info.update');
// ── Admin Policies ────────────────────────────────────────────────────────────
Route::get('admin/policies',         [PoliciesController::class, 'index'])->name('admin.policies');
Route::get('admin/policies/list',    [PoliciesController::class, 'list'])->name('admin.policies.list');
Route::post('admin/policies/store',  [PoliciesController::class, 'store'])->name('admin.policies.store');
Route::post('admin/policies/update', [PoliciesController::class, 'update'])->name('admin.policies.update');
Route::post('admin/policies/destroy',[PoliciesController::class, 'destroy'])->name('admin.policies.destroy');
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
        return view('teacher.dashboard'); 
    })->name('dashboard'); 


    Route::get('/class-list', function () { 
        return view('teacher.class-list'); 
    })->name('class-list'); 


    Route::get('/announcements', function () { 
        return view('teacher.announcements'); 
    })->name('announcements'); 
    
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
        return view('student.dashboard'); 
    })->name('dashboard'); 

    Route::get('/class-schedule', function () { 
        return view('student.class-schedule'); 
    })->name('schedule'); 

    Route::get('/announcements', function () { 
        return view('student.announcements'); 
    })->name('announcements'); 
});
use App\Http\Controllers\PopulateFieldsController;

// Populate Fields (shared across pages)
Route::get('fields/subjects',              [PopulateFieldsController::class, 'getSubjects'])->name('fields.subjects');
Route::get('fields/sections',              [PopulateFieldsController::class, 'getSections'])->name('fields.sections');
Route::get('fields/sections/{gradeLevel}', [PopulateFieldsController::class, 'getSectionsByGrade'])->name('fields.sections.byGrade');
Route::get('fields/grade-levels',          [PopulateFieldsController::class, 'getGradeLevels'])->name('fields.gradeLevels');
