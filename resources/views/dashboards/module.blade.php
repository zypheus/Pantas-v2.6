@extends('layouts.sidebar')

@section('title', $title)

@section('header')
    @include('dashboards.partials.page-header')
@endsection

@section('content')
    <div class="dashboard-shell">
        <section class="dashboard-section">
            @include('dashboards.partials.section-header', [
                'kicker' => 'Module access',
                'title' => 'Active module: '.str_replace('-', ' ', ucfirst($module)),
                'description' => 'Switch between the administrative workspaces available to your account.',
            ])

            <div class="dashboard-card">
                <div class="dashboard-card-body">
                <div class="dashboard-action-list dashboard-action-list-inline">
                    @foreach (app(\App\Services\Auth\ModuleAccessService::class)->availableModules(auth()->user()) as $availableModule)
                        <form method="POST" action="{{ route('module.switch') }}">
                            @csrf
                            <input type="hidden" name="module" value="{{ $availableModule }}">
                            <button type="submit" class="dashboard-action-link dashboard-action-button">
                                <span class="dashboard-action-icon" aria-hidden="true">
                                    <i class="bi bi-arrow-left-right"></i>
                                </span>
                                <span>Switch to {{ str_replace('-', ' ', ucfirst($availableModule)) }}</span>
                            </button>
                        </form>
                    @endforeach
                </div>
                </div>
            </div>
        </section>
    </div>
@endsection
