<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CrearSuperAdmin extends Command
{
    protected $signature   = 'gestionaula:superadmin
                                {--name=Super Admin : Nombre del usuario}
                                {--email= : Email del superadmin}
                                {--password= : Contraseña (mínimo 8 caracteres)}';

    protected $description = 'Crea o actualiza el usuario Super Admin de GestiónAula';

    public function handle(): int
    {
        $this->info('');
        $this->info('╔══════════════════════════════════════╗');
        $this->info('║   GestiónAula — Crear Super Admin    ║');
        $this->info('╚══════════════════════════════════════╝');
        $this->info('');

        // ── Datos del usuario ──────────────────────────────────
        $name = $this->option('name') ?: $this->ask('Nombre del superadmin', 'Super Admin');

        $email = $this->option('email') ?: $this->ask('Email del superadmin');
        while (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Email inválido. Intentá de nuevo.');
            $email = $this->ask('Email del superadmin');
        }

        $password = $this->option('password') ?: $this->secret('Contraseña (mínimo 8 caracteres)');
        while (strlen($password) < 8) {
            $this->error('La contraseña debe tener al menos 8 caracteres.');
            $password = $this->secret('Contraseña');
        }

        // ── Crear roles si no existen ──────────────────────────
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $rolAdmin   = Role::firstOrCreate(['name' => 'admin',   'guard_name' => 'web']);
        $rolDocente = Role::firstOrCreate(['name' => 'docente', 'guard_name' => 'web']);

        $this->line('✓ Roles verificados: admin, docente');

        // ── Crear o actualizar usuario ─────────────────────────
        $existe = User::where('email', $email)->first();

        if ($existe) {
            $this->warn("El usuario {$email} ya existe. ¿Actualizarlo?");
            if (!$this->confirm('¿Actualizar datos y rol?', true)) {
                $this->info('Operación cancelada.');
                return self::SUCCESS;
            }

            $existe->update([
                'name'     => $name,
                'password' => Hash::make($password),
                'activo'   => true,
            ]);
            $existe->syncRoles([$rolAdmin]);
            $user = $existe;
            $this->info("✓ Usuario actualizado: {$email}");
        } else {
            $user = User::create([
                'name'              => $name,
                'email'             => $email,
                'password'          => Hash::make($password),
                'email_verified_at' => now(),
                'activo'            => true,
            ]);
            $user->assignRole($rolAdmin);
            $this->info("✓ Usuario creado: {$email}");
        }

        // ── Resumen ────────────────────────────────────────────
        $this->info('');
        $this->table(
            ['Campo', 'Valor'],
            [
                ['ID',       $user->id],
                ['Nombre',   $user->name],
                ['Email',    $user->email],
                ['Rol',      'admin'],
                ['Activo',   'Sí'],
            ]
        );

        $this->info('');
        $this->info('🎉 Super Admin listo. Podés iniciar sesión en:');
        $this->info('   ' . config('app.url') . '/login');
        $this->info('');

        return self::SUCCESS;
    }
}
