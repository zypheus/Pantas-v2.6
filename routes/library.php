<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function (): void {
    Route::get('/dashboard/library-admin', [DashboardController::class, 'libraryAdmin'])
        ->middleware('library.admin')
        ->name('dashboard.library-admin');

    Route::get('/dashboard/library-staff', [DashboardController::class, 'libraryStaff'])
        ->middleware('library.access')
        ->name('dashboard.library-staff');
});
