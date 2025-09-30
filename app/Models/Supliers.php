<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Supliers extends Authenticatable
{
    use HasFactory;
    protected $fillable = [
        'name',
        'status',
        'werkende_breedte',
        'toepassing_dak',
        'toepassing_wand',
        'suplier_name',
        'suplier_straat',
        'suplier_land',
        'suplier_postcode',
        'suplier_plaats',
        'suplier_email',


    ];

    protected $table = 'supliers';
}
