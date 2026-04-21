<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AttendanceSessionController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrganisationController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');
Route::get('/me', [AuthController::class, 'me'])->middleware('auth');
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

Route::middleware(['auth', 'role:admin,leader'])->group(function () {
    Route::get('users/export/excel', [UserController::class, 'exportExcel'])->name('users.export.excel');
    Route::get('users/export/pdf', [UserController::class, 'exportPdf'])->name('users.export.pdf');
    Route::post('users/reset-grades', [UserController::class, 'resetGrades'])->name('users.reset-grades');
    Route::resource('/admin/users', UserController::class);
    Route::resource('/sessions', AttendanceSessionController::class);
    
    // Attendance marking for leaders/admins
    Route::get('/sessions/{session}/mark', [AttendanceController::class, 'markingSheet'])->name('sessions.mark');
    Route::post('/sessions/{session}/mark', [AttendanceController::class, 'submitMarking'])->name('sessions.submit-mark');
    Route::get('/sessions/{session}/report', [AttendanceController::class, 'report'])->name('sessions.report');
    Route::get('/sessions/{session}/logs', [AttendanceController::class, 'sessionLogs'])->name('sessions.logs');
});

Route::middleware(['auth'])->group(function() {
    // Self-checkin and History for members
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/sessions/{session}/checkin', [AttendanceController::class, 'selfCheckIn'])->name('sessions.checkin');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('/admin/organisations', OrganisationController::class);
    Route::resource('/admin/divisions', DivisionController::class);
});
