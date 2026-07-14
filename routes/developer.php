<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeveloperBrandingController;
use App\Http\Controllers\DeveloperLoginModalController;
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
    Route::get('/developer/branding/activity', [DeveloperBrandingController::class, 'activity'])
        ->name('developer.branding.activity');
    Route::get('/developer/login-modal', [DeveloperLoginModalController::class, 'edit'])
        ->name('developer.login-modal.edit');
    Route::put('/developer/login-modal', [DeveloperLoginModalController::class, 'update'])
        ->name('developer.login-modal.update');
    Route::post('/developer/login-modal/restore', [DeveloperLoginModalController::class, 'restore'])
        ->name('developer.login-modal.restore');
});
