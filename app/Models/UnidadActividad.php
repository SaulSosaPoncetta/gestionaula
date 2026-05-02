<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnidadActividad extends Model
{
    protected $table = 'unidadactividades';
    protected $fillable = ['unidad_id', 'actividad', 'orden'];

    public function unidad()
    {
        return $this->belongsTo(Unidad::class);
    }
}