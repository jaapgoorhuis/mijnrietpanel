<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Surcharges extends Authenticatable
{
    use HasFactory;
    protected $fillable = [
        'condition',
        'rule',
        'price',
        'name',
        'number'

    ];

    protected $table = 'surcharges';

}
