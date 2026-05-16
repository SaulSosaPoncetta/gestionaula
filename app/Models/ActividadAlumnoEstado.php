<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActividadAlumnoEstado extends Model
{
    protected $table = 'actividadalumnoestados';

    protected $fillable = [
        'actividad_id', 'alumno_id', 'user_id',
        'estado', 'fechaestado', 'nota', 'observacion'
    ];

    protected $casts = [
        'fechaestado' => 'date',
    ];

    const ESTADOS = [
        'enproceso'  => 'En proceso',
        'finalizado' => 'Finalizado',
        'vencida'    => 'Entrega vencida',
        'incompleta' => 'Entrega incompleta',
    ];

    const BADGES = [
        'enproceso'  => 'primary',
        'finalizado' => 'success',
        'vencida'    => 'danger',
        'incompleta' => 'warning',
    ];

    public function actividad()
    {
        return $this->belongsTo(Actividad::class);
    }

    public function alumno()
    {
        return $this->belongsTo(Alumno::class);
    }

    public function docente()
    {
        return $this->belongsTo(User::class, 'user_id');
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