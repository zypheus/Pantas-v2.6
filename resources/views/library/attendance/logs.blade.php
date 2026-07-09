@extends('layouts.sidebar')

@section('title', 'Library Attendance Logs')

@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
            <div>
                <h1 class="h3 mb-1">Library Attendance Logs</h1>
                <p class="text-muted mb-0">Independent visit records for registered library patrons.</p>
            </div>
            <a class="btn btn-outline-primary" href="{{ route('library.attendance.reports') }}">Reports</a>
        </div>

        <form class="row g-2 mb-3" method="GET" action="{{ route('library.attendance.logs') }}">
            <div class="col-sm-4 col-md-3">
                <input class="form-control" type="date" name="from" value="{{ request('from') }}">
            </div>
            <div class="col-sm-4 col-md-3">
                <input class="form-control" type="date" name="to" value="{{ request('to') }}">
            </div>
            <div class="col-sm-4 col-md-2">
                <button class="btn btn-primary w-100" type="submit">Filter</button>
            </div>
        </form>

        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Patron</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Section</th>
                            <th>Scanned At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logs as $log)
                            @php($patron = $log->student ?: $log->employee)
                            <tr>
                                <td>{{ $patron?->firstname }} {{ $patron?->lastname }}</td>
                                <td>{{ $log->student ? 'Student' : 'Employee' }}</td>
                                <td><span class="badge text-bg-{{ $log->status === 'IN' ? 'success' : 'secondary' }}">{{ $log->status }}</span></td>
                                <td>{{ $log->section ?: 'General' }}</td>
                                <td>{{ optional($log->scanned_at)->format('Y-m-d h:i A') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No library attendance logs found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-3">
            {{ $logs->links() }}
        </div>
    </div>
@endsection
