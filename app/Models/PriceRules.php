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
        'price',
        'reseller',
        'company_id'

    ];

    protected $table = 'price_rules';


    public function panelType() {
        return $this->hasOne(PanelType::class,'id', 'panel_type');
    }

    public function company() {
        return $this->belongsTo(Company::class, 'id', 'company_id');
    }
}
