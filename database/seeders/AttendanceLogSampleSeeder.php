<?php

namespace Database\Seeders;

use App\Models\AttendanceLog;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttendanceLogSampleSeeder extends Seeder
{
    private const TIMEZONE = 'Asia/Manila';

    public function run(): void
    {
        $students = Student::query()
            ->whereIn('qrcode', $this->sampleQrCodes())
            ->orderBy('qrcode')
            ->get();

        if ($students->isEmpty()) {
            $this->command?->warn('No sample students found. Run StudentSampleSeeder first.');

            return;
        }

        DB::transaction(function () use ($students) {
            AttendanceLog::query()
                ->whereIn('student_id', $students->pluck('id')->map(fn ($id) => (string) $id))
                ->delete();

            $now = Carbon::now(self::TIMEZONE);
            $rows = [];

            foreach ($students->values() as $index => $student) {
                $visitCount = max(16, 74 - ($index * 6));

                for ($visit = 0; $visit < $visitCount; $visit++) {
                    $scanIn = $this->scanInTime($now, $index, $visit);
                    $scanOut = $scanIn->copy()->addMinutes(55 + (($index + $visit) % 7) * 25);

                    $rows[] = $this->logRow($student->id, 'IN', $scanIn);

                    if (($visit + $index) % 9 !== 0) {
                        $rows[] = $this->logRow($student->id, 'OUT', $scanOut);
                    }
                }
            }

            foreach (array_chunk($rows, 500) as $chunk) {
                AttendanceLog::query()->insert($chunk);
            }
        });

        $this->command?->info('Sample attendance logs seeded for reports and analytics.');
    }

    /**
     * @return list<string>
     */
    private function sampleQrCodes(): array
    {
        return array_map(
            fn (int $number): string => sprintf('S-%08d', $number),
            range(1, 10)
        );
    }

    private function scanInTime(Carbon $now, int $studentIndex, int $visitIndex): Carbon
    {
        $date = $now->copy()
            ->subDays(($visitIndex * 5) + ($studentIndex * 2))
            ->startOfDay();

        while ($date->isWeekend()) {
            $date->subDay();
        }

        $hour = [8, 9, 10, 13, 14, 15, 16][($visitIndex + $studentIndex) % 7];
        $minute = [0, 5, 10, 15, 20, 30, 45][($visitIndex * 2 + $studentIndex) % 7];

        return $date->setTime($hour, $minute);
    }

    /**
     * @return array{student_id: string, status: string, scanned_at: string, created_at: string, updated_at: string}
     */
    private function logRow(int $studentId, string $status, Carbon $scannedAt): array
    {
        $timestamp = $scannedAt->toDateTimeString();

        return [
            'student_id' => (string) $studentId,
            'status' => $status,
            'scanned_at' => $timestamp,
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ];
    }
}
