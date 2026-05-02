<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContenidoSubtema extends Model
{
    protected $table = 'contenidosubtemas';

    protected $fillable = [
        'contenido_id', 'subtema', 'orden'
    ];

    public function contenido()
    {
        return $this->belongsTo(Contenido::class);
    }
}