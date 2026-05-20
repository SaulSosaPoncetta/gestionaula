<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CierreNota extends Model
{
    protected $table = 'cierrenotas';

    protected $fillable = [
        'user_id', 'alumno_id', 'materia_id', 'curso_id',
        'tipocierre', 'notanumerica', 'notavalorativa',
        'promedioactividades', 'promediocalificaciones',
        'notaasistencia', 'porcentajeasistencia', 'fecharegistro'
    ];

    protected $casts = [
        'fecharegistro' => 'date',
    ];

    public function docente()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function alumno()
    {
        return $this->belongsTo(Alumno::class);
    }

    public function materia()
    {
        return $this->belongsTo(Materia::class);
    }

    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }
}