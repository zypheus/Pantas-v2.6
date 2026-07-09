<?php

declare(strict_types=1);

return [
    'fine_per_day' => env('CIRCULATION_FINE_PER_DAY', 5.00),
    'max_fine' => env('CIRCULATION_MAX_FINE', 500.00),
    'grace_period_days' => env('CIRCULATION_GRACE_PERIOD_DAYS', 0),
    'loan_duration_days' => env('CIRCULATION_LOAN_DURATION_DAYS', 7),
];
