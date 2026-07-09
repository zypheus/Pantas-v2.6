<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LibraryPendingEmployee extends Model
{
    protected $guarded = [];

    public function role(): BelongsTo
    {
        return $this->belongsTo(LibraryRole::class, 'role_id');
    }
}
