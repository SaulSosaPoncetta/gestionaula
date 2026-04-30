<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Establecimiento extends Model
{
    protected $fillable = [
        'nombre', 'cue', 'modalidad', 'nivel_id',
        'direccion', 'localidad', 'provincia', 'telefono', 'email'
    ];

    const MODALIDADES = ['comun', 'tecnico'];

    public function nivel()
    {
        return $this->belongsTo(Nivel::class);
    }

    public function cursos()
    {
        return $this->hasMany(Curso::class);
    }

    public function docentes()
    {
        return $this->hasMany(User::class);
    }

    public function getModalidadLabelAttribute(): string
    {
        return match($this->modalidad) {
            'comun'   => 'Común',
            'tecnico' => 'Técnico',
        };
    }

    public function getNombreCompletoAttribute(): string
    {
        return "{$this->nombre} ({$this->modalidadlabel} — {$this->nivel->tipo_label})";
    }
}