@extends('layouts.sidebar')

@section('title', 'Library Attendance Reports')

@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
            <div>
                <h1 class="h3 mb-1">Library Attendance Reports</h1>
                <p class="text-muted mb-0">Summary of library visit scan activity.</p>
            </div>
            <a class="btn btn-outline-secondary" href="{{ route('library.attendance.logs') }}">View Logs</a>
        </div>

        <div class="row g-3">
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="text-muted">Total Ins</div>
                        <div class="display-6">{{ $totalIns }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="text-muted">Total Outs</div>
                        <div class="display-6">{{ $totalOuts }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="text-muted">Today Ins</div>
                        <div class="display-6">{{ $todayIns }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
