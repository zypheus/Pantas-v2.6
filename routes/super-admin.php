<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function (): void {
    Route::get('/dashboard/super-admin', [DashboardController::class, 'superAdmin'])
        ->middleware('super-admin')
        ->name('dashboard.super-admin');
});
