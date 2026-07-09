@extends('layouts.sidebar')

@section('title', $title)

@section('header')
    <div>
        <h1 class="h4 mb-1">{{ $title }}</h1>
        <p class="text-muted mb-0">{{ $summary }}</p>
    </div>
@endsection

@section('content')
    <div class="row g-3 mb-3">
        @foreach ($stats as $stat)
            @include('dashboards.partials.stat-card', ['stat' => $stat])
        @endforeach
    </div>

    <div class="row g-3">
        <div class="col-12 col-xl-5">
            @include('dashboards.partials.chart-card', ['chart' => $charts[0]])
        </div>
        <div class="col-12 col-xl-3">
            @include('dashboards.partials.quick-actions', ['actions' => $quickActions])
        </div>
        <div class="col-12 col-xl-4">
            @include('dashboards.partials.recent-list', [
                'title' => 'Recent Circulation',
                'items' => $recent,
                'empty' => 'No circulation activity yet.',
            ])
        </div>
    </div>

    @include('dashboards.partials.chart-scripts')
@endsection
