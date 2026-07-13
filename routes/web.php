<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandingAssetController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ModuleSelectionController;
use App\Models\AttendanceProgram;
use App\Models\Program;
use Illuminate\Support\Facades\Route;

Route::get('/branding-assets/{type}/{filename}', BrandingAssetController::class)
    ->whereIn('type', ['banners', 'logos'])
    ->where('filename', '[A-Za-z0-9._-]+')
    ->name('branding.asset');

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return view('index', [
        'attendancePrograms' => AttendanceProgram::query()->orderBy('program_name')->get(),
        'libraryPrograms' => Program::query()->orderBy('program_name')->get(),
        'workStartYears' => range((int) date('Y'), 1980),
    ]);
})->name('home');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');

Route::post('/switch-module', [ModuleSelectionController::class, 'store'])
    ->middleware('auth')
    ->name('module.switch');

Route::post('/user/preferences/theme', [\App\Http\Controllers\UserPreferenceController::class, 'store'])
    ->middleware('auth')
    ->name('user.preferences.theme');

require __DIR__.'/library.php';
require __DIR__.'/attendance.php';
require __DIR__.'/super-admin.php';
require __DIR__.'/developer.php';
