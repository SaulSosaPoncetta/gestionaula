<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entrega extends Model
{
    protected $fillable = [
        'tarea_id', 'alumno_id', 'estado', 'observacion', 'fechaentrega'
    ];

    protected $casts = [
        'fechaentrega' => 'date',
    ];

    public function tarea()
    {
        return $this->belongsTo(Tarea::class);
    }

    public function alumno()
    {
        return $this->belongsTo(Alumno::class);
    }
}