@extends('layouts.sidebar')

@section('title', $title)

@section('header')
    @include('dashboards.partials.page-header')
@endsection

@section('content')
    <div class="dashboard-shell">
        <section class="dashboard-section">
            @include('dashboards.partials.section-header', [
                'kicker' => 'Attendance command',
                'title' => 'Campus attendance snapshot',
                'description' => 'Today’s scans, active patrons, registrations, and feedback signals.',
                'meta' => 'Attendance Admin',
            ])
            <div class="dashboard-stat-grid">
                @foreach ($stats as $stat)
                    @include('dashboards.partials.stat-card', ['stat' => $stat])
                @endforeach
            </div>
        </section>

        <section class="dashboard-section">
            @include('dashboards.partials.section-header', [
                'kicker' => 'Analytics',
                'title' => 'Attendance flow and status',
                'description' => 'Monitor scan trends and today’s IN/OUT balance.',
            ])
            <div class="dashboard-chart-grid">
                @foreach ($charts as $chart)
                    @include('dashboards.partials.chart-card', ['chart' => $chart])
                @endforeach
            </div>
        </section>

        <section class="dashboard-section">
            @include('dashboards.partials.section-header', [
                'kicker' => 'Activity',
                'title' => 'Recent scan activity',
                'description' => 'Latest patron movement through attendance checkpoints.',
            ])
            <div class="dashboard-content-grid dashboard-content-grid-single">
                @include('dashboards.partials.recent-list', [
                    'title' => 'Recent Attendance Scans',
                    'items' => $recent,
                    'empty' => 'No Attendance scans yet.',
                ])
            </div>
        </section>
    </div>

    @include('dashboards.partials.chart-scripts')
@endsection
