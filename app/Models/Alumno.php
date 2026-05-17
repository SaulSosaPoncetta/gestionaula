<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alumno extends Model
{
    protected $fillable = [
        'user_id', 'nombre', 'apellido', 'dni',
        'fechanacimiento', 'telefono', 'email', 'curso_id'
    ];

    protected $casts = ['fechanacimiento' => 'date'];

    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }

    public function asistencias()
    {
        return $this->hasMany(Asistencia::class);
    }

    public function getNombreCompletoAttribute(): string
    {
        return "{$this->apellido}, {$this->nombre}";
    }
}