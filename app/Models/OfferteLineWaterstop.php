<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfferteLineWaterstop extends Model
{
    protected $table = 'offerte_line_waterstops';

    protected $fillable = [
        'offerte_line_id',
        'type',
        'vertical',
        'horizontal',
    ];

    protected $casts = [
        'type' => 'integer',
        'vertical' => 'integer',
        'horizontal' => 'integer',
    ];

    public function offerteLine()
    {
        return $this->belongsTo(OfferteLines::class, 'offerte_line_id');
    }


}
