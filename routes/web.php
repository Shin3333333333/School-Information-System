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