<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeclaracionItem extends Model
{
    protected $table = 'declaracionitems';

    protected $fillable = [
        'declaracion_id', 'establecimiento_id', 'curso_id', 'materia_id',
        'dia', 'horainicio', 'horafin'
    ];

    public function declaracion()
    {
        return $this->belongsTo(Declaracion::class);
    }

    public function establecimiento()
    {
        return $this->belongsTo(Establecimiento::class);
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
