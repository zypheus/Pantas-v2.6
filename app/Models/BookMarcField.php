<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookMarcField extends Model
{
    protected $table = 'book_marc_fields';

    protected $fillable = [
        'book_id',
        'tag',
        'subfield',
        'indicator1',
        'indicator2',
        'occurrence',
        'value',
    ];
}

