<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActividadNota extends Model
{
    protected $table = 'actividadnotas';

    protected $fillable = [
        'user_id', 'asignacion_id', 'alumno_id', 'actividad_id',
        'notaindividual', 'notagrupal', 'estado', 'fechaestado', 'observacion'
    ];

    protected $casts = [
        'fechaestado' => 'date',
    ];

    const ESTADOS = [
        'pendiente'  => 'Pendiente',
        'enproceso'  => 'En proceso',
        'entregado'  => 'Entregado',
        'vencido'    => 'Vencido',
    ];

    const BADGES = [
        'pendiente'  => 'secondary',
        'enproceso'  => 'primary',
        'entregado'  => 'success',
        'vencido'    => 'danger',
    ];

    public function docente()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function asignacion()
    {
        return $this->belongsTo(ActividadAsignacion::class, 'asignacion_id');
    }

    public function alumno()
    {
        return $this->belongsTo(Alumno::class);
    }

    public function actividad()
    {
        return $this->belongsTo(Actividad::class);
    }

    public function getEstadolabelAttribute(): string
    {
        return self::ESTADOS[$this->estado] ?? ucfirst($this->estado);
    }

    public function getEstadobadgeAttribute(): string
    {
        return self::BADGES[$this->estado] ?? 'secondary';
    }
}