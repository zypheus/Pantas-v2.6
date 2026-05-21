<?php

namespace App\Services;

use App\Models\AttendanceLog;
use App\Models\Program;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PatronAttendanceReportService
{
    /**
     * @return array{0:?string,1:?string}
     */
    protected function normalizeDateRange(?string $from, ?string $to): array
    {
        $tz = 'Asia/Manila';

        try {
            $fromDt = $from ? Carbon::parse($from, $tz)->startOfDay() : null;
        } catch (\Throwable $e) {
            $fromDt = null;
        }

        try {
            $toDt = $to ? Carbon::parse($to, $tz)->endOfDay() : null;
        } catch (\Throwable $e) {
            $toDt = null;
        }

        if ($fromDt && $toDt && $fromDt->greaterThan($toDt)) {
            [$fromDt, $toDt] = [$toDt->copy()->startOfDay(), $fromDt->copy()->endOfDay()];
        }

        return [
            $fromDt?->toDateTimeString(),
            $toDt?->toDateTimeString(),
        ];
    }

    /**
     * @return array<string, Collection<int, object>|Collection>
     */
    public function build(?string $from = null, ?string $to = null): array
    {
        $empty = [
            'topStudentsByIns' => collect(),
            'topStudentsByDistinctInDays' => collect(),
            'programAttendanceTotals' => collect(),
            'weeklyInsTrend' => collect(),
            'monthlyInsTrend' => collect(),
            'busiestHours' => collect(),
        ];

        if (! Schema::hasTable('attendance_logs')) {
            return $empty;
        }

        [$fromDt, $toDt] = $this->normalizeDateRange($from, $to);

        $inExpr = "LOWER(TRIM(attendance_logs.status)) = 'in'";

        $topStudentsByIns = DB::table('attendance_logs')
            ->join('students', 'students.id', '=', 'attendance_logs.student_id')
            ->whereRaw($inExpr)
            ->when($fromDt && $toDt, fn ($q) => $q->whereBetween('attendance_logs.scanned_at', [$fromDt, $toDt]))
            ->select(
                'students.id',
                'students.lastname',
                'students.firstname',
                'students.course',
                DB::raw('COUNT(*) as ins_count')
            )
            ->groupBy('students.id', 'students.lastname', 'students.firstname', 'students.course')
            ->orderByDesc('ins_count')
            ->limit(10)
            ->get();

        $topStudentsByDistinctInDays = DB::table('attendance_logs')
            ->join('students', 'students.id', '=', 'attendance_logs.student_id')
            ->whereRaw($inExpr)
            ->when($fromDt && $toDt, fn ($q) => $q->whereBetween('attendance_logs.scanned_at', [$fromDt, $toDt]))
            ->select(
                'students.id',
                'students.lastname',
                'students.firstname',
                'students.course',
                DB::raw('COUNT(DISTINCT DATE(attendance_logs.scanned_at)) as distinct_in_days')
            )
            ->groupBy('students.id', 'students.lastname', 'students.firstname', 'students.course')
            ->orderByDesc('distinct_in_days')
            ->limit(10)
            ->get();

        $registeredByCourse = DB::table('students')
            ->whereNotNull('course')
            ->where('course', '!=', '')
            ->select('course', DB::raw('COUNT(*) as student_count'))
            ->groupBy('course')
            ->get()
            ->keyBy('course');

        $insByCourse = DB::table('attendance_logs')
            ->join('students', 'students.id', '=', 'attendance_logs.student_id')
            ->whereRaw($inExpr)
            ->when($fromDt && $toDt, fn ($q) => $q->whereBetween('attendance_logs.scanned_at', [$fromDt, $toDt]))
            ->whereNotNull('students.course')
            ->where('students.course', '!=', '')
            ->select('students.course', DB::raw('COUNT(*) as ins_count'))
            ->groupBy('students.course')
            ->get()
            ->keyBy('course');

        $codes = $registeredByCourse->keys()->merge($insByCourse->keys())->unique()->sort()->values();

        $programAttendanceTotals = $codes->map(function ($code) use ($registeredByCourse, $insByCourse) {
            $sc = (int) ($registeredByCourse->get($code)->student_count ?? 0);
            $ic = (int) ($insByCourse->get($code)->ins_count ?? 0);

            return (object) [
                'course' => $code,
                'student_count' => $sc,
                'ins_count' => $ic,
                'avg_ins_per_student' => $sc > 0 ? round($ic / $sc, 2) : 0.0,
            ];
        })->sortByDesc('ins_count')->values();

        $tz = 'Asia/Manila';
        $weeklyInsTrend = collect();
        if ($fromDt && $toDt) {
            $cursor = Carbon::parse($fromDt, $tz)->startOfWeek(Carbon::MONDAY);
            $endCursor = Carbon::parse($toDt, $tz)->endOfWeek(Carbon::SUNDAY)->endOfDay();
            while ($cursor->lte($endCursor)) {
                $start = $cursor->copy()->startOfWeek(Carbon::MONDAY);
                $end = $cursor->copy()->endOfWeek(Carbon::SUNDAY)->endOfDay();
                $rangeStart = max($start->toDateTimeString(), $fromDt);
                $rangeEnd = min($end->toDateTimeString(), $toDt);

                $cnt = AttendanceLog::query()
                    ->whereRaw("LOWER(TRIM(status)) = 'in'")
                    ->whereBetween('scanned_at', [$rangeStart, $rangeEnd])
                    ->count();

                $weeklyInsTrend->push((object) [
                    'label' => $start->format('M j').' – '.$end->format('M j, Y'),
                    'count' => $cnt,
                ]);

                $cursor->addWeek();
            }
        } else {
            for ($i = 11; $i >= 0; $i--) {
                $start = Carbon::now($tz)->subWeeks($i)->startOfWeek(Carbon::MONDAY);
                $end = $start->copy()->endOfWeek(Carbon::SUNDAY)->endOfDay();
                $cnt = AttendanceLog::query()
                    ->whereRaw("LOWER(TRIM(status)) = 'in'")
                    ->whereBetween('scanned_at', [$start->toDateTimeString(), $end->toDateTimeString()])
                    ->count();
                $weeklyInsTrend->push((object) [
                    'label' => $start->format('M j').' – '.$end->format('M j, Y'),
                    'count' => $cnt,
                ]);
            }
        }

        $monthlyInsTrend = collect();
        if ($fromDt && $toDt) {
            $cursor = Carbon::parse($fromDt, $tz)->startOfMonth();
            $endCursor = Carbon::parse($toDt, $tz)->endOfMonth()->endOfDay();
            while ($cursor->lte($endCursor)) {
                $start = $cursor->copy()->startOfMonth();
                $end = $cursor->copy()->endOfMonth()->endOfDay();
                $rangeStart = max($start->toDateTimeString(), $fromDt);
                $rangeEnd = min($end->toDateTimeString(), $toDt);

                $cnt = AttendanceLog::query()
                    ->whereRaw("LOWER(TRIM(status)) = 'in'")
                    ->whereBetween('scanned_at', [$rangeStart, $rangeEnd])
                    ->count();

                $monthlyInsTrend->push((object) [
                    'label' => $start->format('F Y'),
                    'count' => $cnt,
                ]);

                $cursor->addMonth();
            }
        } else {
            for ($i = 11; $i >= 0; $i--) {
                $start = Carbon::now($tz)->subMonths($i)->startOfMonth();
                $end = $start->copy()->endOfMonth()->endOfDay();
                $cnt = AttendanceLog::query()
                    ->whereRaw("LOWER(TRIM(status)) = 'in'")
                    ->whereBetween('scanned_at', [$start->toDateTimeString(), $end->toDateTimeString()])
                    ->count();
                $monthlyInsTrend->push((object) [
                    'label' => $start->format('F Y'),
                    'count' => $cnt,
                ]);
            }
        }

        $hourRows = DB::table('attendance_logs')
            ->whereRaw($inExpr)
            ->when($fromDt && $toDt, fn ($q) => $q->whereBetween('attendance_logs.scanned_at', [$fromDt, $toDt]))
            ->select(DB::raw('HOUR(scanned_at) as hr'), DB::raw('COUNT(*) as cnt'))
            ->groupBy('hr')
            ->get()
            ->keyBy(fn ($r) => (int) $r->hr);

        $busiestHours = collect(range(0, 23))->map(function ($h) use ($hourRows) {
            $cnt = (int) ($hourRows->get($h)->cnt ?? 0);

            return (object) [
                'hour' => $h,
                'label' => sprintf('%02d:00–%02d:59', $h, $h),
                'count' => $cnt,
            ];
        })->sortByDesc('count')->values();

        return compact(
            'topStudentsByIns',
            'topStudentsByDistinctInDays',
            'programAttendanceTotals',
            'weeklyInsTrend',
            'monthlyInsTrend',
            'busiestHours'
        );
    }

    public function streamCsvResponse(?string $from = null, ?string $to = null): StreamedResponse
    {
        $reports = $this->build($from, $to);
        $programNameByCode = Program::query()->pluck('program_name', 'program_code');
        $filename = 'patron-attendance-reports-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($reports, $programNameByCode) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");

            $w = static function (array $row) use ($out): void {
                fputcsv($out, $row);
            };

            $w(['Patron attendance reports', 'Exported at', now()->timezone('Asia/Manila')->format('Y-m-d H:i')]);
            $w([]);

            $w(['# TOP 10 — MOST IN SCANS']);
            $w(['Rank', 'Last name', 'First name', 'Course code', 'Program name', 'IN count']);
            foreach ($reports['topStudentsByIns'] as $i => $row) {
                $w([
                    $i + 1,
                    $row->lastname,
                    $row->firstname,
                    $row->course ?? '',
                    $row->course ? ($programNameByCode->get($row->course, '')) : '',
                    $row->ins_count,
                ]);
            }
            $w([]);

            $w(['# TOP 10 — DISTINCT DAYS WITH IN']);
            $w(['Rank', 'Last name', 'First name', 'Course code', 'Program name', 'Distinct days']);
            foreach ($reports['topStudentsByDistinctInDays'] as $i => $row) {
                $w([
                    $i + 1,
                    $row->lastname,
                    $row->firstname,
                    $row->course ?? '',
                    $row->course ? ($programNameByCode->get($row->course, '')) : '',
                    $row->distinct_in_days,
                ]);
            }
            $w([]);

            $w(['# TOTALS BY PROGRAM / COURSE']);
            $w(['Course code', 'Program name', 'Registered patrons', 'IN scans (all time)', 'Avg INs / patron']);
            foreach ($reports['programAttendanceTotals'] as $row) {
                $w([
                    $row->course,
                    $programNameByCode->get($row->course, ''),
                    $row->student_count,
                    $row->ins_count,
                    $row->avg_ins_per_student ?? 0,
                ]);
            }
            $w([]);

            $w(['# IN SCANS BY WEEK (last 12 weeks, Asia/Manila)']);
            $w(['Week label', 'IN count']);
            foreach ($reports['weeklyInsTrend'] as $row) {
                $w([$row->label, $row->count]);
            }
            $w([]);

            $w(['# IN SCANS BY MONTH (last 12 months, Asia/Manila)']);
            $w(['Month', 'IN count']);
            foreach ($reports['monthlyInsTrend'] as $row) {
                $w([$row->label, $row->count]);
            }
            $w([]);

            $w(['# BUSIEST HOUR OF DAY (IN scans, all time, server local hour of scanned_at)']);
            $w(['Hour', 'IN count']);
            foreach ($reports['busiestHours'] as $row) {
                $w([$row->label, $row->count]);
            }

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
