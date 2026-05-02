<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialTeoricoArchivo extends Model
{
    protected $table = 'materialteoricoarchivos';

    protected $fillable = [
        'user_id', 'tarea_id', 'titulo', 'descripcion', 'ruta', 'orden'
    ];

    public function docente()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tarea()
    {
        return $this->belongsTo(Tarea::class);
    }

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->ruta);
    }
}