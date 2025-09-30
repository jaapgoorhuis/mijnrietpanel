<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;

class Offerte extends Authenticatable
{
   use HasFactory;

   protected $fillable = ['offerte_id', 'discount', 'rietkleur', 'status', 'toepassing', 'merk_paneel','kerndikte', 'project_naam', 'is_order', 'klantnaam', 'referentie','aflever_straat', 'aflever_postcode', 'aflever_plaats', 'aflever_land', 'intaker', 'user_id', 'status'];

   protected $table = 'offerte';
   public function offerteLines() {
       return $this->hasMany(OfferteLines::class);
   }

   public function user() {
       return $this->belongsTo(User::class);
   }


}
