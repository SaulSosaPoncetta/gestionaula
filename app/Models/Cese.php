<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cese extends Model
{
    protected $fillable = [
        'user_id', 'materia_id', 'establecimiento_id', 'horario_id',
        'fechatomapossesion', 'fechacese', 'numerosecuencia',
        'dia', 'horainicio', 'horafin'
    ];

    protected $casts = [
        'fechatomapossesion' => 'date',
        'fechacese'          => 'date',
    ];

    public function docente()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function materia()
    {
        return $this->belongsTo(Materia::class);
    }

    public function establecimiento()
    {
        return $this->belongsTo(Establecimiento::class);
    }
}