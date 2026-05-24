<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alumno extends Model
{
    protected $fillable = [
        'user_id', 'codigo', 'nombre', 'apellido',
        'fechanacimiento', 'porcentajeasistencia',
        'curso_id', 'tipocursada'
    ];

    protected $casts = ['fechanacimiento' => 'date'];

    const TIPOSCURSADA = [
        'regular'     => 'Regular',
        'libre'       => 'Libre',
        'recursa'     => 'Recursa',
        'intensifica' => 'Intensifica',
    ];

    const BADGESCURSADA = [
        'regular'     => 'success',
        'libre'       => 'warning',
        'recursa'     => 'info',
        'intensifica' => 'danger',
    ];

    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }

    public function asistencias()
    {
        return $this->hasMany(Asistencia::class);
    }

    public function getNombreCompletoAttribute(): string
    {
        return "{$this->apellido}, {$this->nombre}";
    }

    public function getTipocursadalabelAttribute(): string
    {
        return self::TIPOSCURSADA[$this->tipocursada] ?? ucfirst($this->tipocursada);
    }

    public function getTipocursadabadgeAttribute(): string
    {
        return self::BADGESCURSADA[$this->tipocursada] ?? 'secondary';
    }

    /**
     * Genera un código único de 8 dígitos
     */
    public static function generarCodigo(): string
    {
        do {
            $codigo = str_pad(random_int(0, 99999999), 8, '0', STR_PAD_LEFT);
        } while (self::where('codigo', $codigo)->exists());

        return $codigo;
    }
}