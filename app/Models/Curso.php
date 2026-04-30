<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Curso extends Model
{
    protected $fillable = ['nombre', 'division', 'turno', 'nivel', 'establecimiento_id'];

    public function alumnos()
    {
        return $this->hasMany(Alumno::class);
    }

    public function materias()
    {
        return $this->hasMany(Materia::class);
    }

    public function establecimiento()
    {
        return $this->belongsTo(Establecimiento::class);
    }

    public function docentes()
    {
        return $this->belongsToMany(User::class, 'cursodocente')
                    ->withPivot('materia_id')
                    ->withTimestamps();
    }

    public function getNombreCompletoAttribute(): string
    {
        return trim("{$this->nombre} {$this->division} {$this->turno}");
    }
}