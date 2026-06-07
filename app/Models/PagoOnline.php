<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PagoOnline extends Model
{
    protected $table = 'pagos_online';

    protected $fillable = [
        'user_id', 'suscripcion_id', 'plataforma', 'external_id',
        'preference_id', 'monto', 'moneda', 'estado', 'metodo_pago',
        'datos_extra', 'periododesde', 'periodohasta', 'fecha_aprobacion'
    ];

    protected $casts = [
        'datos_extra'      => 'array',
        'periododesde'     => 'date',
        'periodohasta'     => 'date',
        'fecha_aprobacion' => 'datetime',
    ];

    const ESTADOS = [
        'pendiente'    => 'Pendiente',
        'aprobado'     => 'Aprobado',
        'rechazado'    => 'Rechazado',
        'cancelado'    => 'Cancelado',
        'reembolsado'  => 'Reembolsado',
    ];

    const BADGES = [
        'pendiente'   => 'warning',
        'aprobado'    => 'success',
        'rechazado'   => 'danger',
        'cancelado'   => 'secondary',
        'reembolsado' => 'info',
    ];

    public function user()        { return $this->belongsTo(User::class); }
    public function suscripcion() { return $this->belongsTo(Suscripcion::class); }

    public function getEstadolabelAttribute(): string
    {
        return self::ESTADOS[$this->estado] ?? ucfirst($this->estado);
    }

    public function getEstadobadgeAttribute(): string
    {
        return self::BADGES[$this->estado] ?? 'secondary';
    }
}