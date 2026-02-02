<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Company extends Authenticatable
{
    use HasFactory;
    protected $fillable = [
        'bedrijfsnaam',
        'discount',
        'is_reseller',
        'straat',
        'postcode',
        'plaats',
        'logo',
        'lang',

    ];

    protected $table = 'companys';

    public function users() {
        return $this->hasMany(User::class, 'bedrijf_id', 'id');
    }
}
