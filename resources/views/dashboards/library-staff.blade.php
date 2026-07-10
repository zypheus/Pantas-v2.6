@extends('layouts.sidebar')

@section('title', $title)

@section('header')
    @include('dashboards.partials.page-header')
@endsection

@section('content')
    <div class="dashboard-shell">
        <section class="dashboard-section">
            @include('dashboards.partials.section-header', [
                'kicker' => 'Library desk',
                'title' => 'Today’s service snapshot',
                'description' => 'Circulation, due dates, overdue items, and library traffic for daily desk work.',
                'meta' => 'Library Staff',
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
                'title' => 'Visits and circulation activity',
                'description' => 'A compact view of today’s movement and recent borrowing records.',
            ])
            <div class="dashboard-content-grid">
                @include('dashboards.partials.chart-card', ['chart' => $charts[0]])
                @include('dashboards.partials.recent-list', [
                    'title' => 'Recent Circulation',
                    'items' => $recent,
                    'empty' => 'No circulation activity yet.',
                ])
            </div>
        </section>
    </div>

    @include('dashboards.partials.chart-scripts')
@endsection
