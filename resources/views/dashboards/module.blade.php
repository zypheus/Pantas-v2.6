@extends('layouts.sidebar')

@section('title', $title)

@section('header')
    <div>
        <h1 class="h4 mb-1">{{ $title }}</h1>
        <p class="text-muted mb-0">{{ $summary }}</p>
    </div>
@endsection

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <p class="mb-3">
                Active module: <strong>{{ $module }}</strong>
            </p>

            <div class="d-flex gap-2 flex-wrap">
                @foreach (app(\App\Services\Auth\ModuleAccessService::class)->availableModules(auth()->user()) as $availableModule)
                    <form method="POST" action="{{ route('module.switch') }}">
                        @csrf
                        <input type="hidden" name="module" value="{{ $availableModule }}">
                        <button type="submit" class="btn btn-outline-primary btn-sm">
                            Switch to {{ str_replace('-', ' ', ucfirst($availableModule)) }}
                        </button>
                    </form>
                @endforeach
            </div>
        </div>
    </div>
@endsection
