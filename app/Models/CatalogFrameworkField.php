<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatalogFrameworkField extends Model
{
    protected $fillable = [
        'framework_id',
        'marc_field_id',
        'visible',
        'required',
        'sort_order',
        'book_column',
        'default_value',
    ];

    protected $casts = [
        'visible' => 'boolean',
        'required' => 'boolean',
    ];

    public function framework()
    {
        return $this->belongsTo(CatalogFramework::class, 'framework_id');
    }

    public function marcField()
    {
        return $this->belongsTo(MarcField::class, 'marc_field_id');
    }
}

