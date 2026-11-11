<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanningNote extends Model
{
    protected $fillable = [
        'date',
        'title',
        'color',
        'blocks_day',
    ];

    protected $casts = [
        'blocks_day' => 'boolean',
    ];
}
