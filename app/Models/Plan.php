<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = ['nombre', 'descripcion', 'precio', 'periodicidad', 'activo'];

    protected $casts = ['activo' => 'boolean'];

    const PERIODICIDADES = [
        'mensual'     => 'Mensual',
        'trimestral'  => 'Trimestral',
        'anual'       => 'Anual',
    ];

    public function suscripciones()
    {
        return $this->hasMany(Suscripcion::class);
    }
}