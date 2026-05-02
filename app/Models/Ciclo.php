<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Ciclo extends Model
{
    protected $fillable = ['nombre', 'tipo', 'descripcion'];

    const TIPOS = ['basico', 'superior'];

    public function materias()
    {
        return $this->hasMany(Materia::class);
    }

    public function getTipolabelAttribute(): string
    {
        return match($this->tipo) {
            'basico'   => 'Ciclo Básico',
            'superior' => 'Ciclo Superior',
            default    => ucfirst($this->tipo),
        };
    }
}