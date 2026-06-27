<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CicloLectivo extends Model
{
    protected $table = 'ciclos_lectivos';

    protected $fillable = [
        'user_id', 'anio', 'fechainicio', 'fechafin', 'activo',
    ];

    protected $casts = [
        'fechainicio' => 'date',
        'fechafin'    => 'date',
        'activo'      => 'boolean',
    ];

    public function docente()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function estaActivo(): bool
    {
        $hoy = Carbon::now('America/Argentina/Buenos_Aires')->toDateString();
        return $this->activo
            && $this->fechainicio->toDateString() <= $hoy
            && $this->fechafin->toDateString()    >= $hoy;
    }

    public function terminoPronto(): bool
    {
        $hoy      = Carbon::now('America/Argentina/Buenos_Aires');
        $diasRest = $hoy->diffInDays($this->fechafin, false);
        return $diasRest >= 0 && $diasRest <= 30;
    }

    public function yaTermino(): bool
    {
        return Carbon::now('America/Argentina/Buenos_Aires')->toDateString()
             > $this->fechafin->toDateString();
    }

    /**
     * Crear ciclo lectivo para el año actual para un usuario nuevo.
     */
    public static function crearParaUsuario(int $userId): self
    {
        $anio = Carbon::now('America/Argentina/Buenos_Aires')->year;

        return self::firstOrCreate(
            ['user_id' => $userId, 'anio' => (string) $anio],
            [
                'fechainicio' => "{$anio}-03-01",
                'fechafin'    => "{$anio}-12-15",
                'activo'      => true,
            ]
        );
    }
}
