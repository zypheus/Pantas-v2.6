@extends('layouts.sidebar')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/tailwind-build.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/attendance_logs/index.css') }}">
@endsection

@section('content')
    <div class="container mt-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h1 class="h4 mb-1">Attendance Absences</h1>
                <p class="text-muted mb-0">
                    Students with no IN scan on {{ \Carbon\Carbon::parse($date)->format('F j, Y') }}.
                </p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('attendance_logs.absences.export', request()->query()) }}" class="btn btn-success btn-sm">
                    <i class="bi bi-filetype-csv me-1" aria-hidden="true"></i>
                    Export CSV
                </a>
                <a href="{{ route('attendance_logs.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left-circle me-1" aria-hidden="true"></i>
                    Attendance Logs
                </a>
            </div>
        </div>

        <div class="mb-4 no-bg p-4">
            <form method="GET" class="flex flex-col md:flex-row flex-wrap gap-4 items-end">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                    <input type="date" name="date" value="{{ $date }}" class="border px-3 py-2 w-full">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, ID, program, year" class="border px-3 py-2 w-full">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Program</label>
                    <select name="course_code" class="border px-3 py-2 w-full">
                        <option value="">All Programs</option>
                        @foreach($courses as $course)
                            <option value="{{ $course }}" {{ request('course_code') == $course ? 'selected' : '' }}>
                                {{ $course }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Year Level</label>
                    <select name="year_level" class="border px-3 py-2 w-full">
                        <option value="">All Levels</option>
                        @foreach($years as $year)
                            <option value="{{ $year }}" {{ request('year_level') == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <button type="submit" class="btn-search">Search</button>
                    <a href="{{ route('attendance_logs.absences') }}" class="btn btn-outline-secondary">Clear</a>
                </div>
            </form>
        </div>

        <div class="alert alert-info">
            Absence is computed as no <strong>IN</strong> attendance scan for the selected date. OUT-only records do not count as present.
        </div>

        <div class="overflow-x-auto bg-white rounded shadow">
            <table class="w-full text-sm text-left table-auto">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="px-4 py-2">Student ID</th>
                        <th class="px-4 py-2">Last Name</th>
                        <th class="px-4 py-2">First Name</th>
                        <th class="px-4 py-2">Program</th>
                        <th class="px-4 py-2">Year Level</th>
                        <th class="px-4 py-2">Mobile Number</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($absences as $student)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-2">{{ $student->student_id ?? 'N/A' }}</td>
                            <td class="px-4 py-2">{{ $student->lastname }}</td>
                            <td class="px-4 py-2">{{ $student->firstname }}</td>
                            <td class="px-4 py-2">{{ $student->course ?? 'N/A' }}</td>
                            <td class="px-4 py-2">{{ $student->year ?? 'N/A' }}</td>
                            <td class="px-4 py-2">{{ $student->mobile_number ?? 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center px-4 py-6 text-gray-500">
                                No absences found for this date and filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $absences->links() }}
        </div>
    </div>
@endsection
