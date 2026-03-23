<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;

class DocumentationFolder extends Authenticatable
{
   use HasFactory;

   protected $fillable = ['order_id', 'name','lang','cropimage'];
    protected $table = 'documentationFolders';

    public function documentations()
    {
        return $this->hasMany(Documentation::class,'documentation_category_id', 'id');
    }

}
