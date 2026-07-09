<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class LibraryBook extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'archived_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function logs(): HasMany
    {
        return $this->hasMany(LibraryBookLog::class, 'book_id');
    }

    public function marcFields(): HasMany
    {
        return $this->hasMany(LibraryBookMarcField::class, 'book_id');
    }

    public function programs(): BelongsToMany
    {
        return $this->belongsToMany(LibraryProgram::class, 'library_book_program', 'book_id', 'program_id');
    }
}
