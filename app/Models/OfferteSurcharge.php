<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfferteSurcharge extends Model
{
    protected $fillable = [
        'offerte_id',
        'name',
        'rule',
        'qty',
        'unit_price',
        'total',
    ];
}
