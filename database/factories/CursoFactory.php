<?php

namespace Database\Factories;

use App\Models\Curso;
use App\Models\User;
use App\Models\Nivel;            // <-- ASEGÚRATE DE QUE ESTA LÍNEA ESTÉ ASÍ
use App\Models\Especialidad;     // <-- Y ESTA
use App\Models\Establecimiento;  // <-- Y ESTA
use Illuminate\Database\Eloquent\Factories\Factory;

class CursoFactory extends Factory
{
    protected $model = Curso::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(), 
            'nivel_id' => Nivel::factory(), 
            'especialidad_id' => Especialidad::factory(),
            'establecimiento_id' => Establecimiento::factory(),
            
            'nombre' => $this->faker->randomElement(['Informática', 'Electromecánica', 'Gestión']),
            'anio' => $this->faker->randomElement(['1° Año', '2° Año', '3° Año']),
            'division' => $this->faker->randomElement(['A', 'B', 'C']),
            'turno' => $this->faker->randomElement(['Mañana', 'Tarde', 'Noche']),
        ];
    }
}