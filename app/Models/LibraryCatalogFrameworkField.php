<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LibraryCatalogFrameworkField extends Model
{
    protected $guarded = [];

    public function catalogFramework(): BelongsTo
    {
        return $this->belongsTo(LibraryCatalogFramework::class, 'catalog_framework_id');
    }

    public function marcField(): BelongsTo
    {
        return $this->belongsTo(LibraryMarcField::class, 'marc_field_id');
    }
}
