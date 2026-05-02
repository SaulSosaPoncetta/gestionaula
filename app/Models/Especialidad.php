<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Especialidad extends Model
{
    protected $table = 'especialidades';
    
    protected $fillable = ['nombre', 'descripcion'];
    
    public function materias()
    {
        return $this->hasMany(Materia::class);
    }

    public function cursos()
    {
        return $this->hasMany(Curso::class);
    }
}