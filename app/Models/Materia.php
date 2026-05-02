<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Materia extends Model
{
    protected $fillable = [
        'nombre', 'ciclo_id', 'area_formacion_id', 'especialidad_id',
        'establecimiento_id', 'anio', 'tipomateria', 'tipohora',
        'cargahorariasemanal', 'cargahorariaanual'
    ];

    const TIPOS     = ['aula', 'taller'];
    const TIPOSHORA = ['catedra', 'modulo'];

    const TIPOLABELS = [
        'aula'   => 'Aula',
        'taller' => 'Taller',
    ];

    public function ciclo()
    {
        return $this->belongsTo(Ciclo::class);
    }

    public function areaformacion()
    {
        return $this->belongsTo(AreaFormacion::class, 'area_formacion_id');
    }

    public function especialidad()
    {
        return $this->belongsTo(Especialidad::class);
    }

    public function cursos()
    {
        return $this->belongsToMany(Curso::class, 'curso_materia');
    }

    public function establecimiento()
    {
        return $this->belongsTo(Establecimiento::class);
    }

    public function asistencias()
    {
        return $this->hasMany(Asistencia::class);
    }

    public function getTipomateriaLabelAttribute(): string
    {
        return self::TIPOLABELS[$this->tipomateria] ?? '—';
    }

    public function getTipohoraLabelAttribute(): string
    {
        return match($this->tipohora) {
            'catedra' => 'Hora cátedra',
            'modulo'  => 'Módulo',
            default   => '—',
        };
    }
}
