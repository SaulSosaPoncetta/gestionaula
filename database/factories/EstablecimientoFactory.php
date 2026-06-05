<?php

namespace Database\Factories;
use App\Models\Nivel;
use App\Models\Establecimiento;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Establecimiento>
 */
class EstablecimientoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return ['nombre' => $this->faker->company,
        'nivel_id' => Nivel::factory()]; // O el campo que use tu tabla
        
        }
}
