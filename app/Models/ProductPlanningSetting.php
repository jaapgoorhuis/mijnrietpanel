<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductPlanningSetting extends Model
{
    protected $fillable = [
        'max_m2_per_day',
        'blocked_days',
        'blocked_dates',
    ];

    protected $casts = [
        'blocked_days' => 'array',
        'blocked_dates'=> 'array',// Laravel cast JSON naar array
    ];
}
