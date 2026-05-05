<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LibroTema extends Model
{
    protected $table = 'librotemas';

    protected $fillable = [
        'user_id', 'materia_id', 'curso_id', 'tipoclase_id',
        'contenido_id', 'actividad_id', 'fecha', 'numeroclase',
        'numerounidad', 'observacion'
    ];

    protected $casts = [
        'fecha' => 'date',
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

    public function tipoclase()
    {
        return $this->belongsTo(TipoClase::class);
    }

    public function contenido()
    {
        return $this->belongsTo(Contenido::class);
    }

    public function actividad()
    {
        return $this->belongsTo(Actividad::class);
    }
}