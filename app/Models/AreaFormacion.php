<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class AreaFormacion extends Model
{
    protected $table = 'areasformacion';
    protected $fillable = ['user_id', 'nombre', 'descripcion'];

    public function materias()
    {
        return $this->hasMany(Materia::class, 'area_formacion_id');
    }
}