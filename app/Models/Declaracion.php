<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Declaracion extends Model
{
    protected $fillable = [
        'user_id', 'ciclo', 'estado', 'observacion',
        'fechapresentacion', 'fecharesolucion', 'resueltopor'
    ];

    protected $casts = [
        'fechapresentacion' => 'datetime',
        'fecharesolucion'   => 'datetime',
    ];

    public function docente()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function resolutor()
    {
        return $this->belongsTo(User::class, 'resueltopor');
    }

    public function items()
    {
        return $this->hasMany(DeclaracionItem::class);
    }

    public function getEstadoBadgeAttribute(): string
    {
        return match($this->estado) {
            'borrador'   => 'secondary',
            'presentada' => 'primary',
            'aprobada'   => 'success',
            'rechazada'  => 'danger',
        };
    }
}