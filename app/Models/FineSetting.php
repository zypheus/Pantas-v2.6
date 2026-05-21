<?php
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
}

