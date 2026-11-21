<?php

namespace Tests\Feature;

use App\Livewire\Usuarios\Index;
use Livewire\Livewire;
use Tests\TestCase;

class UsuariosViewTest extends TestCase
{
    public function test_pantalla_de_usuarios_carga_con_roles(): void
    {
        $response = $this->get('/usuarios');

        $response->assertStatus(200);
        $response->assertSee('Usuarios y Permisos');
        $response->assertSee('Mauricio Gramajo');
        $response->assertSee('Empleado de local');
        $response->assertSee('Vista previa de permisos');
    }

    public function test_invariante_de_descuento_visible_para_todos_los_roles(): void
    {
        foreach (['Dueño', 'Administrador', 'Empleado de local'] as $rol) {
            Livewire::test(Index::class)
                ->set('rolVistaPrevia', $rol)
                ->assertSee('Descuentos y cambio de precio en ventas')
                ->assertSee('habilitado para este rol');
        }
    }

    public function test_previsualizacion_de_permisos_segun_rol(): void
    {
        Livewire::test(Index::class)
            ->assertSee('Sin restricciones de módulos')
            ->set('rolVistaPrevia', 'Empleado de local')
            ->assertSee('Sin acceso a informes e historial')
            ->assertSee('Sin acceso a administración de usuarios')
            ->assertSee('Productos y existencias (consulta)');
    }

    public function test_cambio_de_rol_de_usuario(): void
    {
        Livewire::test(Index::class)
            ->assertSet('usuarios.2.rol', 'Empleado de local')
            ->call('cambiarRolUsuario', 3, 'Administrador')
            ->assertSet('usuarios.2.rol', 'Administrador');
    }

    public function test_rol_invalido_no_modifica_al_usuario(): void
    {
        Livewire::test(Index::class)
            ->call('cambiarRolUsuario', 1, 'Rol Inexistente')
            ->assertSet('usuarios.0.rol', 'Dueño');
    }

    public function test_activar_y_desactivar_usuario(): void
    {
        Livewire::test(Index::class)
            ->assertSet('usuarios.3.estado', 'Inactivo')
            ->call('toggleEstado', 4)
            ->assertSet('usuarios.3.estado', 'Activo');
    }

    public function test_alta_simulada_de_usuario_con_rol(): void
    {
        Livewire::test(Index::class)
            ->call('abrirFormulario')
            ->assertSet('mostrarFormulario', true)
            ->set('formNombre', 'Valeria Acosta')
            ->set('formEmail', 'valeria@ojotascapiovi.com.ar')
            ->set('formRol', 'Administrador')
            ->call('guardarUsuario')
            ->assertSet('mostrarFormulario', false)
            ->assertSet('usuarios.4.nombre', 'Valeria Acosta')
            ->assertSet('usuarios.4.rol', 'Administrador')
            ->assertSet('usuarios.4.estado', 'Activo')
            ->assertSee('Valeria Acosta');
    }

    public function test_edicion_simulada_de_usuario_existente(): void
    {
        Livewire::test(Index::class)
            ->call('abrirFormulario', 2)
            ->assertSet('formNombre', 'Ana Paiva')
            ->set('formNombre', 'Ana Paiva Actualizada')
            ->call('guardarUsuario')
            ->assertSet('mostrarFormulario', false)
            ->assertSet('usuarios.1.nombre', 'Ana Paiva Actualizada')
            ->assertCount('usuarios', 4);
    }

    public function test_alta_requiere_nombre_y_correo(): void
    {
        Livewire::test(Index::class)
            ->call('abrirFormulario')
            ->set('formNombre', 'Usuario incompleto')
            ->call('guardarUsuario')
            ->assertSet('mostrarFormulario', true)
            ->assertCount('usuarios', 4);
    }
}
