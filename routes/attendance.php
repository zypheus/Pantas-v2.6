<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceLogController;
use App\Http\Controllers\AttendancePatronAdminController;
use App\Http\Controllers\AttendanceRegistrationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FeedController;
use Illuminate\Support\Facades\Route;

Route::get('/attendance', [AttendanceController::class, 'showScanner'])->name('attendance.scan');
Route::post('/attendance', [AttendanceController::class, 'scan'])->name('attendance.process');
Route::post('/attendance-feedback', [FeedController::class, 'store'])->name('attendance.feedback.store');
Route::get('/register/attendance', [AttendanceRegistrationController::class, 'create'])->name('attendance.register');
Route::post('/register/attendance', [AttendanceRegistrationController::class, 'storeStudent'])->name('attendance.pending.store');
Route::post('/register/attendance/employee', [AttendanceRegistrationController::class, 'storeEmployee'])->name('attendance.pendingEmployee.store');

Route::middleware('auth')->group(function (): void {
    Route::get('/dashboard/attendance-admin', [DashboardController::class, 'attendanceAdmin'])
        ->middleware('attendance.admin')
        ->name('dashboard.attendance-admin');

    Route::get('/dashboard/attendance-staff', [DashboardController::class, 'attendanceStaff'])
        ->middleware('attendance.access')
        ->name('dashboard.attendance-staff');
});

Route::middleware(['auth', 'attendance.access'])->group(function (): void {
    Route::get('/attendance/change-video', [AttendanceController::class, 'showChangeVideo'])->name('attendance.changeVideo');
    Route::post('/attendance/upload-video', [AttendanceController::class, 'uploadVideo'])->name('attendance.uploadVideo');
    Route::get('/attendance/logout-feedback', [AttendanceController::class, 'feedbackSettings'])->name('attendance.feedback.settings');
    Route::post('/attendance/logout-feedback', [AttendanceController::class, 'updateFeedbackSettings'])->name('attendance.feedback.settings.update');
});

