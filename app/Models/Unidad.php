<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unidad extends Model
{
    protected $fillable = ['planificacion_id', 'nombre', 'numeroclases', 'orden'];

    public function planificacion()
    {
        return $this->belongsTo(Planificacion::class);
    }

    public function contenidos()
    {
        return $this->belongsToMany(Contenido::class, 'unidadcontenidos');
    }

    public function objetivosaprendizaje()
    {
        return $this->hasMany(UnidadObjetivoAprendizaje::class)->orderBy('orden');
    }

    public function objetivosensenianza()
    {
        return $this->hasMany(UnidadObjetivoEnsenianza::class)->orderBy('orden');
    }

    public function actividades()
    {
        return $this->hasMany(UnidadActividad::class)->orderBy('orden');
    }

    public function recursos()
    {
        return $this->hasMany(UnidadRecurso::class)->orderBy('orden');
    }

    public function archivos()
    {
        return $this->hasMany(UnidadArchivo::class)->orderBy('orden');
    }
}