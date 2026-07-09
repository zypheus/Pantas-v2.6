<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LibraryProgram extends Model
{
    protected $guarded = [];

    public function courses(): HasMany
    {
        return $this->hasMany(LibraryProgramCourse::class, 'program_id');
    }

    public function books(): BelongsToMany
    {
        return $this->belongsToMany(LibraryBook::class, 'library_book_program', 'program_id', 'book_id');
    }
}
