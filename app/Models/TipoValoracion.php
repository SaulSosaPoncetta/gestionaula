<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoValoracion extends Model
{
    protected $table = 'tipovaloraciones';

    protected $fillable = [
        'user_id', 'denominacion', 'notainferior', 'notasuperior'
    ];

    protected $casts = [
        'notainferior' => 'decimal:2',
        'notasuperior' => 'decimal:2',
    ];

    public function docente()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}