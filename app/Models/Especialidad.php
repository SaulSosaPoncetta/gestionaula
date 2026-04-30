<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Especialidad extends Model
{
    protected $fillable = ['nombre', 'descripcion'];

    public function materias()
    {
        return $this->hasMany(Materia::class);
    }
}