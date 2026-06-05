<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Suscripcion extends Model
{
    protected $table = 'suscripciones';
    protected $fillable = [
        'user_id', 'plan_id', 'montomensual', 'estado',
        'fechainicio', 'fechavencimiento', 'proximopago', 'observaciones'
    ];

    protected $casts = [
        'fechainicio'      => 'date',
        'fechavencimiento' => 'date',
        'proximopago'      => 'date',
    ];

    const ESTADOS = [
        'activa'     => 'Activa',
        'suspendida' => 'Suspendida',
        'cancelada'  => 'Cancelada',
    ];

    const BADGES = [
        'activa'     => 'success',
        'suspendida' => 'warning',
        'cancelada'  => 'danger',
    ];

    public function user()   { return $this->belongsTo(User::class); }
    public function plan()   { return $this->belongsTo(Plan::class); }
    public function pagos()  { return $this->hasMany(Pago::class); }

    public function getEstadolabelAttribute(): string
    {
        return self::ESTADOS[$this->estado] ?? ucfirst($this->estado);
    }

    public function getEstadobadgeAttribute(): string
    {
        return self::BADGES[$this->estado] ?? 'secondary';
    }
}