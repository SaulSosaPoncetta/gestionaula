<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contenido extends Model
{
    protected $fillable = [
        'user_id',
        'materia_id',
        'numerounidad',
        'observacion',
        'tema',
        'fecha',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function materia(): BelongsTo
    {
        return $this->belongsTo(Materia::class);
    }

    public function subtemas(): HasMany
    {
        return $this->hasMany(ContenidoSubtema::class)->orderBy('orden');
    }
}
