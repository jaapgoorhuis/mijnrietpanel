<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KerndikteColor extends Model
{
    use HasFactory;

    protected $fillable = ['kerndikte', 'color'];
}
