<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NucleoCurricular extends Model
{
    protected $table = 'nucleos_curriculares';
    protected $fillable = ['eje_curricular_id', 'nombre', 'orden'];

    public function eje(): BelongsTo
    {
        return $this->belongsTo(EjeCurricular::class, 'eje_curricular_id');
    }

    public function temas(): HasMany
    {
        return $this->hasMany(TemaCurricular::class)->orderBy('orden');
    }
}
