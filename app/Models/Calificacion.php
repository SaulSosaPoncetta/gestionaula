<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Calificacion extends Model
{
    protected $table = 'calificaciones';

    protected $fillable = [
        'alumno_id', 'curso_id', 'materia_id', 'user_id',
        'periodo_id', 'tipoevaluacion_id', 'nota', 'observacion'
    ];

    public function alumno()
    {
        return $this->belongsTo(Alumno::class);
    }

    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }

    public function materia()
    {
        return $this->belongsTo(Materia::class);
    }

    public function docente()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function periodo()
    {
        return $this->belongsTo(Periodo::class);
    }

    public function tipoevaluacion()
    {
        return $this->belongsTo(TipoEvaluacion::class);
    }
}