@extends('layouts.sidebar')

@section('title', $title)

@section('header')
    @include('dashboards.partials.page-header')
@endsection

@section('content')
    <div class="dashboard-shell">
        <section class="dashboard-section">
            @include('dashboards.partials.section-header', [
                'kicker' => 'Library command',
                'title' => 'Collection and service snapshot',
                'description' => 'Books, patrons, rooms, fines, and library visit signals in one operational view.',
                'meta' => 'Library Admin',
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
                'title' => 'Borrowing and visit movement',
                'description' => 'Track circulation demand and physical library traffic over time.',
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
                'title' => 'Recent library operations',
                'description' => 'Latest circulation records and administrative updates.',
            ])
            <div class="dashboard-content-grid">
                @include('dashboards.partials.recent-list', [
                    'title' => 'Recent Circulation',
                    'items' => $recent,
                    'empty' => 'No circulation activity yet.',
                ])
                @include('dashboards.partials.recent-list', [
                    'title' => 'Recent Library Admin Activity',
                    'items' => $secondaryRecent,
                    'empty' => 'No Library admin activity yet.',
                ])
            </div>
        </section>
    </div>

    @include('dashboards.partials.chart-scripts')
@endsection
