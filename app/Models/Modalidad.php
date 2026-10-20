<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Modalidad extends Model
{
    protected $fillable = ['user_id', 'nombre', 'slug'];
}
