@extends('layouts.sec')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/books/index.css') }}">
    <style>
        .openlibrary-review .marc-field .form-label { font-size: 0.9rem; }
        .openlibrary-review .card-header { font-size: 0.95rem; }
    </style>
@endsection

@section('content')
<div class="openlibrary-review">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <h2 class="mb-0">Review Book Record</h2>
        <a href="{{ route('catalog.copy.openlibrary.form', !empty($isbnQuery) ? ['isbn' => $isbnQuery] : []) }}" class="btn btn-outline-secondary btn-sm">New ISBN search</a>
    </div>

    @php($sourceLabel = ($catalogSource ?? 'openlibrary') === 'googlebooks' ? 'Google Books' : 'Open Library')
    <p class="text-muted small mb-3">
        Fields follow your <strong>Books</strong> MARC catalog framework. Data came from <strong>{{ $sourceLabel }}</strong>. If a cover was returned, it is shown below and will be saved when you submit (unless you upload a different image).
    </p>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($frameworkFields->isEmpty())
        <div class="alert alert-warning">
            No visible fields found for the <strong>Books</strong> catalog framework. Seed or configure frameworks under Admin → MARC catalog frameworks.
        </div>
    @endif

    @if(!empty($record['cover_image']))
        <div class="mb-4">
            <div class="fw-semibold small text-muted mb-2">Cover ({{ $sourceLabel }})</div>
            <img src="{{ $record['cover_image'] }}" alt="Book cover" class="img-thumbnail d-block" style="max-width: 200px;">
        </div>
    @endif

    <form id="addBookForm" method="POST" action="{{ route('book.store') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="catalog_source" value="{{ old('catalog_source', $catalogSource ?? 'openlibrary') }}">
        <input type="hidden" name="openlibrary_return_isbn" value="{{ $isbnQuery }}">
        @if(!empty($record['cover_image']))
            <input type="hidden" name="external_cover_url" value="{{ old('external_cover_url', $record['cover_image']) }}">
        @else
            <input type="hidden" name="external_cover_url" value="{{ old('external_cover_url', '') }}">
        @endif

        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-light fw-semibold py-2">Bibliographic record</div>
            <div class="card-body">
                @include('books.partials.marc_editor', ['frameworkFields' => $frameworkFields])
            </div>
        </div>

        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-light fw-semibold py-2">Programs &amp; course</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Program (optional)</label>
                        <p class="text-muted small mb-1">If you attach programs, courses load from the Prospectus.</p>
                        <div id="program-container">
                            <div class="program-row">
                                <select name="program_ids[]" class="form-control mb-2 program-select">
                                    <option value="">-- Select Program --</option>
                                    @foreach($programs as $program)
                                        <option value="{{ $program->id }}">{{ $program->program_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <button type="button" id="add-program-btn" class="btn btn-sm btn-outline-secondary mt-1">Add more program</button>
                    </div>
                    <div class="col-md-6">
                        <label for="year" class="form-label">996 ‡e (Year)</label>
                        <input type="text" name="year" id="year" class="form-control" placeholder="Enter year"
                            value="{{ old('year', $record['year'] ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label for="book_course" class="form-label">650 ‡a (Course)</label>
                        <select name="course" id="book_course" class="form-control" disabled>
                            <option value="">-- Select program(s) first --</option>
                            @if(old('course'))
                                <option value="{{ old('course') }}" selected>{{ old('course') }}</option>
                            @endif
                        </select>
                    </div>
                    <div class="col-12">
                        <label for="cover_image" class="form-label">856 — Cover image (optional upload)</label>
                        <p class="text-muted small mb-1">Leave empty to use the {{ $sourceLabel }} cover above.</p>
                        <input type="file" name="cover_image" id="cover_image" class="form-control" accept="image/jpeg,image/png,image/gif,image/webp">
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-2 mb-4 flex-wrap gap-2">
            <a href="{{ route('catalog.copy.openlibrary.form') }}" class="btn btn-secondary">Go back</a>
            <button type="submit" class="btn btn-success">Save book</button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('addBookForm');
        if (!form) return;
        form.addEventListener('keydown', e => {
            if (e.key === 'Enter') {
                e.preventDefault();
                const inputs = [...form.querySelectorAll('input')];
                const index = inputs.indexOf(document.activeElement);
                if (inputs[index + 1]) inputs[index + 1].focus();
            }
        });
    });
</script>

@include('books.partials.catalog_courses_script')

<script>
    const programs = @json($programs);
    const container = document.getElementById('program-container');
    const addBtn = document.getElementById('add-program-btn');

    function refreshOptions() {
        const selectedValues = Array.from(document.querySelectorAll('.program-select'))
            .map(sel => sel.value)
            .filter(v => v);

        document.querySelectorAll('.program-select').forEach(select => {
            const currentVal = select.value;
            Array.from(select.options).forEach(opt => {
                if (opt.value && selectedValues.includes(opt.value) && opt.value !== currentVal) {
                    opt.hidden = true;
                } else {
                    opt.hidden = false;
                }
            });
        });
    }

    if (addBtn && container) {
        addBtn.addEventListener('click', () => {
            const row = document.createElement('div');
            row.classList.add('program-row', 'd-flex', 'align-items-center', 'mb-2');

            const select = document.createElement('select');
            select.name = "program_ids[]";
            select.classList.add('form-control', 'program-select', 'me-2');

            const defaultOption = document.createElement('option');
            defaultOption.value = "";
            defaultOption.textContent = "-- Select Program --";
            select.appendChild(defaultOption);

            programs.forEach(program => {
                const option = document.createElement('option');
                option.value = program.id;
                option.textContent = program.program_name;
                select.appendChild(option);
            });

            const removeBtn = document.createElement('button');
            removeBtn.type = "button";
            removeBtn.textContent = "Remove";
            removeBtn.classList.add('btn', 'btn-sm', 'btn-danger', 'remove-program');

            row.appendChild(select);
            row.appendChild(removeBtn);
            container.appendChild(row);

            refreshOptions();
            if (typeof window.refreshBookCourseOptions === 'function') {
                window.refreshBookCourseOptions();
            }
        });

        container.addEventListener('click', (e) => {
            if (e.target.classList.contains('remove-program')) {
                e.target.closest('.program-row').remove();
                refreshOptions();
                if (typeof window.refreshBookCourseOptions === 'function') {
                    window.refreshBookCourseOptions();
                }
            }
        });

        container.addEventListener('change', (e) => {
            if (e.target.classList.contains('program-select')) {
                refreshOptions();
            }
        });

        refreshOptions();
    }
</script>
@endsection
