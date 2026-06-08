<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderSurcharge extends Model
{
    protected $fillable = [
        'order_id',
        'name',
        'rule',
        'qty',
        'unit_price',
        'total',
    ];
}
