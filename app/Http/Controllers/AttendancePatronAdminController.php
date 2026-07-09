<?php

namespace App\Http\Controllers;

use App\Models\AttendanceEmployee;
use App\Models\AttendancePendingEmployee;
use App\Models\AttendancePendingStudent;
use App\Models\AttendanceProgram;
use App\Models\AttendanceStudent;
use App\Services\AdminActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Spatie\SimpleExcel\SimpleExcelReader;
use ZipArchive;

class AttendancePatronAdminController extends Controller
{
    private array $studentColumns = [
        'student_id', 'firstname', 'lastname', 'middle_initial', 'birth_date',
        'educational_level', 'course', 'year', 'mobile_number', 'address',
        'emergency_person', 'emergency_relationship', 'emergency_number',
        'emergency_address', 'blood_type', 'qrcode',
    ];

    private array $employeeColumns = [
        'employee_id', 'employee_number', 'firstname', 'lastname', 'middle_initial',
        'department', 'position', 'birth_date', 'mobile_number', 'sex',
        'civil_status', 'blood_type', 'tin_id_number', 'philhealth_number',
        'sss_number', 'hdmf_number', 'address', 'emergency_contact_name',
        'emergency_contact_relationship', 'emergency_contact_number',
        'emergency_address', 'qrcode',
    ];

