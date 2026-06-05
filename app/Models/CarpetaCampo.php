<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarpetaCampo extends Model
{
    protected $table = 'carpetacampo';

    protected $fillable = [
        'user_id', 'proyecto_id', 'alumno_id',
        'titulo', 'subtitulo', 'descripcion'
    ];

    public function docente()  { return $this->belongsTo(User::class, 'user_id'); }
    public function proyecto() { return $this->belongsTo(Proyecto::class); }
    public function alumno()   { return $this->belongsTo(Alumno::class); }

    public function entradas()
    {
        return $this->hasMany(CarpetaCampoEntrada::class, 'carpeta_id')->orderBy('fecha')->orderBy('orden');
    }
}