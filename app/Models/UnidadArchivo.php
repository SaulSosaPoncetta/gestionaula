<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnidadArchivo extends Model
{
    protected $table = 'unidadarchivos';
    protected $fillable = ['unidad_id', 'nombre', 'ruta', 'orden'];

    public function unidad()
    {
        return $this->belongsTo(Unidad::class);
    }

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->ruta);
    }
}