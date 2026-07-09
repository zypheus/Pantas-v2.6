<?php

namespace App\Http\Controllers;

use App\Exports\AttendanceLogsExport;
use App\Models\AttendanceLog;
use App\Models\AttendanceProgram;
use App\Models\AttendanceStudent;
use App\Services\PatronAttendanceReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AttendanceLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = AttendanceLog::with('student')
            ->when($request->from, fn ($q) => $q->whereDate('scanned_at', '>=', $request->from))
            ->when($request->to, fn ($q) => $q->whereDate('scanned_at', '<=', $request->to))
            ->when($request->student_name, fn ($q) => $q->where('student_id', $request->student_name))
            ->when($request->course_code, fn ($q) => $q->whereHas('student', fn ($q2) => $q2->where('course', $request->course_code))
            )
            ->when($request->year_level, fn ($q) => $q->whereHas('student', fn ($q2) => $q2->where('year', $request->year_level))
            )
            // 🔍 UNIVERSAL SEARCH
            ->when($request->search, function ($q) use ($request) {
                $search = $request->search;

                $q->where(function ($query) use ($search) {
                    $query->whereHas('student', function ($s) use ($search) {
                        $s->where('firstname', 'like', "%{$search}%")
                            ->orWhere('lastname', 'like', "%{$search}%")
                            ->orWhere('course', 'like', "%{$search}%")
                            ->orWhere('year', 'like', "%{$search}%");
                    })
                        ->orWhere('status', 'like', "%{$search}%")
                        ->orWhere('scanned_at', 'like', "%{$search}%");
                });
            })
            ->orderBy('scanned_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        $students = AttendanceStudent::orderBy('lastname')->get();
        $courses = AttendanceStudent::select('course')->distinct()->pluck('course');
        $years = AttendanceStudent::select('year')->distinct()->pluck('year');

        return view('attendance_logs.index', compact('logs', 'students', 'courses', 'years'));
    }

    public function create()
    {
        $students = AttendanceStudent::all();

        return view('attendance_logs.create', compact('students'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:attendance_students,id',
            'status' => 'required|in:in,out',
            'scanned_at' => 'required|date',
        ]);

        AttendanceLog::create($request->only(['student_id', 'status', 'scanned_at']));

        return redirect()->route('attendance_logs.index')->with('success', 'Attendance logged!');
    }

    public function exportPdf(Request $request)
    {
        $logs = AttendanceLog::with('student')
            ->when($request->from, fn ($q) => $q->whereDate('scanned_at', '>=', $request->from))
            ->when($request->to, fn ($q) => $q->whereDate('scanned_at', '<=', $request->to))
            ->when($request->student_name, fn ($q) => $q->where('student_id', $request->student_name))
            ->when($request->course_code, fn ($q) => $q->whereHas('student', fn ($q2) => $q2->where('course', $request->course_code))
            )
            ->when($request->year_level, fn ($q) => $q->whereHas('student', fn ($q2) => $q2->where('year', $request->year_level))
            )
            // ⭐ FIX: add universal search
            ->when($request->search, function ($q) use ($request) {
                $search = $request->search;

                $q->where(function ($query) use ($search) {
                    $query->whereHas('student', function ($s) use ($search) {
                        $s->where('firstname', 'like', "%{$search}%")
                            ->orWhere('lastname', 'like', "%{$search}%")
                            ->orWhere('course', 'like', "%{$search}%")
                            ->orWhere('year', 'like', "%{$search}%");
                    })
                        ->orWhere('status', 'like', "%{$search}%")
                        ->orWhere('scanned_at', 'like', "%{$search}%");
                });
            })
            ->orderBy('scanned_at', 'desc')
            ->get();

        $pdf = Pdf::loadView('attendance_logs.pdf', compact('logs'));

        return $pdf->download('attendance_logs.pdf');
    }

    public function exportExcel(Request $request)
    {
        $logs = AttendanceLog::with('student')
            ->when($request->from, fn ($q) => $q->whereDate('scanned_at', '>=', $request->from))
            ->when($request->to, fn ($q) => $q->whereDate('scanned_at', '<=', $request->to))
            ->when($request->student_name, fn ($q) => $q->where('student_id', $request->student_name))
            ->when($request->course_code, fn ($q) => $q->whereHas('student', fn ($q2) => $q2->where('course', $request->course_code))
            )
            ->when($request->year_level, fn ($q) => $q->whereHas('student', fn ($q2) => $q2->where('year', $request->year_level))
            )
            // ⭐ FIX: universal search
            ->when($request->search, function ($q) use ($request) {
                $search = $request->search;

                $q->where(function ($query) use ($search) {
                    $query->whereHas('student', function ($s) use ($search) {
                        $s->where('firstname', 'like', "%{$search}%")
                            ->orWhere('lastname', 'like', "%{$search}%")
                            ->orWhere('course', 'like', "%{$search}%")
                            ->orWhere('year', 'like', "%{$search}%");
                    })
                        ->orWhere('status', 'like', "%{$search}%")
                        ->orWhere('scanned_at', 'like', "%{$search}%");
                });
            })
            ->orderBy('scanned_at', 'desc')
            ->get();

        return Excel::download(new AttendanceLogsExport($logs), 'attendance_logs.xlsx');
    }

    public function absences(Request $request)
    {
        $date = $this->absenceDate($request);
        $absences = $this->absenceQuery($request, $date)
            ->orderBy('lastname')
            ->orderBy('firstname')
            ->paginate(15)
            ->withQueryString();

        $courses = AttendanceStudent::query()
            ->whereNotNull('course')
            ->where('course', '!=', '')
            ->distinct()
            ->orderBy('course')
            ->pluck('course');

        $years = AttendanceStudent::query()
            ->whereNotNull('year')
            ->where('year', '!=', '')
            ->distinct()
            ->orderBy('year')
            ->pluck('year');

        return view('attendance_logs.absences', compact('absences', 'courses', 'date', 'years'));
    }

    public function exportAbsencesCsv(Request $request)
    {
        $date = $this->absenceDate($request);
        $students = $this->absenceQuery($request, $date)
            ->orderBy('lastname')
            ->orderBy('firstname')
            ->get();

        $filename = 'attendance-absences-'.$date.'.csv';

        return response()->streamDownload(function () use ($date, $students): void {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['Attendance absences', $date]);
            fputcsv($handle, []);
            fputcsv($handle, ['Student ID', 'Last Name', 'First Name', 'Program', 'Year Level', 'Mobile Number']);

            foreach ($students as $student) {
                fputcsv($handle, [
                    $student->student_id,
                    $student->lastname,
                    $student->firstname,
                    $student->course,
                    $student->year,
                    $student->mobile_number,
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function reportsHub()
    {
        return view('attendance_logs.reports_hub');
    }

    public function reportsDashboard(Request $request, PatronAttendanceReportService $patronReports)
    {
        $programNameByCode = AttendanceProgram::query()->pluck('program_name', 'program_code');
        $only = $request->query('only');
        $from = $request->query('from');
        $to = $request->query('to');

        return view('attendance_logs.reports_dashboard', array_merge(
            compact('programNameByCode', 'only', 'from', 'to'),
            $patronReports->build($from, $to)
        ));
    }

    public function reportsExportCsv(Request $request, PatronAttendanceReportService $patronReports)
    {
        return $patronReports->streamCsvResponse(
            $request->query('from'),
            $request->query('to')
        );
    }

    private function absenceDate(Request $request): string
    {
        $validated = $request->validate([
            'date' => ['nullable', 'date'],
            'course_code' => ['nullable', 'string'],
            'year_level' => ['nullable', 'string'],
            'search' => ['nullable', 'string'],
        ]);

        return $validated['date'] ?? now('Asia/Manila')->toDateString();
    }

    private function absenceQuery(Request $request, string $date): Builder
    {
        return AttendanceStudent::query()
            ->when($request->query('course_code'), fn (Builder $query, string $course) => $query->where('course', $course))
            ->when($request->query('year_level'), fn (Builder $query, string $year) => $query->where('year', $year))
            ->when($request->query('search'), function (Builder $query, string $search): void {
                $query->where(function (Builder $inner) use ($search): void {
                    $inner->where('student_id', 'like', "%{$search}%")
                        ->orWhere('firstname', 'like', "%{$search}%")
                        ->orWhere('lastname', 'like', "%{$search}%")
                        ->orWhere('course', 'like', "%{$search}%")
                        ->orWhere('year', 'like', "%{$search}%");
                });
            })
            ->whereDoesntHave('logs', function (Builder $query) use ($date): void {
                $query->whereDate('scanned_at', $date)
                    ->whereRaw('LOWER(status) = ?', ['in']);
            });
    }
}