Route::middleware(['auth', 'attendance.admin'])->group(function (): void {
    Route::get('/attendance-logs', [AttendanceLogController::class, 'index'])->name('attendance_logs.index');
    Route::get('/attendance-logs/absences', [AttendanceLogController::class, 'absences'])->name('attendance_logs.absences');
    Route::get('/attendance-logs/absences/export', [AttendanceLogController::class, 'exportAbsencesCsv'])->name('attendance_logs.absences.export');
    Route::get('/attendance-logs/reports', [AttendanceLogController::class, 'reportsHub'])->name('attendance_logs.reports.hub');
    Route::get('/attendance-logs/reports/dashboard', [AttendanceLogController::class, 'reportsDashboard'])->name('attendance_logs.reports.dashboard');
    Route::get('/attendance-logs/reports/export', [AttendanceLogController::class, 'reportsExportCsv'])->name('attendance_logs.reports.export');
    Route::get('/attendance-logs/export/excel', [AttendanceLogController::class, 'exportExcel'])->name('attendance_logs.export.excel');
    Route::get('/attendance-logs/export/pdf', [AttendanceLogController::class, 'exportPdf'])->name('attendance_logs.export.pdf');
    Route::get('/admin/attendance-feedbacks', [FeedController::class, 'index'])->name('admin.attendance.feedbacks');
    Route::get('/attendance/pending', [AttendancePatronAdminController::class, 'index'])->name('attendance.pending.index');
    Route::get('/attendance/pending/students', [AttendancePatronAdminController::class, 'pendingStudents'])->name('attendance.pending.students');
    Route::get('/attendance/pending/employees', [AttendancePatronAdminController::class, 'pendingEmployees'])->name('attendance.pending.employees');
    Route::get('/attendance/patrons/students/create', [AttendancePatronAdminController::class, 'createStudent'])->name('attendance.patrons.students.create');
    Route::post('/attendance/patrons/students', [AttendancePatronAdminController::class, 'storeStudent'])->name('attendance.patrons.students.store');
    Route::get('/attendance/patrons/students/{student}/edit', [AttendancePatronAdminController::class, 'editStudent'])->name('attendance.patrons.students.edit');
    Route::put('/attendance/patrons/students/{student}', [AttendancePatronAdminController::class, 'updateStudent'])->name('attendance.patrons.students.update');
    Route::delete('/attendance/patrons/students/{student}', [AttendancePatronAdminController::class, 'destroyStudent'])->name('attendance.patrons.students.destroy');
    Route::post('/attendance/patrons/students/import', [AttendancePatronAdminController::class, 'importStudents'])->name('attendance.patrons.students.import');
    Route::get('/attendance/patrons/students/export', [AttendancePatronAdminController::class, 'exportStudents'])->name('attendance.patrons.students.export');
    Route::get('/attendance/patrons/students/template', [AttendancePatronAdminController::class, 'studentTemplate'])->name('attendance.patrons.students.template');
    Route::get('/attendance/patrons/students/id-cards/bulk', [AttendancePatronAdminController::class, 'bulkStudentIds'])->name('attendance.patrons.students.ids.bulk');
    Route::get('/attendance/patrons/students/{student}/id-card', [AttendancePatronAdminController::class, 'studentIdCard'])->name('attendance.patrons.students.id');
    Route::get('/attendance/patrons/students/{student}/id-card/download', [AttendancePatronAdminController::class, 'downloadStudentIdCard'])->name('attendance.patrons.students.id.download');
    Route::get('/attendance/patrons/employees/create', [AttendancePatronAdminController::class, 'createEmployee'])->name('attendance.patrons.employees.create');
    Route::post('/attendance/patrons/employees', [AttendancePatronAdminController::class, 'storeEmployee'])->name('attendance.patrons.employees.store');
    Route::get('/attendance/patrons/employees/{employee}/edit', [AttendancePatronAdminController::class, 'editEmployee'])->name('attendance.patrons.employees.edit');
    Route::put('/attendance/patrons/employees/{employee}', [AttendancePatronAdminController::class, 'updateEmployee'])->name('attendance.patrons.employees.update');
    Route::delete('/attendance/patrons/employees/{employee}', [AttendancePatronAdminController::class, 'destroyEmployee'])->name('attendance.patrons.employees.destroy');
    Route::post('/attendance/patrons/employees/import', [AttendancePatronAdminController::class, 'importEmployees'])->name('attendance.patrons.employees.import');
    Route::get('/attendance/patrons/employees/export', [AttendancePatronAdminController::class, 'exportEmployees'])->name('attendance.patrons.employees.export');
    Route::get('/attendance/patrons/employees/template', [AttendancePatronAdminController::class, 'employeeTemplate'])->name('attendance.patrons.employees.template');
    Route::get('/attendance/patrons/employees/id-cards/bulk', [AttendancePatronAdminController::class, 'bulkEmployeeIds'])->name('attendance.patrons.employees.ids.bulk');
    Route::get('/attendance/patrons/employees/{employee}/id-card', [AttendancePatronAdminController::class, 'employeeIdCard'])->name('attendance.patrons.employees.id');
    Route::get('/attendance/patrons/employees/{employee}/id-card/download', [AttendancePatronAdminController::class, 'downloadEmployeeIdCard'])->name('attendance.patrons.employees.id.download');
    Route::post('/attendance/pending/students/{id}/approve', [AttendancePatronAdminController::class, 'approveStudent'])->name('attendance.students.approve');
    Route::post('/attendance/pending/students/{id}/reject', [AttendancePatronAdminController::class, 'rejectStudent'])->name('attendance.students.reject');
    Route::post('/attendance/pending/employees/{id}/approve', [AttendancePatronAdminController::class, 'approveEmployee'])->name('attendance.employees.approve');
    Route::post('/attendance/pending/employees/{id}/reject', [AttendancePatronAdminController::class, 'rejectEmployee'])->name('attendance.employees.reject');
});
