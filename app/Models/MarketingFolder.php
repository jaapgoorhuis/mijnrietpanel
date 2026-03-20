<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;

class MarketingFolder extends Authenticatable
{
   use HasFactory;

   protected $fillable = ['order_id', 'name', 'lang','cropimage'];

   protected $table = 'marketingFolders';

    public function marketing()
    {
        return $this->hasMany(Marketing::class,'marketingFolder_id', 'id');
    }

}
