<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarpetaCampoEntrada extends Model
{
    protected $table = 'carpetacampoentradas';

    protected $fillable = [
        'carpeta_id', 'user_id', 'tipo', 'titulo',
        'descripcion', 'archivo', 'fecha', 'orden'
    ];

    protected $casts = ['fecha' => 'date'];

    const TIPOS = [
        'nota'        => 'Nota',
        'documento'   => 'Documento',
        'imagen'      => 'Imagen',
        'actividad'   => 'Actividad',
        'seguimiento' => 'Seguimiento',
    ];

    const BADGES = [
        'nota'        => 'secondary',
        'documento'   => 'primary',
        'imagen'      => 'info',
        'actividad'   => 'success',
        'seguimiento' => 'warning',
    ];

    const ICONOS = [
        'nota'        => 'bi-sticky',
        'documento'   => 'bi-file-earmark-text',
        'imagen'      => 'bi-image',
        'actividad'   => 'bi-clipboard-check',
        'seguimiento' => 'bi-journal-text',
    ];

    public function carpeta() { return $this->belongsTo(CarpetaCampo::class, 'carpeta_id'); }
    public function docente() { return $this->belongsTo(User::class, 'user_id'); }

    public function getTipolabelAttribute(): string
    {
        return self::TIPOS[$this->tipo] ?? ucfirst($this->tipo);
    }

    public function getTipobadgeAttribute(): string
    {
        return self::BADGES[$this->tipo] ?? 'secondary';
    }

    public function getTipoinconoAttribute(): string
    {
        return self::ICONOS[$this->tipo] ?? 'bi-file';
    }
}