<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class BrandingVersion extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'branding_setting_id',
        'snapshot',
        'changed_by',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'snapshot' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function brandingSetting(): BelongsTo
    {
        return $this->belongsTo(BrandingSetting::class);
    }

    public function changer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}