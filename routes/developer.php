<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeveloperBrandingController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'developer'])->group(function (): void {
    Route::get('/developer/dashboard', [DashboardController::class, 'developer'])
        ->name('dashboard.developer');
    Route::get('/developer/branding', [DeveloperBrandingController::class, 'edit'])
        ->name('developer.branding.edit');
    Route::put('/developer/branding', [DeveloperBrandingController::class, 'update'])
        ->name('developer.branding.update');
    Route::post('/developer/branding/restore', [DeveloperBrandingController::class, 'restore'])
        ->name('developer.branding.restore');
});
