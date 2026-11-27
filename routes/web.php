<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MarkController;
use App\Http\Controllers\PortalController;
use App\Http\Controllers\SubjectController;
use Illuminate\Support\Facades\Route;

// Each seat edits only its own block below.

// ===== Seat 2: login, logout, home, leave =====
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:5,1');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
    Route::get('/', HomeController::class)->name('home');
});

// ===== Seat 3: students (teacher) =====
Route::middleware(['auth', 'role:teacher,admin'])->group(function () {
    // Seat 3 routes go here.
});

// ===== Seat 4: subjects and marks (teacher) =====
Route::middleware(['auth', 'role:teacher,admin'])->group(function () {
    Route::get('/subjects', [SubjectController::class, 'index'])->name('subjects.index');
    Route::get('/subjects/create', [SubjectController::class, 'create'])->name('subjects.create');
    Route::post('/subjects', [SubjectController::class, 'store'])->name('subjects.store');
    Route::get('/subjects/{subject}/marks', [MarkController::class, 'edit'])->name('marks.edit');
    Route::put('/subjects/{subject}/marks', [MarkController::class, 'update'])->name('marks.update');
});

// ===== Seat 5: student portal (/my/...) =====
// The menu links use the route names portal.subjects and portal.marks.
// No {id} in these URLs: the controller always reads auth()->user()->student.
Route::middleware(['auth', 'role:student,admin'])->group(function () {
    Route::get('/my/subjects', [PortalController::class, 'subjects'])->name('portal.subjects');
    Route::get('/my/marks', [PortalController::class, 'marks'])->name('portal.marks');
});

// ===== Seat 6: principal (read-only) =====
Route::middleware(['auth', 'role:principal,admin'])->prefix('principal')->group(function () {
    // Seat 6 routes go here.
});
