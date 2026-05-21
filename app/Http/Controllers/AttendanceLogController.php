<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceLog;
use App\Models\Program;
use App\Models\Student;
use App\Services\PatronAttendanceReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AttendanceLogsExport;

class AttendanceLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = AttendanceLog::with('student')
            ->when($request->from, fn($q) => $q->whereDate('scanned_at', '>=', $request->from))
            ->when($request->to, fn($q) => $q->whereDate('scanned_at', '<=', $request->to))
            ->when($request->student_name, fn($q) => $q->where('student_id', $request->student_name))
            ->when($request->course_code, fn($q) =>
                $q->whereHas('student', fn($q2) => $q2->where('course', $request->course_code))
            )
            ->when($request->year_level, fn($q) =>
                $q->whereHas('student', fn($q2) => $q2->where('year', $request->year_level))
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

        $students = Student::orderBy('lastname')->get();
        $courses = Student::select('course')->distinct()->pluck('course');
        $years = Student::select('year')->distinct()->pluck('year');

        return view('attendance_logs.index', compact('logs', 'students', 'courses', 'years'));
    }

    public function create()
    {
        $students = Student::all();
        return view('attendance_logs.create', compact('students'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'status' => 'required|in:in,out',
            'scanned_at' => 'required|date',
        ]);

        AttendanceLog::create($request->only(['student_id', 'status', 'scanned_at']));

        return redirect()->route('attendance_logs.index')->with('success', 'Attendance logged!');
    }

    public function exportPdf(Request $request)
    {
        $logs = AttendanceLog::with('student')
            ->when($request->from, fn($q) => $q->whereDate('scanned_at', '>=', $request->from))
            ->when($request->to, fn($q) => $q->whereDate('scanned_at', '<=', $request->to))
            ->when($request->student_name, fn($q) => $q->where('student_id', $request->student_name))
            ->when($request->course_code, fn($q) =>
                $q->whereHas('student', fn($q2) => $q2->where('course', $request->course_code))
            )
            ->when($request->year_level, fn($q) =>
                $q->whereHas('student', fn($q2) => $q2->where('year', $request->year_level))
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
            ->when($request->from, fn($q) => $q->whereDate('scanned_at', '>=', $request->from))
            ->when($request->to, fn($q) => $q->whereDate('scanned_at', '<=', $request->to))
            ->when($request->student_name, fn($q) => $q->where('student_id', $request->student_name))
            ->when($request->course_code, fn($q) =>
                $q->whereHas('student', fn($q2) => $q2->where('course', $request->course_code))
            )
            ->when($request->year_level, fn($q) =>
                $q->whereHas('student', fn($q2) => $q2->where('year', $request->year_level))
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

    public function reportsHub()
    {
        return view('attendance_logs.reports_hub');
    }

    public function reportsDashboard(Request $request, PatronAttendanceReportService $patronReports)
    {
        $programNameByCode = Program::query()->pluck('program_name', 'program_code');
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
}
