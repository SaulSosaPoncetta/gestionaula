<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Actividad extends Model
{
    protected $table = 'actividades';

    protected $fillable = [
        'user_id', 'materia_id', 'curso_id', 'tipoactividad_id',
        'titulo', 'tema', 'subtema', 'descripcion',
        'fechainicio', 'fechaentrega',
        'esgrupal', 'integrantesporgrupo', 'estado'
    ];

    protected $casts = [
        'fechainicio'  => 'date',
        'fechaentrega' => 'date',
        'esgrupal'     => 'boolean',
    ];

    public function docente()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function materia()
    {
        return $this->belongsTo(Materia::class);
    }

    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }

    public function tipoactividad()
    {
        return $this->belongsTo(TipoActividad::class);
    }

    public function grupos()
    {
        return $this->hasMany(ActividadGrupo::class)->orderBy('numero');
    }
}