    public function index(Request $request): View
    {
        $studentQuery = AttendanceStudent::query();
        $employeeQuery = AttendanceEmployee::query();

        if ($request->filled('student_search')) {
            $search = $request->string('student_search')->toString();
            $studentQuery->where(function ($query) use ($search): void {
                $query->where('firstname', 'like', "%{$search}%")
                    ->orWhere('lastname', 'like', "%{$search}%")
                    ->orWhere('course', 'like', "%{$search}%")
                    ->orWhere('qrcode', 'like', "%{$search}%")
                    ->orWhere('student_id', 'like', "%{$search}%");
            });
        }

        foreach (['student_course' => 'course', 'student_year' => 'year', 'student_educational_level' => 'educational_level'] as $input => $column) {
            if ($request->filled($input)) {
                $studentQuery->where($column, $request->input($input));
            }
        }

        if ($request->filled('employee_search')) {
            $search = $request->string('employee_search')->toString();
            $employeeQuery->where(function ($query) use ($search): void {
                $query->where('firstname', 'like', "%{$search}%")
                    ->orWhere('lastname', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%")
                    ->orWhere('position', 'like', "%{$search}%")
                    ->orWhere('employee_id', 'like', "%{$search}%")
                    ->orWhere('qrcode', 'like', "%{$search}%");
            });
        }

        foreach (['employee_department' => 'department', 'employee_position' => 'position'] as $input => $column) {
            if ($request->filled($input)) {
                $employeeQuery->where($column, $request->input($input));
            }
        }

        return view('attendance_patrons.index', [
            'activeTab' => $request->query('tab', 'students'),
            'students' => $studentQuery->orderBy('lastname')->paginate(12, ['*'], 'students_page')->withQueryString(),
            'employees' => $employeeQuery->orderBy('lastname')->paginate(12, ['*'], 'employees_page')->withQueryString(),
            'pendingStudents' => AttendancePendingStudent::query()->latest()->paginate(8, ['*'], 'pending_students_page')->withQueryString(),
            'pendingEmployees' => AttendancePendingEmployee::query()->latest()->paginate(8, ['*'], 'pending_employees_page')->withQueryString(),
            'studentCount' => AttendanceStudent::count(),
            'employeeCount' => AttendanceEmployee::count(),
            'pendingStudentCount' => AttendancePendingStudent::count(),
            'pendingEmployeeCount' => AttendancePendingEmployee::count(),
            'programs' => AttendanceProgram::query()->orderBy('program_name')->get(),
            'studentYears' => AttendanceStudent::query()->whereNotNull('year')->distinct()->orderBy('year')->pluck('year'),
            'educationalLevels' => AttendanceStudent::query()->whereNotNull('educational_level')->distinct()->orderBy('educational_level')->pluck('educational_level'),
            'departments' => AttendanceEmployee::query()->whereNotNull('department')->distinct()->orderBy('department')->pluck('department'),
            'positions' => AttendanceEmployee::query()->whereNotNull('position')->distinct()->orderBy('position')->pluck('position'),
        ]);
    }

    public function pendingStudents(): View
    {
        return view('attendance_patrons.pending_students', [
            'pendingStudents' => AttendancePendingStudent::query()->latest()->paginate(12)->withQueryString(),
            'pendingStudentCount' => AttendancePendingStudent::count(),
            'studentCount' => AttendanceStudent::count(),
        ]);
    }

    public function pendingEmployees(): View
    {
        return view('attendance_patrons.pending_employees', [
            'pendingEmployees' => AttendancePendingEmployee::query()->latest()->paginate(12)->withQueryString(),
            'pendingEmployeeCount' => AttendancePendingEmployee::count(),
            'employeeCount' => AttendanceEmployee::count(),
        ]);
    }

    public function createStudent(): View
    {
        return view('attendance_patrons.student_form', [
            'student' => new AttendanceStudent,
            'programs' => AttendanceProgram::query()->orderBy('program_name')->get(),
            'mode' => 'create',
        ]);
    }

    public function storeStudent(Request $request): RedirectResponse
    {
        $validated = $this->validateStudent($request);
        $validated['qrcode'] = empty($validated['qrcode']) ? $this->nextCode(AttendanceStudent::class, 'S') : $validated['qrcode'];
        $this->storeUploads($request, $validated, 'profile_picture', 'images/attendance/profile_pictures');
        $this->storeBase64Signature($validated, 'student_signature', 'images/attendance/student_signatures');

        AttendanceStudent::query()->create($validated);

        return redirect()->route('attendance.pending.index', ['tab' => 'students'])->with('success', 'Attendance student added.');
    }

    public function editStudent(int $student): View
    {
        return view('attendance_patrons.student_form', [
            'student' => AttendanceStudent::query()->findOrFail($student),
            'programs' => AttendanceProgram::query()->orderBy('program_name')->get(),
            'mode' => 'edit',
        ]);
    }

    public function updateStudent(Request $request, int $student): RedirectResponse
    {
        $record = AttendanceStudent::query()->findOrFail($student);
        $validated = $this->validateStudent($request, $record->id);
        unset($validated['qrcode']);
        $this->storeUploads($request, $validated, 'profile_picture', 'images/attendance/profile_pictures');
        $this->storeBase64Signature($validated, 'student_signature', 'images/attendance/student_signatures');
        $record->update($validated);

        return redirect()->route('attendance.pending.index', ['tab' => 'students'])->with('success', 'Attendance student updated.');
    }

    public function destroyStudent(int $student): RedirectResponse
    {
        AttendanceStudent::query()->findOrFail($student)->delete();

        return back()->with('success', 'Attendance student deleted.');
    }

    public function createEmployee(): View
    {
        return view('attendance_patrons.employee_form', [
            'employee' => new AttendanceEmployee,
            'mode' => 'create',
        ]);
    }

    public function storeEmployee(Request $request): RedirectResponse
    {
        $validated = $this->validateEmployee($request);
        $validated['qrcode'] = empty($validated['qrcode']) ? $this->nextCode(AttendanceEmployee::class, 'E') : $validated['qrcode'];
        $this->storeUploads($request, $validated, 'formal_picture', 'images/attendance/formal_pictures');
        $this->storeBase64Signature($validated, 'employee_signature', 'images/attendance/employee_signatures');

        AttendanceEmployee::query()->create($validated);

        return redirect()->route('attendance.pending.index', ['tab' => 'employees'])->with('success', 'Attendance employee added.');
    }

    public function editEmployee(int $employee): View
    {
        return view('attendance_patrons.employee_form', [
            'employee' => AttendanceEmployee::query()->findOrFail($employee),
            'mode' => 'edit',
        ]);
    }

    public function updateEmployee(Request $request, int $employee): RedirectResponse
    {
        $record = AttendanceEmployee::query()->findOrFail($employee);
        $validated = $this->validateEmployee($request, $record->id);
        unset($validated['qrcode']);
        $this->storeUploads($request, $validated, 'formal_picture', 'images/attendance/formal_pictures');
        $this->storeBase64Signature($validated, 'employee_signature', 'images/attendance/employee_signatures');
        $record->update($validated);

        return redirect()->route('attendance.pending.index', ['tab' => 'employees'])->with('success', 'Attendance employee updated.');
    }

    public function destroyEmployee(int $employee): RedirectResponse
    {
        AttendanceEmployee::query()->findOrFail($employee)->delete();

        return back()->with('success', 'Attendance employee deleted.');
    }

    public function approveStudent(int $id, AdminActivityLogger $activities): RedirectResponse
    {
        DB::transaction(function () use ($activities, $id): void {
            $pending = AttendancePendingStudent::query()->findOrFail($id);
            $student = AttendanceStudent::query()->create(array_merge($pending->only([
                'student_id', 'lastname', 'firstname', 'middle_initial', 'birth_date',
                'educational_level', 'blood_type', 'course', 'year', 'mobile_number',
                'address', 'emergency_person', 'emergency_relationship', 'emergency_number',
                'emergency_address', 'profile_picture', 'student_signature',
            ]), [
                'qrcode' => $pending->qrcode ?: $this->nextCode(AttendanceStudent::class, 'S'),
            ]));
            $pending->delete();

            $activities->log('attendance', 'patron.approved', 'Attendance student approved', $student->student_id, $student);
        });

        return back()->with('success', 'Attendance student approved.');
    }

    public function rejectStudent(int $id, AdminActivityLogger $activities): RedirectResponse
    {
        $pending = AttendancePendingStudent::query()->findOrFail($id);
        $label = $pending->student_id;
        $pending->delete();
        $activities->log('attendance', 'patron.rejected', 'Attendance student rejected', $label);

        return back()->with('success', 'Attendance student rejected.');
    }

    public function approveEmployee(int $id, AdminActivityLogger $activities): RedirectResponse
    {
        DB::transaction(function () use ($activities, $id): void {
            $pending = AttendancePendingEmployee::query()->findOrFail($id);
            $employee = AttendanceEmployee::query()->create(array_merge($pending->only([
                'employee_id', 'employee_number', 'firstname', 'lastname', 'middle_initial',
                'department', 'position', 'birth_date', 'mobile_number', 'sex',
                'civil_status', 'blood_type', 'tin_id_number', 'philhealth_number',
                'sss_number', 'hdmf_number', 'formal_picture', 'emergency_contact_name',
                'emergency_contact_relationship', 'emergency_contact_number', 'address',
                'emergency_address', 'employee_signature',
            ]), [
                'qrcode' => $pending->qrcode ?: $this->nextCode(AttendanceEmployee::class, 'E'),
            ]));
            $pending->delete();

            $activities->log('attendance', 'patron.approved', 'Attendance employee approved', $employee->employee_id, $employee);
        });

        return back()->with('success', 'Attendance employee approved.');
    }

    public function rejectEmployee(int $id, AdminActivityLogger $activities): RedirectResponse
    {
        $pending = AttendancePendingEmployee::query()->findOrFail($id);
        $label = $pending->employee_id ?: $pending->employee_number;
        $pending->delete();
        $activities->log('attendance', 'patron.rejected', 'Attendance employee rejected', $label);

        return back()->with('success', 'Attendance employee rejected.');
    }

    public function exportStudents()
    {
        return $this->csvDownload('attendance_students_'.now()->format('Ymd_His').'.csv', $this->studentColumns, AttendanceStudent::query()->orderBy('lastname')->get());
    }

    public function exportEmployees()
    {
        return $this->csvDownload('attendance_employees_'.now()->format('Ymd_His').'.csv', $this->employeeColumns, AttendanceEmployee::query()->orderBy('lastname')->get());
    }

    public function studentTemplate()
    {
        return $this->csvDownload('attendance_student_import_template.csv', $this->studentColumns, collect());
    }

    public function employeeTemplate()
    {
        return $this->csvDownload('attendance_employee_import_template.csv', $this->employeeColumns, collect());
    }

    public function importStudents(Request $request): RedirectResponse
    {
        $request->validate(['file' => ['required', 'file', 'mimes:csv,txt,xlsx']]);
        foreach (SimpleExcelReader::create($request->file('file')->getRealPath())->getRows() as $row) {
            $data = $this->onlyKnownColumns($row, $this->studentColumns);
            if (empty($data['student_id'])) {
                continue;
            }
            $data['qrcode'] = $data['qrcode'] ?? $this->nextCode(AttendanceStudent::class, 'S');
            AttendanceStudent::query()->updateOrCreate(['student_id' => $data['student_id']], $data);
        }

        return back()->with('success', 'Attendance students imported.');
    }

    public function importEmployees(Request $request): RedirectResponse
    {
        $request->validate(['file' => ['required', 'file', 'mimes:csv,txt,xlsx']]);
        foreach (SimpleExcelReader::create($request->file('file')->getRealPath())->getRows() as $row) {
            $data = $this->onlyKnownColumns($row, $this->employeeColumns);
            if (empty($data['employee_id'])) {
                continue;
            }
            $data['qrcode'] = $data['qrcode'] ?? $this->nextCode(AttendanceEmployee::class, 'E');
            AttendanceEmployee::query()->updateOrCreate(['employee_id' => $data['employee_id']], $data);
        }

        return back()->with('success', 'Attendance employees imported.');
    }

    public function studentIdCard(int $student): View
    {
        return view('attendance_patrons.id_card', [
            'type' => 'Student',
            'person' => AttendanceStudent::query()->findOrFail($student),
            'identifier' => 'student_id',
            'photo' => 'profile_picture',
        ]);
    }

    public function employeeIdCard(int $employee): View
    {
        return view('attendance_patrons.id_card', [
            'type' => 'Employee',
            'person' => AttendanceEmployee::query()->findOrFail($employee),
            'identifier' => 'employee_id',
            'photo' => 'formal_picture',
        ]);
    }

    public function downloadStudentIdCard(int $student)
    {
        $record = AttendanceStudent::query()->findOrFail($student);

        return response(view('attendance_patrons.id_card', [
            'type' => 'Student',
            'person' => $record,
            'identifier' => 'student_id',
            'photo' => 'profile_picture',
        ])->render(), 200, [
            'Content-Type' => 'text/html',
            'Content-Disposition' => 'attachment; filename="attendance-student-'.$record->id.'-id.html"',
        ]);
    }

    public function downloadEmployeeIdCard(int $employee)
    {
        $record = AttendanceEmployee::query()->findOrFail($employee);

        return response(view('attendance_patrons.id_card', [
            'type' => 'Employee',
            'person' => $record,
            'identifier' => 'employee_id',
            'photo' => 'formal_picture',
        ])->render(), 200, [
            'Content-Type' => 'text/html',
            'Content-Disposition' => 'attachment; filename="attendance-employee-'.$record->id.'-id.html"',
        ]);
    }

    public function bulkStudentIds()
    {
        return $this->bulkCards(AttendanceStudent::query()->orderBy('lastname')->get(), 'student', 'student_id', 'profile_picture');
    }

    public function bulkEmployeeIds()
    {
        return $this->bulkCards(AttendanceEmployee::query()->orderBy('lastname')->get(), 'employee', 'employee_id', 'formal_picture');
    }

    private function validateStudent(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'student_id' => ['required', 'string', 'max:255', 'unique:attendance_students,student_id'.($ignoreId ? ','.$ignoreId : '')],
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'middle_initial' => ['nullable', 'string', 'max:32'],
            'birth_date' => ['nullable', 'date'],
            'educational_level' => ['nullable', 'string', 'max:255'],
            'course' => ['nullable', 'string', 'max:255'],
            'year' => ['nullable', 'string', 'max:255'],
            'mobile_number' => ['nullable', 'string', 'max:32'],
            'address' => ['nullable', 'string'],
            'blood_type' => ['nullable', 'string', 'max:10'],
            'emergency_person' => ['nullable', 'string', 'max:255'],
            'emergency_relationship' => ['nullable', 'string', 'max:255'],
            'emergency_number' => ['nullable', 'string', 'max:32'],
            'emergency_address' => ['nullable', 'string'],
            'profile_picture' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:4096'],
            'student_signature' => ['nullable', 'string'],
            'qrcode' => ['nullable', 'string', 'max:255', 'unique:attendance_students,qrcode'.($ignoreId ? ','.$ignoreId : '')],
        ]);
    }

    private function validateEmployee(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'employee_id' => ['required', 'string', 'max:255', 'unique:attendance_employees,employee_id'.($ignoreId ? ','.$ignoreId : '')],
            'employee_number' => ['nullable', 'string', 'max:255', 'unique:attendance_employees,employee_number'.($ignoreId ? ','.$ignoreId : '')],
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'middle_initial' => ['nullable', 'string', 'max:32'],
            'department' => ['nullable', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'mobile_number' => ['nullable', 'string', 'max:32'],
            'sex' => ['nullable', 'string', 'max:20'],
            'civil_status' => ['nullable', 'string', 'max:50'],
            'blood_type' => ['nullable', 'string', 'max:10'],
            'tin_id_number' => ['nullable', 'string', 'max:255'],
            'philhealth_number' => ['nullable', 'string', 'max:255'],
            'sss_number' => ['nullable', 'string', 'max:255'],
            'hdmf_number' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_relationship' => ['nullable', 'string', 'max:255'],
            'emergency_contact_number' => ['nullable', 'string', 'max:32'],
            'emergency_address' => ['nullable', 'string'],
            'formal_picture' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:4096'],
            'employee_signature' => ['nullable', 'string'],
            'qrcode' => ['nullable', 'string', 'max:255', 'unique:attendance_employees,qrcode'.($ignoreId ? ','.$ignoreId : '')],
        ]);
    }

    private function nextCode(string $modelClass, string $prefix): string
    {
        $last = $modelClass::query()
            ->whereNotNull('qrcode')
            ->where('qrcode', 'like', $prefix.'-%')
            ->orderByDesc('id')
            ->value('qrcode');

        $next = 1;
        if ($last && preg_match('/'.preg_quote($prefix, '/').'-(\d+)/', $last, $matches)) {
            $next = (int) $matches[1] + 1;
        }

        return $prefix.'-'.str_pad((string) $next, 8, '0', STR_PAD_LEFT);
    }

    private function storeUploads(Request $request, array &$validated, string $field, string $directory): void
    {
        if (! $request->hasFile($field)) {
            unset($validated[$field]);

            return;
        }

        $file = $request->file($field);
        $filename = time().'_'.Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)).'.'.$file->getClientOriginalExtension();
        $destination = public_path($directory);
        if (! is_dir($destination)) {
            mkdir($destination, 0755, true);
        }
        $file->move($destination, $filename);
        $validated[$field] = $directory.'/'.$filename;
    }

    private function storeBase64Signature(array &$validated, string $field, string $directory): void
    {
        if (empty($validated[$field]) || ! str_starts_with($validated[$field], 'data:')) {
            if (empty($validated[$field])) {
                unset($validated[$field]);
            }

            return;
        }

        [$meta, $contents] = explode(',', $validated[$field], 2);
        $extension = preg_match('/jpeg|jpg/i', $meta) ? 'jpg' : 'png';
        $filename = time().'_'.$field.'.'.$extension;
        $destination = public_path($directory);
        if (! is_dir($destination)) {
            mkdir($destination, 0755, true);
        }
        file_put_contents($destination.DIRECTORY_SEPARATOR.$filename, base64_decode($contents));
        $validated[$field] = $directory.'/'.$filename;
    }

    private function onlyKnownColumns(iterable $row, array $columns): array
    {
        $normal = [];
        foreach ($row as $key => $value) {
            $normal[Str::snake(trim((string) $key))] = is_string($value) ? trim($value) : $value;
        }

        return collect($columns)
            ->mapWithKeys(fn (string $column): array => [$column => $normal[$column] ?? null])
            ->filter(fn ($value): bool => $value !== null && $value !== '')
            ->all();
    }

    private function csvDownload(string $filename, array $columns, $rows)
    {
        return response()->streamDownload(function () use ($columns, $rows): void {
            $output = fopen('php://output', 'w');
            fputcsv($output, $columns);
            foreach ($rows as $row) {
                fputcsv($output, collect($columns)->map(fn (string $column) => $row->{$column} ?? null)->all());
            }
            fclose($output);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    private function bulkCards($records, string $type, string $identifier, string $photo)
    {
        $zipPath = storage_path('app/attendance_'.$type.'_ids_'.time().'.zip');
        $zip = new ZipArchive;

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return back()->with('error', 'Unable to create ID card ZIP.');
        }

        foreach ($records as $record) {
            $html = view('attendance_patrons.id_card', [
                'type' => ucfirst($type),
                'person' => $record,
                'identifier' => $identifier,
                'photo' => $photo,
            ])->render();
            $name = Str::slug(($record->lastname ?? 'patron').'-'.($record->firstname ?? $record->id)).'-id.html';
            $zip->addFromString($name, $html);
        }

        $zip->close();

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }
}
