<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubtemaCurricular extends Model
{
    protected $table = 'subtemas_curriculares';
    protected $fillable = ['tema_curricular_id', 'nombre', 'orden'];

    public function tema(): BelongsTo
    {
        return $this->belongsTo(TemaCurricular::class, 'tema_curricular_id');
    }
}
