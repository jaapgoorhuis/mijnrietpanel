<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class PriceRules extends Authenticatable
{
    use HasFactory;
    protected $fillable = [
        'rule_name',
        'panel_type',
        'price'

    ];

    protected $table = 'price_rules';


    public function panelType() {
        return $this->hasOne(PanelType::class,'id', 'panel_type');
    }

}
