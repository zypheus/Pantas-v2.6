<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarcField extends Model
{
    protected $fillable = [
        'tag',
        'subfield',
        'label',
        'repeatable',
        'input_type',
        'options',
    ];

    protected $casts = [
        'repeatable' => 'boolean',
        'options' => 'array',
    ];

    public static function isExtensibleSelect(string $tag, ?string $subfield): bool
    {
        foreach (config('catalog.extensible_select_marc', []) as $def) {
            $defSub = $def['subfield'] ?? null;
            if ($def['tag'] === $tag && $defSub === $subfield) {
                return true;
            }
        }

        return false;
    }

    public static function findForTagSubfield(string $tag, ?string $subfield): ?self
    {
        return static::query()
            ->where('tag', $tag)
            ->where(function ($q) use ($subfield) {
                if ($subfield === null || $subfield === '') {
                    $q->whereNull('subfield');
                } else {
                    $q->where('subfield', $subfield);
                }
            })
            ->first();
    }

    /**
     * @return list<string>
     */
    public static function normalizeOptionsArray(mixed $options): array
    {
        if (! is_array($options)) {
            return [];
        }

        $out = [];
        foreach ($options as $opt) {
            $val = is_array($opt) ? trim((string) ($opt['value'] ?? $opt['label'] ?? '')) : trim((string) $opt);
            if ($val !== '' && ! in_array($val, $out, true)) {
                $out[] = $val;
            }
        }

        return $out;
    }

    /**
     * Options for cataloging select: framework list + values already used on library_books.
     *
     * @return list<string>
     */
    public function mergedSelectOptions(?string $bookColumn = null): array
    {
        $options = collect(static::normalizeOptionsArray($this->options));

        $column = $bookColumn;
        if ($column) {
            $fromBooks = Book::query()
                ->whereNotNull($column)
                ->where($column, '!=', '')
                ->distinct()
                ->orderBy($column)
                ->pluck($column);
            $options = $options->merge($fromBooks);
        }

        return $options->unique(fn ($v) => mb_strtolower(trim((string) $v)))
            ->sort(SORT_NATURAL | SORT_FLAG_CASE)
            ->values()
            ->all();
    }
}
