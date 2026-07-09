<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LibraryRole extends Model
{
    protected $guarded = [];

    public function employees(): HasMany
    {
        return $this->hasMany(LibraryEmployee::class, 'role_id');
    }
}
