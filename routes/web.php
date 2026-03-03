<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController; 
use App\Http\Controllers\StudentController; 
use App\Http\Controllers\EnrollmentController; 
use App\Http\Controllers\FeeController; 
use App\Http\Controllers\GradeController; 
use App\Http\Controllers\AdminController; 


Route::get('/', [DashboardController::class, 'index'])->name('dashboard'); 
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