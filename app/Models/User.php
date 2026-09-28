<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = ['name', 'email', 'password', 'activo'];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'activo'            => 'boolean',
    ];

    public function suscripcion()
    {
        return $this->hasOne(Suscripcion::class)->latest();
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class);
    }

    /**
     * Retorna el paso de onboarding del docente.
     * 0 = sin ciclo lectivo
     * 1 = tiene ciclo lectivo, sin designaciones
     * 2 = tiene designaciones, sin horarios
     * 3 = tiene horarios → todo habilitado
     */
    public function onboardingStep(): int
    {
        try {
            if (\App\Models\Horario::where('user_id', $this->id)->exists()) {
                return 3;
            }
            if (\App\Models\Designacion::where('user_id', $this->id)->exists()) {
                return 2;
            }
            if (\App\Models\CicloLectivo::where('user_id', $this->id)->exists()) {
                return 1;
            }
            return 0;
        } catch (\Throwable $e) {
            return 3; // Si falla, asumir completo para no bloquear
        }
    }

    public function onboardingCompleto(): bool
    {
        try {
            return $this->onboardingStep() >= 3;
        } catch (\Throwable $e) {
            return true;
        }
    }
}