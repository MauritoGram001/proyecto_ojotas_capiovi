<?php

namespace Tests\Feature;

use App\Livewire\Proveedores\Index;
use Livewire\Livewire;
use Tests\TestCase;

class ProveedoresViewTest extends TestCase
{
    public function test_pantalla_proveedores_carga_con_datos_de_contacto(): void
    {
        $response = $this->get('/proveedores');

        $response->assertStatus(200);
        $response->assertSee('Proveedores');
        $response->assertSee('Calzados Río Paraná S.A.');
        $response->assertSee('Hernán Giménez');
        $response->assertSee('376-4421900');
    }

    public function test_busqueda_por_nombre_de_proveedor_o_contacto(): void
    {
        Livewire::test(Index::class)
            ->set('buscar', 'rio parana')
            ->assertSee('Calzados Río Paraná S.A.')
            ->assertDontSee('Textil Norte Argentino')
            ->set('buscar', 'silvia')
            ->assertSee('Textil Norte Argentino')
            ->assertDontSee('Calzados Río Paraná S.A.');
    }

    public function test_busqueda_por_telefono_ignora_guiones(): void
    {
        Livewire::test(Index::class)
            ->set('buscar', '3764421900')
            ->assertSee('Calzados Río Paraná S.A.')
            ->assertDontSee('Textil Norte Argentino');
    }

    public function test_filtro_por_rubro(): void
    {
        Livewire::test(Index::class)
            ->set('filtroRubro', 'Textil')
            ->assertSee('Textil Norte Argentino')
            ->assertDontSee('Calzados Río Paraná S.A.');
    }

    public function test_alta_simulada_de_proveedor(): void
    {
        Livewire::test(Index::class)
            ->call('abrirFormulario')
            ->set('formNombre', 'Proveedor De Prueba')
            ->set('formRubro', 'Textil')
            ->set('formContacto', 'Ana Prueba')
            ->call('guardarSimulado')
            ->assertHasNoErrors()
            ->assertSee('Proveedor De Prueba')
            ->assertSee('Proveedor simulado agregado');
    }

    public function test_nombre_de_proveedor_es_obligatorio(): void
    {
        Livewire::test(Index::class)
            ->call('abrirFormulario')
            ->call('guardarSimulado')
            ->assertHasErrors(['formNombre' => 'required']);
    }

    public function test_email_invalido_se_rechaza(): void
    {
        Livewire::test(Index::class)
            ->call('abrirFormulario')
            ->set('formNombre', 'Proveedor Con Email Malo')
            ->set('formEmail', 'esto-no-es-un-email')
            ->call('guardarSimulado')
            ->assertHasErrors(['formEmail' => 'email']);
    }
}
