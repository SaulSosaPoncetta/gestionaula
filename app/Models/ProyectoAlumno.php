<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProyectoAlumno extends Model
{
    protected $table = 'proyectoalumnos';

    protected $fillable = ['proyecto_id', 'alumno_id'];

    public function proyecto() { return $this->belongsTo(Proyecto::class); }
    public function alumno()   { return $this->belongsTo(Alumno::class); }
}