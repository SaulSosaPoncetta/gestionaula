<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoEvaluacion extends Model
{
    protected $table = 'tiposevaluacion';

    protected $fillable = ['denominacion'];

    public function calificaciones()
    {
        return $this->hasMany(Calificacion::class, 'tipoevaluacion_id');
    }
}