<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Planificacion extends Model
{
    protected $fillable = ['user_id', 'materia_id', 'ciclo', 'descripcion'];

    public function docente()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function materia()
    {
        return $this->belongsTo(Materia::class);
    }

    public function unidades()
    {
        return $this->hasMany(Unidad::class)->orderBy('orden');
    }
}