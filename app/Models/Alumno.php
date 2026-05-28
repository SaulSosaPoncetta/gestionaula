<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alumno extends Model
{
    protected $fillable = [
        'user_id', 'nombre', 'apellido', 'dni',
        'fechanacimiento', 'telefono', 'email',
        'porcentajeasistencia', 'curso_id', 'tipocursada'
    ];

    protected $casts = ['fechanacimiento' => 'date'];

    const TIPOSCURSADA = [
        'regular'     => 'Regular',
        'libre'       => 'Libre',
        'recursa'     => 'Recursa',
        'intensifica' => 'Intensifica',
    ];

    const BADGESCURSADA = [
        'regular'     => 'success',
        'libre'       => 'warning',
        'recursa'     => 'info',
        'intensifica' => 'danger',
    ];

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

    public function getTipocursadalabelAttribute(): string
    {
        return self::TIPOSCURSADA[$this->tipocursada] ?? ucfirst($this->tipocursada);
    }

    public function getTipocursadabadgeAttribute(): string
    {
        return self::BADGESCURSADA[$this->tipocursada] ?? 'secondary';
    }
}