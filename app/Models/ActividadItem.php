<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActividadItem extends Model
{
    protected $table = 'actividaditems';

    protected $fillable = [
        'actividad_id', 'numeroitem', 'texto', 'orden'
    ];

    public function actividad()
    {
        return $this->belongsTo(Actividad::class);
    }
}