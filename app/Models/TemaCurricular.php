<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TemaCurricular extends Model
{
    protected $table = 'temas_curriculares';
    protected $fillable = ['nucleo_curricular_id', 'nombre', 'orden'];

    public function nucleo(): BelongsTo
    {
        return $this->belongsTo(NucleoCurricular::class, 'nucleo_curricular_id');
    }

    public function subtemas(): HasMany
    {
        return $this->hasMany(SubtemaCurricular::class)->orderBy('orden');
    }
}
