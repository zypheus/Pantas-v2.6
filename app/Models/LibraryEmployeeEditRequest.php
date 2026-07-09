<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LibraryEmployeeEditRequest extends Model
{
    protected $guarded = [];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(LibraryEmployee::class, 'employee_id');
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
