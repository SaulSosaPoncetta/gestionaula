<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Periodo extends Model
{
    protected $table = 'periodos';

    protected $fillable = ['denominacion', 'orden'];

    public function calificaciones()
    {
        return $this->hasMany(Calificacion::class, 'periodo_id');
    }
}