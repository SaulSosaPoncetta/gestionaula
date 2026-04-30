<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nivel extends Model
{
    protected $table = 'niveles';
    protected $fillable = ['nombre', 'tipo'];

    const TIPOS = ['inicial', 'primario', 'secundario', 'terciario'];

    public function establecimientos()
    {
        return $this->hasMany(Establecimiento::class);
    }

    public function getTipoLabelAttribute(): string
    {
        return ucfirst($this->tipo);
    }
}