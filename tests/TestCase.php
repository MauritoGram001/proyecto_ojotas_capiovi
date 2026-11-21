<?php

namespace Tests;

use App\Models\User;
use Database\Seeders\CatalogoSeeder;
use Database\Seeders\DirectorioSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected User $usuario;

    protected function setUp(): void
    {
        parent::setUp();

        // Las pruebas de las vistas se ejecutan con un usuario con permisos
        // completos; las pruebas de acceso pueden suplantarlo o cerrar sesión.
        $this->usuario = User::factory()->create([
            'role' => User::ROL_ADMINISTRADOR,
        ]);

        $this->actingAs($this->usuario);
        $this->seed([
            DirectorioSeeder::class,
            CatalogoSeeder::class,
        ]);
    }
}
