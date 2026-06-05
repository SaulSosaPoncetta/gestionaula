<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Alumno;
use App\Models\Curso; 
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class AlumnoTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_usuario_puede_ver_el_listado_de_alumnos(): void
{
    // 1. ARRANGE
    Role::create(['name' => 'docente']);
    $user = User::factory()->create();
    $user->assignRole('docente');

    // ASOCIO EL CURSO AL USUARIO LOGUEADO
    $curso = Curso::factory()->create([
        'user_id' => $user->id
    ]);

    // ASOCIO EL ALUMNO AL USUARIO LOGUEADO Y AL CURSO
    $alumno = Alumno::factory()->create([
        'nombre' => 'Carlos',
        'apellido' => 'Gómez',
        'user_id' => $user->id, 
        'curso_id' => $curso->id,
        'tipocursada' => 'regular'
    ]);

    // 2. ACT
    $response = $this->actingAs($user)->get('/alumnos');

    // 3. ASSERT
    $response->assertStatus(200);
    $response->assertSee('Carlos');
    $response->assertSee('Gómez');
}

    public function test_se_puede_registrar_un_alumno_a_traves_del_formulario(): void
    {
        // 1. ARRANGE
        Role::create(['name' => 'docente']);
        $user = User::factory()->create();
        $user->assignRole('docente');

        // Creo un curso para asignarle al alumno en el formulario
        $curso = Curso::factory()->create();

        // Estructura con TODOS los datos requeridos por las validaciones
        $datosAlumno = [
            'nombre' => 'María',
            'apellido' => 'Lopez',
            'email' => 'maria@example.com',
            'tipocursada' => 'regular', 
            'curso_id' => $curso->id,   
        ];

        // 2. ACT
        $response = $this->actingAs($user)->post('/alumnos', $datosAlumno);

        // 3. ASSERT
        $response->assertRedirect('/alumnos');
        
        $this->assertDatabaseHas('alumnos', [
            'email' => 'maria@example.com',
            'nombre' => 'María'
        ]);
    }
}