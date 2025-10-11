<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class PanelType extends Authenticatable
{
    use HasFactory;
    protected $fillable = [
        'name',

    ];

    protected $table = 'panel_types';

    public function priceRule() {
        return $this->hasOne(PriceRules::class,'panel_type', 'id');
    }

}
