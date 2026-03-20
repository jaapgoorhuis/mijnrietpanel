<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;

class Order extends Authenticatable
{
   use HasFactory;

   protected $fillable = ['order_id', 'klantnaam', 'split_m2', 'planned_m2_today','rietpanel_comment', 'marge','lang', 'requested_delivery_date', 'comment', 'aflever_straat','project_naam', 'rietkleur', 'toepassing', 'order_ordered',  'merk_paneel','kerndikte', 'aflever_postcode', 'aflever_land','aflever_plaats','referentie','discount', 'intaker', 'user_id', 'status'];
   public function orderLines()
   {
       return $this->hasMany(OrderLines::class);
   }

    public function getTotalM2Attribute()
    {
        return $this->orderLines()->sum('m2');
    }


   public function Suplier() {
       return $this->hasOne(Supliers::class,'name', 'merk_paneel');
   }
    public function orderRules()
    {
        return $this->hasOne(OrderRules::class);
    }

   public function user() {
       return $this->belongsTo(User::class);
   }

    public function getKerndikteColorAttribute()
    {
        // Zoek de kleur in kerndikte_colors waar de kerndikte matcht
        $kc = \App\Models\KerndikteColor::where('kerndikte', $this->kerndikte)->first();

        // Als er geen match is, fallback naar zwart (of wat je wilt)
        return $kc ? $kc->color : '#000000';
    }

    public function planning()
    {
        return $this->hasMany(OrderPlanning::class);
    }

    public function getTotalM2PlannedAttribute()
    {
        return $this->planning()->sum('planned_m2');
    }

}
