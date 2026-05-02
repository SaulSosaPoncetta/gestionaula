<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contenido extends Model
{
    protected $fillable = [
        'user_id', 'materia_id', 'tema', 'fecha', 'observacion'
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function materia()
    {
        return $this->belongsTo(Materia::class);
    }

    public function docente()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function subtemas()
    {
        return $this->hasMany(ContenidoSubtema::class)->orderBy('orden');
    }
}