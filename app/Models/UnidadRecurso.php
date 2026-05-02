<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnidadRecurso extends Model
{
    protected $table = 'unidadrecursos';
    protected $fillable = ['unidad_id', 'recurso', 'orden'];

    public function unidad()
    {
        return $this->belongsTo(Unidad::class);
    }
}