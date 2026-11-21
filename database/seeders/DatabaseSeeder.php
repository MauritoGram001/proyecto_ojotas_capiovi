<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Cuentas locales de demostración: la contraseña es "capiovi2026" para las tres.
        $usuarios = [
            ['name' => 'Dueño', 'email' => 'dueno@ojotascapiovi.test', 'role' => User::ROL_DUENO],
            ['name' => 'Administrador', 'email' => 'administrador@ojotascapiovi.test', 'role' => User::ROL_ADMINISTRADOR],
            ['name' => 'Empleado de local', 'email' => 'empleado@ojotascapiovi.test', 'role' => User::ROL_EMPLEADO],
        ];

        foreach ($usuarios as $usuario) {
            User::updateOrCreate(
                ['email' => $usuario['email']],
                $usuario + [
                    'password' => Hash::make('capiovi2026'),
                ],
            );
        }

        $this->call([
            DirectorioSeeder::class,
            CatalogoSeeder::class,
            CuentaCorrienteSeeder::class,
        ]);
    }
}
