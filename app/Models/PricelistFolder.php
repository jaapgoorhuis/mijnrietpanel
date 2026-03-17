<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;

class PricelistFolder extends Authenticatable
{
   use HasFactory;

   protected $fillable = ['order_id', 'name','lang','cropimage'];
    protected $table = 'pricelistFolder';

    public function pricelists()
    {
        return $this->hasMany(Pricelist::class,'pricelistFolder_id', 'id');
    }

}
