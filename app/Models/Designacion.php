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
        'cupof', 'dependencia', 'turnodesempeno',
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

    public function docente()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}