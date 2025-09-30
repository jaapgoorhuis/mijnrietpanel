<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

    protected $table = 'users';
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'bedrijfsnaam',
        'phone',
        'bedrijf_id'
    ];

    public function companys() {
        return $this->belongsTo(Company::class, 'bedrijf_id', 'id');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function offertes()
    {
        return $this->hasMany(Offerte::class);
    }

    public function ordersYear() {
        return $this->hasMany(Order::class)
            ->whereYear('created_at', Carbon::now()->year);
    }

    public function offertesYear() {
        return $this->hasMany(Offerte::class)
            ->whereYear('created_at', Carbon::now()->year);
    }
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'bedrijf_id', 'id');
    }

}
