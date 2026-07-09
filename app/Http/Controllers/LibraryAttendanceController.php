<?php

namespace App\Http\Controllers;

use App\Models\LibraryAttendanceFeedback;
use App\Models\LibraryAttendanceLog;
use App\Models\LibraryAttendanceSetting;
use App\Models\LibraryEmployee;
use App\Models\LibraryStudent;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LibraryAttendanceController extends Controller
{
    public function scanner(): View
    {
        return view('library.attendance.scanner', [
            'logoutFeedbackEnabled' => $this->feedbackEnabled(),
        ]);
    }

    public function scan(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'qrcode' => ['required', 'string'],
        ]);

        $token = trim(str_replace("\r", '', $validated['qrcode']));
        $student = LibraryStudent::query()
            ->where('qrcode', $token)
            ->orWhere('id_number', $token)
            ->first();

        $employee = $student ? null : LibraryEmployee::query()
            ->where('qrcode', $token)
            ->orWhere('employee_id', $token)
            ->first();

        if (! $student && ! $employee) {
            return response()->json([
                'type' => 'error',
                'message' => 'Library patron not recognized.',
            ], 404);
        }

        $lastLog = LibraryAttendanceLog::query()
            ->when($student, fn ($query) => $query->where('student_id', $student->id))
            ->when($employee, fn ($query) => $query->where('employee_id', $employee->id))
            ->latest('scanned_at')
            ->latest('id')
            ->first();

        $status = $lastLog && strtoupper((string) $lastLog->status) === 'IN' ? 'OUT' : 'IN';

        $log = LibraryAttendanceLog::query()->create([
            'student_id' => $student?->id,
            'employee_id' => $employee?->id,
            'status' => $status,
            'section' => $request->input('section'),
            'scanned_at' => Carbon::now('Asia/Manila'),
        ]);

        $patron = $student ?: $employee;

        return response()->json([
            'type' => $student ? 'student' : 'employee',
            'patron_id' => $patron->id,
            'patron' => [
                'firstname' => $patron->firstname,
                'lastname' => $patron->lastname,
                'profile_picture' => $student?->profile_picture ?? $employee?->formal_picture,
            ],
            'status' => $status,
            'logout_feedback_enabled' => $this->feedbackEnabled(),
            'log' => [
                'scanned_at' => $log->scanned_at->format('Y-m-d h:i:s A'),
            ],
        ]);
    }

    public function logs(Request $request): View
    {
        $logs = LibraryAttendanceLog::query()
            ->with(['student', 'employee'])
            ->when($request->from, fn ($query) => $query->whereDate('scanned_at', '>=', $request->from))
            ->when($request->to, fn ($query) => $query->whereDate('scanned_at', '<=', $request->to))
            ->latest('scanned_at')
            ->paginate(15)
            ->withQueryString();

        return view('library.attendance.logs', compact('logs'));
    }

    public function reports(): View
    {
        $totalIns = LibraryAttendanceLog::query()->where('status', 'IN')->count();
        $totalOuts = LibraryAttendanceLog::query()->where('status', 'OUT')->count();
        $todayIns = LibraryAttendanceLog::query()
            ->where('status', 'IN')
            ->whereDate('scanned_at', Carbon::now('Asia/Manila')->toDateString())
            ->count();

        return view('library.attendance.reports', compact('totalIns', 'totalOuts', 'todayIns'));
    }

    public function feedback(Request $request): JsonResponse
    {
        if (! $this->feedbackEnabled()) {
            return response()->json(['success' => false, 'message' => 'Library feedback is disabled.'], 403);
        }

        $validated = $request->validate([
            'student_id' => ['nullable', 'integer', 'exists:library_students,id'],
            'employee_id' => ['nullable', 'integer', 'exists:library_employees,id'],
            'rating' => ['nullable', 'string', 'in:excellent,good,medium,poor,very_bad'],
            'declined' => ['nullable', 'boolean'],
        ]);

        abort_if(empty($validated['student_id']) && empty($validated['employee_id']), 422);

        $declined = (bool) ($validated['declined'] ?? false);

        LibraryAttendanceFeedback::query()->create([
            'student_id' => $validated['student_id'] ?? null,
            'employee_id' => $validated['employee_id'] ?? null,
            'rating' => $declined ? null : ($validated['rating'] ?? null),
            'declined' => $declined,
        ]);

        return response()->json(['success' => true]);
    }

    public function feedbackSettings(): View
    {
        return view('library.attendance.feedback_settings', [
            'enabled' => $this->feedbackEnabled(),
        ]);
    }

    public function updateFeedbackSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'enabled' => ['required', 'boolean'],
        ]);

        LibraryAttendanceSetting::query()->updateOrCreate(
            ['key' => 'logout_feedback_enabled'],
            ['value' => $validated['enabled'] ? '1' : '0']
        );

        return back()->with('success', 'Library visit feedback settings updated.');
    }

    private function feedbackEnabled(): bool
    {
        $value = LibraryAttendanceSetting::query()
            ->where('key', 'logout_feedback_enabled')
            ->value('value');

        return $value === null || in_array(strtolower((string) $value), ['1', 'true', 'yes', 'on'], true);
    }
}
