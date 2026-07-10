@extends('layouts.sidebar')

@section('title', $title)

@section('header')
    @include('dashboards.partials.page-header')
@endsection

@section('content')
    <div class="dashboard-shell">
        <section class="dashboard-section">
            @include('dashboards.partials.section-header', [
                'kicker' => 'System control',
                'title' => 'Operational snapshot',
                'description' => 'Current staff access, account status, and module coverage.',
                'meta' => 'Super Admin',
            ])
            <div class="dashboard-stat-grid">
                @foreach ($stats as $stat)
                    @include('dashboards.partials.stat-card', ['stat' => $stat])
                @endforeach
            </div>
        </section>

        <section class="dashboard-section">
            @include('dashboards.partials.section-header', [
                'kicker' => 'Intelligence',
                'title' => 'Staff distribution and recent decisions',
                'description' => 'A concise view of staffing structure and the latest administrative movement.',
            ])
            <div class="dashboard-content-grid dashboard-content-grid-featured">
                @include('dashboards.partials.chart-card', ['chart' => $charts[0]])
                @include('dashboards.partials.recent-list', [
                    'title' => 'Recent Admin Activity',
                    'items' => $recent,
                    'empty' => 'No admin activity has been recorded yet.',
                ])
            </div>
        </section>
    </div>

    @include('dashboards.partials.chart-scripts')
@endsection
