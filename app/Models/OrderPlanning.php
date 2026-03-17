<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderPlanning extends Model
{
    protected $fillable = ['order_id', 'planned_date', 'planned_m2'];

    protected $table = 'order_planning';
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
