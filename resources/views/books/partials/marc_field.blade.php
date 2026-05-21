@if($ff->marcField)
@php
    $mf = $ff->marcField;
    $tag = $mf->tag;
    $subKey = $mf->subfield ?? '_';
    $display = $tag . ($mf->subfield ? " ‡{$mf->subfield}" : "");
    $values = $marcValues[$tag][$subKey] ?? [];
    if (!is_array($values)) $values = [];
    ksort($values);
    $values = array_values($values);
    if (count($values) === 0) {
        $values = [$ff->default_value ?? ''];
    }
@endphp

<div class="col-md-6 marc-field" data-tag="{{ $tag }}" data-sub="{{ $subKey }}" data-repeatable="{{ $mf->repeatable ? '1' : '0' }}">
    <label class="form-label catalog-field-label">
        <span class="catalog-field-tag">{{ $display }}</span>
        @if($mf->label)
            <span class="catalog-field-name">{{ $mf->label }}</span>
        @endif
        @if($ff->required)
            <span class="text-danger">*</span>
        @endif
    </label>

    <div class="marc-values d-grid gap-2">
        @foreach($values as $idx => $val)
            @php $name = "marc[{$tag}][{$subKey}][]"; @endphp

            @if($mf->input_type === 'textarea')
                <textarea name="{{ $name }}" class="form-control catalog-textarea" rows="2" @if($ff->required) required @endif>{{ old("marc.$tag.$subKey.$idx", $val) }}</textarea>
            @elseif($mf->input_type === 'select')
                <select name="{{ $name }}" class="form-control" @if($ff->required) required @endif>
                    <option value="">— Select —</option>
                    @foreach(($mf->options ?? []) as $opt)
                        @php $optVal = is_array($opt) ? ($opt['value'] ?? '') : $opt; @endphp
                        @php $optLabel = is_array($opt) ? ($opt['label'] ?? $optVal) : $opt; @endphp
                        <option value="{{ $optVal }}" {{ old("marc.$tag.$subKey.$idx", $val) == $optVal ? 'selected' : '' }}>
                            {{ $optLabel }}
                        </option>
                    @endforeach
                </select>
            @elseif($mf->input_type === 'date')
                <input type="date" name="{{ $name }}" class="form-control" value="{{ old("marc.$tag.$subKey.$idx", $val) }}" @if($ff->required) required @endif>
            @else
                <input type="text" name="{{ $name }}" class="form-control" value="{{ old("marc.$tag.$subKey.$idx", $val) }}" @if($ff->required) required @endif>
            @endif
        @endforeach
    </div>

    @if($mf->repeatable)
        <button type="button" class="btn btn-sm btn-outline-secondary mt-2 marc-add-value">+ Add another</button>
    @endif

    @error("marc.$tag.$subKey")
        <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror
</div>
@endif
