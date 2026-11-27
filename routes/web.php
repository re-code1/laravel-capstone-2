<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\MarkController;
use App\Http\Controllers\PortalController;
use App\Http\Controllers\StudentController;
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

// Apply for leave: students and teachers only (admin and principal do not apply).
Route::middleware(['auth', 'role:student,teacher'])->group(function () {
    Route::get('/leave', [LeaveRequestController::class, 'index'])->name('leave.index');
    Route::get('/leave/create', [LeaveRequestController::class, 'create'])->name('leave.create');
    Route::post('/leave', [LeaveRequestController::class, 'store'])->name('leave.store');
});

// ===== Seat 3: students (teacher) =====
Route::middleware(['auth', 'role:teacher,admin'])->group(function () {
    Route::get('/students', [StudentController::class, 'index'])->name('students.index');
    Route::get('/students/create', [StudentController::class, 'create'])->name('students.create');
    Route::post('/students', [StudentController::class, 'store'])->name('students.store');

    // Attendance sheet: an adviser records their own section, admin any section.
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
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
