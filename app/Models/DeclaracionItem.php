<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeclaracionItem extends Model
{
    protected $table = 'declaracionitems';

    protected $fillable = [
        'declaracion_id', 'curso_id', 'materia_id',
        'dia', 'horainicio', 'horafin', 'actividad'
    ];

    public function declaracion()
    {
        return $this->belongsTo(Declaracion::class);
    }

    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }

    public function materia()
    {
        return $this->belongsTo(Materia::class);
    }
}