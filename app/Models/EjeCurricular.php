<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EjeCurricular extends Model
{
    protected $fillable = ['materia_id', 'anio', 'nombre', 'orden'];

    public function materia(): BelongsTo
    {
        return $this->belongsTo(Materia::class);
    }

    public function nucleos(): HasMany
    {
        return $this->hasMany(NucleoCurricular::class)->orderBy('orden');
    }
}
