<?php

namespace App\Http\Controllers;

use App\Models\AttendanceEmployee;
use App\Models\AttendancePendingEmployee;
use App\Models\AttendancePendingStudent;
use App\Models\AttendanceStudent;
use App\Services\AdminActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class AttendancePatronAdminController extends Controller
{
    public function approveStudent(int $id, AdminActivityLogger $activities): RedirectResponse
    {
        DB::transaction(function () use ($activities, $id): void {
            $pending = AttendancePendingStudent::query()->findOrFail($id);
            $student = AttendanceStudent::query()->create(array_merge($pending->toArray(), [
                'qrcode' => $pending->qrcode ?: $pending->student_id,
            ]));
            $pending->delete();

            $activities->log('attendance', 'patron.approved', 'Attendance student approved', $student->student_id, $student);
        });

        return back()->with('success', 'Attendance student approved.');
    }

    public function approveEmployee(int $id, AdminActivityLogger $activities): RedirectResponse
    {
        DB::transaction(function () use ($activities, $id): void {
            $pending = AttendancePendingEmployee::query()->findOrFail($id);
            $employee = AttendanceEmployee::query()->create(array_merge($pending->toArray(), [
                'qrcode' => $pending->qrcode ?: $pending->employee_id,
            ]));
            $pending->delete();

            $activities->log('attendance', 'patron.approved', 'Attendance employee approved', $employee->employee_id, $employee);
        });

        return back()->with('success', 'Attendance employee approved.');
    }
}
