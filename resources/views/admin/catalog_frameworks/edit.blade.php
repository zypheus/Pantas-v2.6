@extends('layouts.sidebar')

@section('content')
    <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-0">Framework: <strong>{{ $catalog_framework->name }}</strong></h1>
            <p class="text-muted small mb-0">Toggle visibility, required, order, defaults, and column mapping.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.catalog_frameworks.index') }}" class="btn btn-outline-secondary btn-sm">All frameworks</a>
            <a href="{{ route('book.index') }}" class="btn btn-outline-secondary btn-sm">Books</a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="framework-save" method="POST" action="{{ route('admin.catalog_frameworks.fields.update', $catalog_framework) }}">
        @csrf
        @method('PUT')
    </form>

    @foreach ($catalog_framework->fields as $ff)
        <form id="detach-{{ $ff->id }}" method="POST" action="{{ route('admin.catalog_frameworks.fields.detach', [$catalog_framework, $ff]) }}">
            @csrf
            @method('DELETE')
        </form>
    @endforeach

    <div class="table-responsive mb-3">
        <table class="table table-bordered table-sm align-middle">
            <thead class="table-light">
                <tr>
                    <th>Tag</th>
                    <th>Label</th>
                    <th>Widget</th>
                    <th class="text-center">Visible</th>
                    <th class="text-center">Required</th>
                    <th>Sort</th>
                    <th>Default</th>
                    <th>Maps to column</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($catalog_framework->fields as $ff)
                    @php $mf = $ff->marcField; @endphp
                    @if (!$mf)
                        @continue
                    @endif
                    <tr>
                        <td>
                            <code>{{ $mf->tag }}{{ $mf->subfield ? ' ‡'.$mf->subfield : '' }}</code>
                        </td>
                        <td>{{ $mf->label }}</td>
                        <td class="small">{{ $mf->input_type }}{{ $mf->repeatable ? ' · repeatable' : '' }}</td>
                        <td class="text-center">
                            <input type="hidden" form="framework-save" name="fields[{{ $ff->id }}][visible]" value="0">
                            <input class="form-check-input" type="checkbox" form="framework-save" name="fields[{{ $ff->id }}][visible]" value="1" @checked($ff->visible)>
                        </td>
                        <td class="text-center">
                            <input type="hidden" form="framework-save" name="fields[{{ $ff->id }}][required]" value="0">
                            <input class="form-check-input" type="checkbox" form="framework-save" name="fields[{{ $ff->id }}][required]" value="1" @checked($ff->required)>
                        </td>
                        <td style="width:6rem;">
                            <input type="number" form="framework-save" class="form-control form-control-sm" name="fields[{{ $ff->id }}][sort_order]" value="{{ old('fields.'.$ff->id.'.sort_order', $ff->sort_order) }}" min="0">
                        </td>
                        <td>
                            <input type="text" form="framework-save" class="form-control form-control-sm" name="fields[{{ $ff->id }}][default_value]" value="{{ old('fields.'.$ff->id.'.default_value', $ff->default_value) }}">
                        </td>
                        <td>
                            <select form="framework-save" class="form-select form-select-sm" name="fields[{{ $ff->id }}][book_column]">
                                <option value="">— none —</option>
                                @foreach ($bookColumns as $col)
                                    <option value="{{ $col }}" @selected(old('fields.'.$ff->id.'.book_column', $ff->book_column) === $col)>{{ $col }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="text-nowrap">
                            <button type="submit" class="btn btn-sm btn-outline-danger" form="detach-{{ $ff->id }}"
                                onclick="return confirm('Remove this tag from the framework?');">Remove</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <button type="submit" class="btn btn-primary mb-4" form="framework-save">Save changes</button>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">Add existing tag</div>
                <div class="card-body">
                    @if ($availableMarcFields->isEmpty())
                        <p class="text-muted small mb-0">All defined tags are already in this framework.</p>
                    @else
                        <form method="POST" action="{{ route('admin.catalog_frameworks.fields.attach', $catalog_framework) }}">
                            @csrf
                            <label class="form-label small">MARC field</label>
                            <select name="marc_field_id" class="form-select form-select-sm mb-2" required>
                                @foreach ($availableMarcFields as $mf)
                                    <option value="{{ $mf->id }}">
                                        {{ $mf->tag }}{{ $mf->subfield ? ' ‡'.$mf->subfield : '' }}
                                        @if ($mf->label) — {{ $mf->label }} @endif
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-sm btn-success">Add to framework</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">Define new tag (3-digit) and add</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.catalog_frameworks.marc_fields.store', $catalog_framework) }}">
                        @csrf
                        <div class="row g-2">
                            <div class="col-4">
                                <label class="form-label small">Tag</label>
                                <input type="text" name="tag" class="form-control form-control-sm" maxlength="3" pattern="[0-9]{3}" placeholder="246" value="{{ old('tag') }}" required>
                            </div>
                            <div class="col-4">
                                <label class="form-label small">Subfield</label>
                                <input type="text" name="subfield" class="form-control form-control-sm" maxlength="1" placeholder="a" value="{{ old('subfield') }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label small">Label</label>
                                <input type="text" name="label" class="form-control form-control-sm" value="{{ old('label') }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label small">Input type</label>
                                <select name="input_type" id="newInputType" class="form-select form-select-sm">
                                    @foreach (['text', 'textarea', 'select', 'date', 'time', 'datetime'] as $t)
                                        <option value="{{ $t }}" @selected(old('input_type', 'text') === $t)>{{ $t }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12" id="optionsBlock" style="display:none;">
                                <label class="form-label small">Select options (one per line)</label>
                                <textarea name="options_lines" class="form-control form-control-sm" rows="4">{{ old('options_lines') }}</textarea>
                            </div>
                            <div class="col-12">
                                <input type="hidden" name="repeatable" value="0">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="repeatable" value="1" id="repChk" @checked(old('repeatable') === '1')>
                                    <label class="form-check-label small" for="repChk">Repeatable (650-style)</label>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-sm btn-success mt-3">Create &amp; add</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
(function () {
    const sel = document.getElementById('newInputType');
    const block = document.getElementById('optionsBlock');
    function sync() {
        if (!sel || !block) return;
        block.style.display = (sel.value === 'select') ? '' : 'none';
    }
    if (sel) sel.addEventListener('change', sync);
    sync();
})();
</script>
@endpush
