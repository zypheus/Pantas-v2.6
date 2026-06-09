@extends('layouts.sidebar')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/books/show.css') }}">
@endsection

@section('content')
@php
    $lastTransaction = $book->availability === 'Borrowed'
        ? $book->logs()->where('status', 'Checked Out')->latest()->first()
        : null;

    $coverUrl = filled($book->cover_image)
        ? asset('storage/' . $book->cover_image)
        : asset('images/defaultBook.png');

    $visibleSections = collect($marcDetailSections ?? []);
    $copyIdForCirculation = $book->copyIdentifierForCirculation();
@endphp

<div class="book-show">
    <header class="book-show__hero">
        <div class="book-show__hero-text">
            <p class="book-show__eyebrow">Bibliographic record</p>
            <h1 class="book-show__title">{{ $book->title_statement ?? $book->title ?? 'Untitled' }}</h1>
            @if(filled($book->main_author))
                <p class="book-show__author">{{ $book->main_author }}</p>
            @endif
        </div>
        <div class="book-show__hero-actions">
            <a href="{{ route('book.index') }}" class="btn btn-show-outline">← Catalog</a>
            <a href="{{ route('book.edit', $book->id) }}" class="btn btn-show-outline">Edit</a>
            @if($copyIdForCirculation)
                @if($book->availability === 'Available')
                    <a href="{{ route('logs.index', ['copy_identifier' => $copyIdForCirculation, 'status' => 'checked_out']) }}"
                       class="btn btn-show-primary">Check out</a>
                @else
                    <a href="{{ route('logs.index', [
                        'copy_identifier' => $copyIdForCirculation,
                        'status' => 'checked_in',
                        'patron_name' => $lastTransaction?->patron_name ?? '',
                    ]) }}" class="btn btn-show-primary">Check in</a>
                @endif
            @else
                <span class="btn btn-show-primary disabled" title="Add an accession number, barcode, or RFID in cataloging first">No copy ID</span>
            @endif
        </div>
    </header>

    <div class="row g-4 book-show__layout">
        <div class="col-12 col-lg-4">
            <div class="book-show__card book-show__card--cover">
                <a href="{{ $coverUrl }}" target="_blank" rel="noopener noreferrer" class="book-show__cover-link">
                    <img src="{{ $coverUrl }}" alt="Cover" class="book-show__cover-img">
                </a>
                <p class="book-show__cover-hint small text-muted mb-0">856 · Cover image</p>
            </div>

            <div class="book-show__card book-show__card--summary">
                <h2 class="book-show__card-title">Copy summary</h2>
                <dl class="book-show__facts">
                    <div class="book-show__fact">
                        <dt>Status</dt>
                        <dd>
                            @if($book->availability === 'Available')
                                <span class="book-show__badge book-show__badge--available">Available</span>
                            @else
                                <span class="book-show__badge book-show__badge--borrowed">Borrowed</span>
                            @endif
                        </dd>
                    </div>
                    @if(filled($book->call_number))
                    <div class="book-show__fact">
                        <dt>Call number</dt>
                        <dd><code class="book-show__code">{{ $book->call_number }}</code></dd>
                    </div>
                    @endif
                    @if(filled($book->accession_no))
                    <div class="book-show__fact">
                        <dt>Accession</dt>
                        <dd>{{ $book->accession_no }}</dd>
                    </div>
                    @endif
                    @if($copyIdForCirculation)
                    <div class="book-show__fact">
                        <dt>{{ $book->copyIdentifierTypeLabel() ?? 'Copy ID' }}</dt>
                        <dd><code>{{ $copyIdForCirculation }}</code></dd>
                    </div>
                    @endif
                    @if(filled($book->accession_no))
                    <div class="book-show__fact">
                        <dt>Accession</dt>
                        <dd>{{ $book->accession_no }}</dd>
                    </div>
                    @endif
                    @if(filled($book->barcode))
                    <div class="book-show__fact">
                        <dt>Barcode</dt>
                        <dd>{{ $book->barcode }}</dd>
                    </div>
                    @endif
                    @if(filled($book->rfid))
                    <div class="book-show__fact">
                        <dt>RFID</dt>
                        <dd>{{ $book->rfid }}</dd>
                    </div>
                    @endif
                    @if($book->programs && $book->programs->count() > 0)
                    <div class="book-show__fact">
                        <dt>Programs</dt>
                        <dd class="book-show__programs">
                            @foreach($book->programs as $program)
                                <span class="book-show__program-pill">{{ $program->program_name }}</span>
                            @endforeach
                        </dd>
                    </div>
                    @endif
                    @if($book->availability === 'Borrowed' && $lastTransaction)
                    <div class="book-show__fact">
                        <dt>Current borrower</dt>
                        <dd>{{ $lastTransaction->patron_name }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
        </div>

        <div class="col-12 col-lg-8">
            <div class="book-show__card book-show__card--marc">
                <h2 class="book-show__card-title">MARC catalog details</h2>
                <p class="book-show__card-lead text-muted">From the Books catalog framework. Empty fields are hidden.</p>

                @if($visibleSections->isEmpty())
                    <p class="text-muted mb-0">No additional bibliographic fields on file for this copy.</p>
                @else
                    <div class="accordion book-show__accordion" id="bookMarcAccordion">
                        @foreach($visibleSections as $index => $section)
                            <div class="accordion-item">
                                <h3 class="accordion-header">
                                    <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }}"
                                            type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#marcSection{{ $index }}"
                                            aria-expanded="{{ $index === 0 ? 'true' : 'false' }}"
                                            aria-controls="marcSection{{ $index }}">
                                        {{ $section['title'] }}
                                        <span class="book-show__section-count">{{ count($section['rows']) }} fields</span>
                                    </button>
                                </h3>
                                <div id="marcSection{{ $index }}"
                                     class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}"
                                     data-bs-parent="#bookMarcAccordion">
                                    <div class="accordion-body p-0">
                                        <dl class="book-show__marc-list">
                                            @foreach($section['rows'] as $row)
                                                <div class="book-show__marc-row">
                                                    <dt>
                                                        <span class="book-show__marc-tag">{{ $row['tag'] }}</span>
                                                        {{ $row['label'] }}
                                                    </dt>
                                                    <dd>{{ $row['value'] }}</dd>
                                                </div>
                                            @endforeach
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
