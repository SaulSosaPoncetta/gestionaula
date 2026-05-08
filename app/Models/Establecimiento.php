<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Establecimiento extends Model
{
    protected $fillable = [
        'user_id', 'nombre', 'cue', 'modalidad', 'nivel_id',
        'direccion', 'localidad', 'provincia', 'telefono', 'email'
    ];

    public function nivel()
    {
        return $this->belongsTo(Nivel::class);
    }

    public function cursos()
    {
        return $this->hasMany(Curso::class);
    }
}