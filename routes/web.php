<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MarkController;
use App\Http\Controllers\PortalController;
use App\Http\Controllers\SubjectController;
use Illuminate\Support\Facades\Route;

// Each seat edits only its own block below.

// ===== Seat 2: login, logout, home, leave =====
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/login', [LoginController::class, 'show'])->name('login');

// ===== Seat 3: students (teacher) =====

// ===== Seat 4: subjects and marks (teacher) =====
// ASSUMPTION (Seat 2): the role middleware alias is `role`. Adjust after feat/auth merges.
Route::middleware(['auth', 'role:teacher'])->group(function () {
    Route::get('/subjects', [SubjectController::class, 'index'])->name('subjects.index');
    Route::get('/subjects/create', [SubjectController::class, 'create'])->name('subjects.create');
    Route::post('/subjects', [SubjectController::class, 'store'])->name('subjects.store');
    Route::get('/subjects/{subject}/marks', [MarkController::class, 'edit'])->name('marks.edit');
    Route::put('/subjects/{subject}/marks', [MarkController::class, 'update'])->name('marks.update');
});

// ===== Seat 5: student portal (/my/...) =====
// ASSUMPTION (Seat 2): the role middleware alias is `role`, and the menu links use
// the route names portal.subjects and portal.marks. Adjust after feat/auth merges.
// No {id} in these URLs: the controller always reads auth()->user()->student.
Route::middleware(['auth', 'role:student'])->group(function () {
    Route::get('/my/subjects', [PortalController::class, 'subjects'])->name('portal.subjects');
    Route::get('/my/marks', [PortalController::class, 'marks'])->name('portal.marks');
});

// ===== Seat 6: principal (read-only) =====
