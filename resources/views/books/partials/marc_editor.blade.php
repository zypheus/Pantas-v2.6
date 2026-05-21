@php
    /**
     * Expects:
     * - $frameworkFields (Collection<CatalogFrameworkField> with marcField loaded)
     * - $marcValues (array) optional for edit
     * - $grouped (bool) optional — group fields by MARC tag family
     * - $tabbed (bool) optional — when grouped, use pill tabs per MARC family
     */
    $marcValues = $marcValues ?? [];
    $grouped = $grouped ?? false;
    $tabbed = $tabbed ?? false;

    $groupTitles = [
        '0' => 'Identification',
        '1' => 'Authors',
        '2' => 'Title',
        '3' => 'Physical',
        '4' => 'Series',
        '5' => 'Notes',
        '6' => 'Subjects',
        '7' => 'Access',
        '8' => 'Digital',
        '9' => 'Local',
    ];

    $groupTitlesLong = [
        '0' => 'Identification & codes',
        '1' => 'Authors & contributors',
        '2' => 'Title & edition',
        '3' => 'Physical description',
        '4' => 'Series',
        '5' => 'Notes',
        '6' => 'Subjects & classification',
        '7' => 'Additional access points',
        '8' => 'Digital location',
        '9' => 'Local data',
    ];

    $fieldsByGroup = [];
    foreach ($frameworkFields as $ff) {
        if (!$ff->marcField) {
            continue;
        }
        $key = substr($ff->marcField->tag, 0, 1);
        $fieldsByGroup[$key][] = $ff;
    }
    ksort($fieldsByGroup);
    $firstGroupKey = array_key_first($fieldsByGroup);
@endphp

@if (!$grouped)
    <div class="row g-3" id="marcEditor">
        @foreach($frameworkFields as $ff)
            @include('books.partials.marc_field', ['ff' => $ff, 'marcValues' => $marcValues])
        @endforeach
    </div>
@elseif ($tabbed && count($fieldsByGroup) > 0)
    <div id="marcEditor" class="catalog-marc-tabs">
        <ul class="nav nav-pills catalog-marc-tabs__nav flex-wrap" role="tablist">
            @foreach($fieldsByGroup as $groupKey => $groupFields)
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $groupKey === $firstGroupKey ? 'active' : '' }}"
                            id="marc-tab-{{ $groupKey }}-btn"
                            data-bs-toggle="tab"
                            data-bs-target="#marc-tab-{{ $groupKey }}"
                            type="button"
                            role="tab"
                            aria-controls="marc-tab-{{ $groupKey }}"
                            aria-selected="{{ $groupKey === $firstGroupKey ? 'true' : 'false' }}">
                        <span class="catalog-marc-tabs__code">{{ $groupKey }}xx</span>
                        <span class="catalog-marc-tabs__label">{{ $groupTitles[$groupKey] ?? 'Other' }}</span>
                    </button>
                </li>
            @endforeach
        </ul>

        <div class="tab-content catalog-marc-tabs__panes">
            @foreach($fieldsByGroup as $groupKey => $groupFields)
                <div class="tab-pane fade {{ $groupKey === $firstGroupKey ? 'show active' : '' }}"
                     id="marc-tab-{{ $groupKey }}"
                     role="tabpanel"
                     aria-labelledby="marc-tab-{{ $groupKey }}-btn"
                     tabindex="0">
                    <p class="catalog-marc-tabs__intro text-muted small">
                        {{ $groupTitlesLong[$groupKey] ?? 'MARC fields' }} · {{ count($groupFields) }} {{ count($groupFields) === 1 ? 'field' : 'fields' }}
                    </p>
                    <div class="row g-3">
                        @foreach($groupFields as $ff)
                            @include('books.partials.marc_field', ['ff' => $ff, 'marcValues' => $marcValues])
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@else
    <div id="marcEditor" class="catalog-marc-grouped">
        @forelse($fieldsByGroup as $groupKey => $groupFields)
            <section class="catalog-panel">
                <header class="catalog-panel__head">
                    <span class="catalog-panel__badge" aria-hidden="true">{{ $groupKey }}xx</span>
                    <div>
                        <h2 class="catalog-panel__title">{{ $groupTitlesLong[$groupKey] ?? 'Other fields' }}</h2>
                        <p class="catalog-panel__hint">MARC {{ $groupKey }}XX field group</p>
                    </div>
                </header>
                <div class="catalog-panel__body row g-3">
                    @foreach($groupFields as $ff)
                        @include('books.partials.marc_field', ['ff' => $ff, 'marcValues' => $marcValues])
                    @endforeach
                </div>
            </section>
        @empty
            <p class="text-muted">No catalog fields configured. Add fields in MARC catalog frameworks.</p>
        @endforelse
    </div>
@endif

@push('scripts')
<script>
  document.addEventListener('click', function (e) {
    const btn = e.target.closest('.marc-add-value');
    if (!btn) return;
    const field = btn.closest('.marc-field');
    const valuesWrap = field.querySelector('.marc-values');
    const last = valuesWrap.querySelector('input, textarea, select');
    if (!last) return;

    const clone = last.cloneNode(true);
    if (clone.tagName === 'SELECT') {
      clone.selectedIndex = 0;
    } else {
      clone.value = '';
    }
    valuesWrap.appendChild(clone);
    clone.focus();
  });
</script>
@endpush
