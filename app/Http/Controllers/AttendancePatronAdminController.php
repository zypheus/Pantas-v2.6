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
            $student = AttendanceStudent::query()->create(array_merge($pending->only([
                'student_id',
                'lastname',
                'firstname',
                'middle_initial',
                'birth_date',
                'blood_type',
                'qrcode',
                'course',
                'year',
                'mobile_number',
                'address',
                'emergency_person',
                'emergency_relationship',
                'emergency_number',
                'emergency_address',
                'profile_picture',
                'student_signature',
            ]), [
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
            $employee = AttendanceEmployee::query()->create(array_merge($pending->only([
                'employee_id',
                'employee_number',
                'firstname',
                'lastname',
                'middle_initial',
                'department',
                'position',
                'birth_date',
                'sex',
                'civil_status',
                'blood_type',
                'tin_id_number',
                'philhealth_number',
                'sss_number',
                'hdmf_number',
                'qrcode',
                'formal_picture',
                'emergency_contact_name',
                'emergency_contact_relationship',
                'emergency_contact_number',
                'address',
                'employee_signature',
            ]), [
                'qrcode' => $pending->qrcode ?: $pending->employee_id,
            ]));
            $pending->delete();

            $activities->log('attendance', 'patron.approved', 'Attendance employee approved', $employee->employee_id, $employee);
        });

        return back()->with('success', 'Attendance employee approved.');
    }
}
