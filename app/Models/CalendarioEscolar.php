<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CalendarioEscolar extends Model
{
    protected $table = 'calendarioescolar';

    protected $fillable = [
        'user_id', 'periodo_id', 'fecha', 'denominacion',
        'esferiado', 'fechainicio', 'fechafin'
    ];

    protected $casts = [
        'fecha'      => 'date',
        'fechainicio' => 'date',
        'fechafin'   => 'date',
        'esferiado'  => 'boolean',
    ];

    public function docente()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function periodo()
    {
        return $this->belongsTo(Periodo::class);
    }
}