<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderLineWaterstop extends Model
{
    protected $table = 'order_line_waterstops';

    protected $fillable = [
        'order_line_id',
        'type',
        'vertical',
        'horizontal',
    ];

    protected $casts = [
        'type' => 'integer',
        'vertical' => 'integer',
        'horizontal' => 'integer',
    ];

    public function orderLine()
    {
        return $this->belongsTo(OrderLines::class, 'order_line_id');
    }
}
