<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class OrderLines extends Authenticatable
{
    protected $fillable = ['order_id',  'fillCb', 'fillLb',  'fillTotaleLengte', 'aantal', 'user_id','m2'];
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

}


