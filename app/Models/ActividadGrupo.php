<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActividadGrupo extends Model
{
    protected $table = 'actividadgrupos';

    protected $fillable = ['actividad_id', 'nombre', 'numero'];

    public function actividad()
    {
        return $this->belongsTo(Actividad::class);
    }

    public function alumnos()
    {
        return $this->belongsToMany(
            Alumno::class,
            'actividadgrupoalumnos',
            'grupo_id',
            'alumno_id'
        );
    }
}