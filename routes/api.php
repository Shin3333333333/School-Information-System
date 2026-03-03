<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\FeeController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/', [DashboardController::class, 'index'])->name('api.dashboard');
Route::resource('students', StudentController::class);
Route::resource('enrollment', EnrollmentController::class)->only(['index','show']);
Route::resource('fees', FeeController::class)->only(['index','create','store']);
Route::resource('grades', GradeController::class)->only(['index','edit','update']);

Route::prefix('admin')->name('api.admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('index');
});

Route::prefix('teacher')->name('api.teacher.')->group(function () {
    Route::get('/dashboard', function () {
        // This should return JSON data for the teacher dashboard
        return response()->json(['message' => 'Teacher Dashboard']);
    })->name('dashboard');

    Route::get('/class-list', function () {
        // This should return a list of classes for the teacher
        return response()->json(['message' => 'Class List']);
    })->name('class-list');

    Route::get('/announcements', function () {
        // This should return a list of announcements for the teacher
        return response()->json(['message' => 'Announcements']);
    })->name('announcements');
});