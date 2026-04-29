<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tarea extends Model
{
    protected $fillable = [
        'curso_id', 'materia_id', 'user_id',
        'titulo', 'descripcion', 'fechavencimiento', 'estado'
    ];

    protected $casts = [
        'fechavencimiento' => 'date',
    ];

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

    public function entregas()
    {
        return $this->hasMany(Entrega::class);
    }
}