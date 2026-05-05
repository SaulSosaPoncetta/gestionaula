<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActividadGrupoAlumno extends Model
{
    protected $table = 'actividadgrupoalumnos';

    protected $fillable = ['grupo_id', 'alumno_id'];

    public function grupo()
    {
        return $this->belongsTo(ActividadGrupo::class);
    }

    public function alumno()
    {
        return $this->belongsTo(Alumno::class);
    }
}