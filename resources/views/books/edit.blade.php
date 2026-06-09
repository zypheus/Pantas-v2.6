@extends('layouts.sidebar')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/books/create.css') }}">
    <link rel="stylesheet" href="{{ asset('css/books/edit.css') }}">
    <link href="{{ asset('vendor/fontsource/martel-sans/latin-900.css') }}" rel="stylesheet">
@endsection

@section('content')

<div class="catalog-page">
    <header class="catalog-page__hero">
        <div>
            <h1 class="catalog-page__title">Edit Book</h1>
            <p class="catalog-page__subtitle">
                {{ $book->title_statement ?? 'Untitled' }}
                @if($book->main_author)
                    <span class="text-muted"> · {{ $book->main_author }}</span>
                @endif
            </p>
        </div>
        <a href="{{ route('book.index') }}" class="btn btn-back btn-sm">← Back to catalog</a>
    </header>

    @if ($errors->any())
        <div class="alert alert-danger catalog-alert" role="alert">
            <strong>Please fix the following:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="editBookForm" method="POST" action="{{ route('book.update', $book->id) }}"
          enctype="multipart/form-data" class="catalog-form">
        @csrf
        @method('PUT')

        <div class="catalog-tabs card">
            <ul class="nav nav-tabs catalog-tabs__nav" id="catalogMainTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="tab-bibliographic-btn"
                            data-bs-toggle="tab" data-bs-target="#tab-bibliographic"
                            type="button" role="tab" aria-controls="tab-bibliographic" aria-selected="true">
                        <span class="catalog-tabs__step">1</span>
                        Bibliographic record
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-programs-btn"
                            data-bs-toggle="tab" data-bs-target="#tab-programs"
                            type="button" role="tab" aria-controls="tab-programs" aria-selected="false">
                        <span class="catalog-tabs__step">2</span>
                        Programs &amp; course
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-cover-btn"
                            data-bs-toggle="tab" data-bs-target="#tab-cover"
                            type="button" role="tab" aria-controls="tab-cover" aria-selected="false">
                        <span class="catalog-tabs__step">3</span>
                        Cover image
                    </button>
                </li>
            </ul>

            <div class="tab-content catalog-tabs__content" id="catalogMainTabContent">
                <div class="tab-pane fade show active" id="tab-bibliographic" role="tabpanel"
                     aria-labelledby="tab-bibliographic-btn" tabindex="0">
                    <p class="catalog-tab-lead text-muted">
                        MARC cataloging fields — use the pills below to switch between field groups.
                    </p>
                    @include('books.partials.marc_editor', [
                        'frameworkFields' => $frameworkFields,
                        'marcValues' => $marcValues,
                        'grouped' => true,
                        'tabbed' => true,
                    ])
                </div>

                <div class="tab-pane fade" id="tab-programs" role="tabpanel"
                     aria-labelledby="tab-programs-btn" tabindex="0">
                    <p class="catalog-tab-lead text-muted">
                        Optional — link this title to prospectus programs and courses for discovery.
                    </p>
                    <div class="row g-3">
                        @include('books.partials.catalog_curriculum_field', ['curriculumValue' => $book->curriculum])
                        <div class="col-12">
                            <label class="form-label catalog-field-label">Program(s)</label>
                            <div id="program-container" class="program-stack">
                                @php
                                    $selectedProgramIds = collect(old('program_ids', $book->programs->pluck('id')->all()))
                                        ->map(fn ($id) => (int) $id)
                                        ->filter()
                                        ->values();
                                @endphp
                                @if($selectedProgramIds->isNotEmpty())
                                    @foreach($selectedProgramIds as $selectedId)
                                        <div class="program-row d-flex gap-2 align-items-start {{ !$loop->first ? 'mt-2' : '' }}">
                                            <select name="program_ids[]" class="form-control program-select flex-grow-1">
                                                <option value="">— Select program —</option>
                                                @foreach($programs as $p)
                                                    <option value="{{ $p->id }}"
                                                        {{ (int) $p->id === $selectedId ? 'selected' : '' }}>
                                                        {{ $p->program_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-program flex-shrink-0">Remove</button>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="program-row d-flex gap-2 align-items-start">
                                        <select name="program_ids[]" class="form-control program-select flex-grow-1">
                                            <option value="">— Select program —</option>
                                            @foreach($programs as $p)
                                                <option value="{{ $p->id }}">{{ $p->program_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                            </div>
                            <button type="button" id="add-program-btn" class="btn btn-sm btn-outline-secondary mt-2">
                                + Add another program
                            </button>
                        </div>

                        <div class="col-md-6">
                            <label for="year" class="form-label catalog-field-label">
                                <span class="catalog-field-tag">996 ‡e</span>
                                <span class="catalog-field-name">Year level</span>
                            </label>
                            <input type="text" name="year" id="year" class="form-control"
                                   value="{{ old('year', $book->year) }}" placeholder="e.g. 1st Year">
                        </div>

                        <div class="col-md-6">
                            <label for="book_course" class="form-label catalog-field-label">
                                <span class="catalog-field-tag">650 ‡a</span>
                                <span class="catalog-field-name">Course</span>
                            </label>
                            <select name="course" id="book_course" class="form-control"
                                {{ $selectedProgramIds->isEmpty() ? 'disabled' : '' }}>
                                <option value="">— Select program(s) first —</option>
                                @php $courseVal = old('course', $book->course); @endphp
                                @if($courseVal)
                                    <option value="{{ $courseVal }}" selected>{{ $courseVal }}</option>
                                @endif
                            </select>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="tab-cover" role="tabpanel"
                     aria-labelledby="tab-cover-btn" tabindex="0">
                    <p class="catalog-tab-lead text-muted">
                        MARC 856 — replace the cover image or leave unchanged.
                    </p>
                    <div class="catalog-cover-upload">
                        @if ($book->cover_image)
                            <div class="catalog-cover-preview mb-3">
                                <p class="small text-muted mb-2">Current cover</p>
                                <img src="{{ asset('storage/' . $book->cover_image) }}" alt="Current cover"
                                     class="catalog-cover-preview__img">
                            </div>
                        @endif
                        <label for="cover_image" class="form-label catalog-field-label">Upload new cover</label>
                        <input type="file" name="cover_image" id="cover_image" class="form-control"
                               accept="image/jpeg,image/png,image/jpg,image/webp,image/gif">
                        <p class="small text-muted mt-2 mb-0">JPG, PNG, or WebP · max 5 MB</p>
                    </div>
                </div>
            </div>
        </div>

        <footer class="catalog-page__actions">
            <a href="{{ route('book.index') }}" class="btn btn-back">Cancel</a>
            <div class="catalog-page__actions-nav d-flex gap-2">
                <button type="button" class="btn btn-outline-secondary" id="catalogTabPrev" disabled>← Previous</button>
                <button type="button" class="btn btn-outline-secondary" id="catalogTabNext">Next →</button>
                <button type="submit" class="btn btn-update">Update book</button>
            </div>
        </footer>
    </form>
</div>

@endsection

@section('scripts')
<script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
@include('books.partials.catalog_tabs_script', ['formId' => 'editBookForm'])
@include('books.partials.catalog_courses_script')
@include('books.partials.catalog_programs_script')
@include('books.partials.catalog_marc_pickers_script')
@endsection
