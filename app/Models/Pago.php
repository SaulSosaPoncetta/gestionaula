<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $fillable = [
        'user_id', 'suscripcion_id', 'monto', 'fechapago',
        'periododesde', 'periodohasta', 'estado',
        'metodopago', 'comprobante', 'observaciones'
    ];

    protected $casts = [
        'fechapago'   => 'date',
        'periododesde'=> 'date',
        'periodohasta'=> 'date',
    ];

    const ESTADOS = [
        'pendiente' => 'Pendiente',
        'pagado'    => 'Pagado',
        'vencido'   => 'Vencido',
    ];

    const BADGES = [
        'pendiente' => 'warning',
        'pagado'    => 'success',
        'vencido'   => 'danger',
    ];

    const METODOS = [
        'efectivo'      => 'Efectivo',
        'transferencia' => 'Transferencia',
        'tarjeta'       => 'Tarjeta',
        'otro'          => 'Otro',
    ];

    public function user()         { return $this->belongsTo(User::class); }
    public function suscripcion()  { return $this->belongsTo(Suscripcion::class); }

    public function getEstadolabelAttribute(): string
    {
        return self::ESTADOS[$this->estado] ?? ucfirst($this->estado);
    }

    public function getEstadobadgeAttribute(): string
    {
        return self::BADGES[$this->estado] ?? 'secondary';
    }
}