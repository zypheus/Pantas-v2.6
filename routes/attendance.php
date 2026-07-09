<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function (): void {
    Route::get('/dashboard/attendance-admin', [DashboardController::class, 'attendanceAdmin'])
        ->middleware('attendance.admin')
        ->name('dashboard.attendance-admin');

    Route::get('/dashboard/attendance-staff', [DashboardController::class, 'attendanceStaff'])
        ->middleware('attendance.access')
        ->name('dashboard.attendance-staff');
});
