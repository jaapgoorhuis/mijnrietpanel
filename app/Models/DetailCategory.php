<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;

class DetailCategory extends Authenticatable
{
   use HasFactory;

   protected $fillable = ['order_id', 'name', 'lang','cropimage','detail_folder_id'];
   protected $table = 'detailCategories';

    public function details()
    {
        return $this->hasMany(Detail::class, 'detail_category_id', 'id');
    }
}
