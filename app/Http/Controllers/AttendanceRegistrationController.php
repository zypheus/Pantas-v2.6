<?php

namespace App\Http\Controllers;

use App\Models\AttendancePendingEmployee;
use App\Models\AttendancePendingStudent;
use App\Models\AttendanceProgram;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceRegistrationController extends Controller
{
    public function create(): View
    {
        return view('attendance.register', [
            'programs' => AttendanceProgram::query()->orderBy('program_name')->get(),
            'workStartYears' => range((int) date('Y'), 1980),
        ]);
    }

    public function storeStudent(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'string', 'max:255'],
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'middle_initial' => ['nullable', 'string', 'max:10'],
            'birth_date' => ['nullable', 'date'],
            'course' => ['nullable', 'string', 'max:255'],
            'year' => ['nullable', 'string', 'max:255'],
            'mobile_number' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
        ]);

        AttendancePendingStudent::query()->create($validated);

        return back()->with('success', 'Attendance student registration submitted.');
    }

    public function storeEmployee(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'string', 'max:255'],
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'middle_initial' => ['nullable', 'string', 'max:10'],
            'department' => ['nullable', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'address' => ['nullable', 'string'],
        ]);

        AttendancePendingEmployee::query()->create($validated);

        return back()->with('success', 'Attendance employee registration submitted.');
    }
}
