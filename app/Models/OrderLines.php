<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class OrderLines extends Authenticatable
{
    protected $fillable = ['order_id',  'fillCb', 'fillLb','waterstop_enabled', 'waterstop_type','waterstop_vertical', 'waterstop_horizontal','price_per_m2', 'lb','nokafschuining', 'vrije_ruimte_1', 'vrije_ruimte_2',  'fillTotaleLengte', 'aantal', 'user_id','m2'];
    public function order()
    {
        return $this->belongsTo(Order::class);
    }



    public function waterstops()
    {
        return $this->hasMany(OrderLineWaterstop::class, 'order_line_id');
    }

}


