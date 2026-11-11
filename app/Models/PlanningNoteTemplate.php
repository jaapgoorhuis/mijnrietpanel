<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanningNoteTemplate extends Model
{
    protected $fillable = [
        'title',
        'color',
        'blocks_day',
    ];

    protected $casts = [
        'blocks_day' => 'boolean',
    ];
}
