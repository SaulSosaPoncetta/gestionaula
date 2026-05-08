<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Curso extends Model
{
    
    protected $fillable = [
        'user_id', 'nombre', 'anio', 'division', 'turno',
        'nivel_id', 'especialidad_id', 'establecimiento_id'
    ];

    public function alumnos()
    {
        return $this->hasMany(Alumno::class);
    }

    public function materias()
    {
        return $this->belongsToMany(Materia::class, 'curso_materia');
    }

    public function docentes()
    {
        return $this->belongsToMany(User::class, 'cursodocente')
                    ->withPivot('materia_id')
                    ->withTimestamps();
    }

    public function establecimiento()
    {
        return $this->belongsTo(Establecimiento::class);
    }

    public function nivel()
    {
        return $this->belongsTo(Nivel::class);
    }

    public function especialidad()
    {
        return $this->belongsTo(Especialidad::class);
    }

    public function getNombreCompletoAttribute(): string
    {
        $partes = array_filter([
            $this->anio ?? $this->nombre,
            $this->division,
            $this->turno,
        ]);
        return implode(' ', $partes);
    }
}