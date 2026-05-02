<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asistencia extends Model
{
    protected $fillable = [
        'alumno_id', 'curso_id', 'materia_id', 'user_id',
        'fecha', 'estado', 'horallegada', 'fotojustificacion', 'observacion'
    ];

    protected $casts = [
        'fecha' => 'date',
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

    public function getEstadoBadgeAttribute(): string
    {
        return match($this->estado) {
            'presente'    => 'success',
            'ausente'     => 'danger',
            'tarde'       => 'warning',
            'justificado' => 'info',
            default       => 'secondary',
        };
    }

    public function getEstadoLabelAttribute(): string
    {
        return match($this->estado) {
            'presente'    => 'Presente',
            'ausente'     => 'Ausente',
            'tarde'       => 'Tarde',
            'justificado' => 'Justificado',
            default       => ucfirst($this->estado),
        };
    }
}
