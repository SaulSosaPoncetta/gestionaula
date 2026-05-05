<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoClase extends Model
{
    protected $table = 'tiposclase';
    protected $fillable = ['denominacion'];
}