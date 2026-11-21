<?php

namespace Tests\Feature;

use App\Livewire\Ventas\Crear;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    private function comoInvitado(): void
    {
        auth()->guard('web')->logout();
        $this->app['auth']->forgetGuards();
    }

    private function usuarioConRol(string $rol): User
    {
        return User::factory()->create(['role' => $rol]);
    }

    public function test_la_pantalla_de_ingreso_es_publica_y_tiene_etiquetas_accesibles(): void
    {
        $this->comoInvitado();

        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Acceso interno de gestión');
        $response->assertSee('for="email"', false);
        $response->assertSee('for="password"', false);
        $response->assertSee('Ingresar');
    }

    public function test_credenciales_incorrectas_no_inician_sesion(): void
    {
        $this->comoInvitado();

        $response = $this->post('/login', [
            'email' => 'dueno@ojotascapiovi.test',
            'password' => 'contraseña-errónea',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
        $this->get('/')->assertRedirect('/login');
    }

    public function test_credenciales_correctas_inician_sesion_y_redirigen_al_inicio(): void
    {
        $this->comoInvitado();
        $usuario = $this->usuarioConRol(User::ROL_DUENO);

        $response = $this->post('/login', [
            'email' => $usuario->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($usuario);
        $this->get('/')->assertStatus(200);
    }

    public function test_cerrar_sesion_bloquea_el_acceso_a_las_vistas(): void
    {
        $this->post('/logout')->assertRedirect('/login');

        $this->assertGuest();
        $this->get('/productos')->assertRedirect('/login');
    }

    public function test_todas_las_vistas_de_gestion_exigen_autenticacion(): void
    {
        $this->comoInvitado();

        foreach (['/', '/productos', '/clientes', '/ventas/crear', '/ventas/postventa', '/usuarios', '/informes'] as $ruta) {
            $this->get($ruta)->assertRedirect('/login');
        }
    }

    public function test_el_middleware_de_roles_verifica_los_permisos_en_servidor(): void
    {
        $this->actingAs($this->usuarioConRol(User::ROL_ADMINISTRADOR));
        $this->get('/usuarios')->assertOk();
        $this->get('/proveedores')->assertOk();

        $this->actingAs($this->usuarioConRol(User::ROL_DUENO));
        $this->get('/usuarios')->assertOk();
        $this->get('/informes')->assertOk();
        $this->get('/proveedores')->assertForbidden();

        $this->actingAs($this->usuarioConRol(User::ROL_EMPLEADO));
        $this->get('/ventas/crear')->assertOk();
        $this->get('/productos')->assertOk();
        $this->get('/usuarios')->assertForbidden();
        $this->get('/informes')->assertForbidden();
    }

    public function test_la_politica_de_ventas_permite_descuentos_y_cambios_de_precio_a_todo_usuario_autenticado(): void
    {
        foreach (User::ROLES as $rol) {
            $usuario = $this->usuarioConRol($rol);

            $this->assertTrue(Gate::forUser($usuario)->allows('create', Venta::class), $rol);
            $this->assertTrue(Gate::forUser($usuario)->allows('aplicarDescuento', Venta::class), $rol);
            $this->assertTrue(Gate::forUser($usuario)->allows('cambiarPrecio', Venta::class), $rol);
        }
    }

    public function test_solo_administrador_y_dueno_pueden_anular_una_venta(): void
    {
        $venta = new Venta();

        $this->assertTrue(Gate::forUser($this->usuarioConRol(User::ROL_ADMINISTRADOR))->allows('anular', $venta));
        $this->assertTrue(Gate::forUser($this->usuarioConRol(User::ROL_DUENO))->allows('anular', $venta));
        $this->assertFalse(Gate::forUser($this->usuarioConRol(User::ROL_EMPLEADO))->allows('anular', $venta));
    }

    public function test_el_pos_aplica_descuentos_y_precios_manuales_para_cualquier_rol(): void
    {
        foreach (User::ROLES as $rol) {
            $this->actingAs($this->usuarioConRol($rol));

            Livewire::test(Crear::class)
                ->set('productoSeleccionado', '101')
                ->call('agregarLinea')
                ->set('lineas.0.precio_unitario', 5000)
                ->set('lineas.0.descuento', 10)
                ->assertSet('lineas.0.precio_unitario', 5000.0)
                ->assertSet('lineas.0.descuento', 10.0);
        }
    }
}
