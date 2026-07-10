@extends('layouts.sidebar')

@section('title', $title)

@section('header')
    @include('dashboards.partials.page-header')
@endsection

@section('content')
    <div class="dashboard-shell">
        <section class="dashboard-section">
            @include('dashboards.partials.section-header', [
                'kicker' => 'Attendance desk',
                'title' => 'Today’s scan snapshot',
                'description' => 'Current scan activity, in-building status, and feedback signals.',
                'meta' => 'Attendance Staff',
            ])
            <div class="dashboard-stat-grid">
                @foreach ($stats as $stat)
                    @include('dashboards.partials.stat-card', ['stat' => $stat])
                @endforeach
            </div>
        </section>

        <section class="dashboard-section">
            @include('dashboards.partials.section-header', [
                'kicker' => 'Work queue',
                'title' => 'Hourly flow and recent scans',
                'description' => 'A quick view of scanning volume and the latest patron movement.',
            ])
            <div class="dashboard-content-grid">
                @include('dashboards.partials.chart-card', ['chart' => $charts[0]])
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
