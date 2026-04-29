<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mensaje extends Model
{
    protected $fillable = [
        'user_id', 'destinatario', 'curso_id',
        'alumno_id', 'asunto', 'cuerpo', 'tipo'
    ];

    public function remitente()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }

    public function alumno()
    {
        return $this->belongsTo(Alumno::class);
    }
}