<?php

namespace Database\Factories;

use App\Models\Alumno;
use App\Models\User; // Asumo que tienes un modelo User
use App\Models\Curso; // Asumo que tienes un modelo Curso
use Illuminate\Database\Eloquent\Factories\Factory;

class AlumnoFactory extends Factory
{
    protected $model = Alumno::class;

    public function definition()
    {
        return [
            // Genera IDs automáticos de usuarios y cursos creados al vuelo para la prueba
            'user_id' => User::factory(), 
            'curso_id' => Curso::factory(),
            
            'nombre' => $this->faker->firstName,
            'apellido' => $this->faker->lastName,
            'fechanacimiento' => $this->faker->date('Y-m-d', '-15 years'), // alumno de aprox 15 años
            'telefono' => $this->faker->phoneNumber,
            'email' => $this->faker->unique()->safeEmail,
            'porcentajeasistencia' => $this->faker->randomFloat(2, 0, 100), // decimal entre 0.00 y 100.00
            'tipocursada' => $this->faker->randomElement(['regular', 'libre', 'recursa', 'intensifica']),
        ];
    }
}