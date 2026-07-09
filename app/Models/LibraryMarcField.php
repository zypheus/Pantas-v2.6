<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LibraryMarcField extends Model
{
    protected $guarded = [];

    protected $casts = [
        'select_options' => 'array',
    ];

    public function frameworkFields(): HasMany
    {
        return $this->hasMany(LibraryCatalogFrameworkField::class, 'marc_field_id');
    }
}
