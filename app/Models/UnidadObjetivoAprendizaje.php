<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnidadObjetivoAprendizaje extends Model
{
    protected $table = 'unidadobjetivosaprendizaje';
    protected $fillable = ['unidad_id', 'objetivo', 'orden'];

    public function unidad()
    {
        return $this->belongsTo(Unidad::class);
    }
}