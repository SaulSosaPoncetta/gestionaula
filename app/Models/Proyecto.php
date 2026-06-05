<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proyecto extends Model
{
    protected $table = 'proyectos';

    protected $fillable = [
        'user_id', 'materia_id', 'curso_id', 'establecimiento_id',
        'actividad_id', 'titulo', 'descripcion', 'fecha', 'hora',
        'fechapresentacion', 'observaciones', 'estado'
    ];

    protected $casts = [
        'fecha'             => 'date',
        'fechapresentacion' => 'date',
    ];

    const ESTADOS = [
        'borrador'   => 'Borrador',
        'activo'     => 'Activo',
        'presentado' => 'Presentado',
        'cerrado'    => 'Cerrado',
    ];

    const BADGES = [
        'borrador'   => 'secondary',
        'activo'     => 'success',
        'presentado' => 'primary',
        'cerrado'    => 'dark',
    ];

    public function docente()        { return $this->belongsTo(User::class, 'user_id'); }
    public function materia()        { return $this->belongsTo(Materia::class); }
    public function curso()          { return $this->belongsTo(Curso::class); }
    public function establecimiento(){ return $this->belongsTo(Establecimiento::class); }
    public function actividad()      { return $this->belongsTo(Actividad::class); }

    public function alumnos()
    {
        return $this->belongsToMany(Alumno::class, 'proyectoalumnos');
    }

    public function carpetas()
    {
        return $this->hasMany(CarpetaCampo::class, 'proyecto_id');
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