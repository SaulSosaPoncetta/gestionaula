<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Designacion extends Model
{
    protected $table = 'designaciones';

    protected $fillable = [
        'user_id', 'distrito', 'tipoestablecimiento', 'numeroescuela',
        'nombreestablecimiento', 'secuencia', 'dependencia_tipo',
        'regimenstatutario', 'caracterderevista', 'tipohora',
        'cupof', 'ige', 'cantmodulos', 'tipohorario', 'dependencia', 'turnodesempeno',
        'fechadesde', 'fechahasta', 'anodesignado', 'divisiondesignada',
        'fechadesignacion', 'fechatomaposecion', 'nombremateria',
        'horaentrada', 'horasalida', 'diasemana'
    ];

    protected $casts = [
        'fechadesde'       => 'date',
        'fechahasta'       => 'date',
        'fechadesignacion' => 'date',
        'fechatomaposecion'=> 'date',
    ];

    const DEPENDENCIA_TIPOS = [
        'oficial'  => 'Oficial',
        'dipregep' => 'Di.Pre.Gep',
    ];

    const TIPOS_HORA = [
        'modulos' => 'Módulos',
        'horas'   => 'Horas',
    ];

    const DIAS = [
        'lunes'     => 'Lunes',
        'martes'    => 'Martes',
        'miercoles' => 'Miércoles',
        'jueves'    => 'Jueves',
        'viernes'   => 'Viernes',
        'sabado'    => 'Sábado',
        'domingo'   => 'Domingo',
    ];

    const TURNOS = [
        'Mañana'  => 'Mañana',
        'Tarde'   => 'Tarde',
        'Noche'   => 'Noche',
        'Vespertino' => 'Vespertino',
    ];

    const TIPOS_HORARIO = [
        'unificado' => 'Unificado',
        'dividido'  => 'Dividido por día',
    ];

    public function docente()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function horarios()
    {
        return $this->hasMany(DesignacionHorario::class)->orderBy('orden');
    }

    /**
     * Devuelve un arreglo de filas horario-diarias, sin importar el tipo
     * de carga (unificado = una sola fila con los datos de cabecera,
     * dividido = una fila por cada dia registrado en designacion_horarios).
     */
    public function filasHorario(): array
    {
        if ($this->tipohorario === 'dividido') {
            return $this->horarios->map(fn($h) => [
                'dia'         => $h->dia,
                'horaentrada' => substr($h->horaentrada, 0, 5),
                'horasalida'  => substr($h->horasalida, 0, 5),
                'cantmodulos' => $h->cantmodulos,
            ])->toArray();
        }

        if (!$this->diasemana || !$this->horaentrada || !$this->horasalida) {
            return [];
        }

        return [[
            'dia'         => $this->diasemana,
            'horaentrada' => substr($this->horaentrada, 0, 5),
            'horasalida'  => substr($this->horasalida, 0, 5),
            'cantmodulos' => $this->cantmodulos,
        ]];
    }
}