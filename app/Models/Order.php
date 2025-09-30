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

   protected $fillable = ['order_id', 'klantnaam', 'aflever_straat','project_naam', 'rietkleur', 'toepassing', 'order_ordered',  'merk_paneel','kerndikte', 'aflever_postcode', 'aflever_land','aflever_plaats','referentie','discount', 'intaker', 'user_id', 'status'];
   public function orderLines()
   {
       return $this->hasMany(OrderLines::class);
   }

   public function user() {
       return $this->belongsTo(User::class);
   }


}
