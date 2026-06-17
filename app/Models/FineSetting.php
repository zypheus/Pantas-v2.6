<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FineSetting extends Model
{
    protected $fillable = [
        'fine_per_day',
        'max_fine',
        'grace_period_days',
        'loan_duration_days',
        'effective_from',
    ];

    public static function current()
    {
        return self::orderByDesc('effective_from')->first();
    }

    public static function currentOrDefault(): self
    {
        return self::current() ?? new self(self::defaultAttributes());
    }

    public static function defaultAttributes(): array
    {
        return [
            'fine_per_day' => (float) config('circulation.fine_per_day', 5.00),
            'max_fine' => config('circulation.max_fine', 500.00),
            'grace_period_days' => (int) config('circulation.grace_period_days', 0),
            'loan_duration_days' => (int) config('circulation.loan_duration_days', 7),
            'effective_from' => now()->toDateString(),
        ];
    }
}
