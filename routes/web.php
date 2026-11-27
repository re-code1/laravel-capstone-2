<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MarkController;
use App\Http\Controllers\SubjectController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/login', [LoginController::class, 'show'])->name('login');

// Seat 4: subjects and marks (teachers only).
// ASSUMPTION (Seat 2): the role middleware alias is `role`. Adjust after feat/auth merges.
Route::middleware(['auth', 'role:teacher'])->group(function () {
    Route::get('/subjects', [SubjectController::class, 'index'])->name('subjects.index');
    Route::get('/subjects/create', [SubjectController::class, 'create'])->name('subjects.create');
    Route::post('/subjects', [SubjectController::class, 'store'])->name('subjects.store');
    Route::get('/subjects/{subject}/marks', [MarkController::class, 'edit'])->name('marks.edit');
    Route::put('/subjects/{subject}/marks', [MarkController::class, 'update'])->name('marks.update');
});
