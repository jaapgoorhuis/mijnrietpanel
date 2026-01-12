<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;

class OrderRules extends Authenticatable
{
   use HasFactory;

    protected $fillable = ['rule', 'price','order_id','show_orderlist'];
    protected $table = 'order_rules';

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

}
