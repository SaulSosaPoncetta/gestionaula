<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActividadAsignacion extends Model
{
    protected $table = 'actividadasignaciones';

    protected $fillable = [
        'actividad_id', 'curso_id', 'materia_id', 'user_id',
        'fechainicio', 'fechaentrega', 'esgrupal',
        'integrantesporgrupo', 'modogrupo', 'estado'
    ];

    protected $casts = [
        'fechainicio'  => 'date',
        'fechaentrega' => 'date',
        'esgrupal'     => 'boolean',
    ];

    public function actividad()
    {
        return $this->belongsTo(Actividad::class);
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

    public function grupos()
    {
        return $this->hasMany(ActividadGrupo::class, 'actividad_id', 'actividad_id');
    }
}