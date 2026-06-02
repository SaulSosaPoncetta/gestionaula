<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nivel extends Model
{
    const TIPOS = ['inicial', 'primario', 'secundario', 'terciario', 'universitario'];

    protected $fillable = ['user_id', 'nombre', 'tipo'];

    public function establecimientos()
    {
        return $this->hasMany(Establecimiento::class);
    }

    public function cursos()
    {
        return $this->hasMany(Curso::class);
    }
}