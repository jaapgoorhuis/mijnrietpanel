<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class OrderLines extends Authenticatable
{
    protected $fillable = ['order_id', 'rietkleur', 'toepassing', 'merk_paneel', 'fillCb', 'fillLb', 'kerndikte', 'fillTotaleLengte', 'aantal', 'user_id','m2'];
}
