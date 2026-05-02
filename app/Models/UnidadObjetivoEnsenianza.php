<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnidadObjetivoEnsenianza extends Model
{
    protected $table = 'unidadobjetivosensenianza';
    protected $fillable = ['unidad_id', 'objetivo', 'orden'];

    public function unidad()
    {
        return $this->belongsTo(Unidad::class);
    }
}