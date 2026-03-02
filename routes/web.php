<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
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
