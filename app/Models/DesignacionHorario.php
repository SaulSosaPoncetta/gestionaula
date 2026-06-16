<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DesignacionHorario extends Model
{
    protected $table = 'designacion_horarios';

    protected $fillable = [
        'designacion_id', 'dia', 'cantmodulos', 'horaentrada', 'horasalida', 'orden'
    ];

    public function designacion()
    {
        return $this->belongsTo(Designacion::class);
    }
}
