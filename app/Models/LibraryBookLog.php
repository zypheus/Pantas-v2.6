<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LibraryBookLog extends Model
{
    protected $guarded = [];

    protected $casts = [
        'timestamp' => 'datetime',
        'due_date' => 'date',
        'returned_date' => 'datetime',
        'last_renewed_at' => 'datetime',
        'fine_cleared_at' => 'datetime',
    ];

    public function book(): BelongsTo
    {
        return $this->belongsTo(LibraryBook::class, 'book_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(LibraryStudent::class, 'student_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(LibraryEmployee::class, 'employee_id');
    }

    public function clearedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'fine_cleared_by');
    }
}
