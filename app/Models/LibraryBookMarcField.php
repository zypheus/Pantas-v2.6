<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LibraryBookMarcField extends Model
{
    protected $guarded = [];

    public function book(): BelongsTo
    {
        return $this->belongsTo(LibraryBook::class, 'book_id');
    }
}
