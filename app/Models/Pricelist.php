<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;

class Pricelist extends Authenticatable
{
   use HasFactory;

   protected $fillable = ['order_id', 'friendly_name', 'file_name','lang','pricelistFolder_id'];
    protected $table = 'pricelist';
    public function pricelistFolder()
    {
        return $this->belongsTo(PricelistFolder::class);
    }

}
