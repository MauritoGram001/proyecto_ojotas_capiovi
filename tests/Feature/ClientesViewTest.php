<?php

namespace Tests\Feature;

use App\Livewire\Clientes\Index;
use Livewire\Livewire;
use Tests\TestCase;

class ClientesViewTest extends TestCase
{
    public function test_pantalla_clientes_carga_con_listado_de_ejemplo(): void
    {
        $response = $this->get('/clientes');

        $response->assertStatus(200);
        $response->assertSee('Clientes');
        $response->assertSee('María Fernández');
        $response->assertSee('Almacén Don Rubén');
        $response->assertSee('Consumidor final');
    }

    public function test_cuit_solo_se_muestra_para_clientes_mayoristas(): void
    {
        Livewire::test(Index::class)
            ->call('abrirFormulario')
            ->assertSet('formTipo', 'Minorista')
            ->assertDontSeeHtml('wire:model="formCuit"')
            ->set('formTipo', 'Mayorista')
            ->assertSeeHtml('wire:model="formCuit"')
            ->set('formTipo', 'Minorista')
            ->assertDontSeeHtml('wire:model="formCuit"');
    }

    public function test_cliente_mayorista_sin_cuit_no_se_puede_guardar(): void
    {
        Livewire::test(Index::class)
            ->call('abrirFormulario')
            ->set('formNombre', 'Mayorista Sin CUIT')
            ->set('formTipo', 'Mayorista')
            ->call('guardarSimulado')
            ->assertHasErrors(['formCuit' => 'required'])
            ->assertCount('clientes', 7)
            ->assertDontSee('Cliente simulado agregado');
    }

    public function test_cliente_mayorista_con_cuit_se_agrega_a_la_maqueta(): void
    {
        Livewire::test(Index::class)
            ->call('abrirFormulario')
            ->set('formNombre', 'Mayorista Con CUIT')
            ->set('formTipo', 'Mayorista')
            ->set('formCuit', '30-11111111-1')
            ->call('guardarSimulado')
            ->assertHasNoErrors()
            ->assertCount('clientes', 8)
            ->assertSee('CUIT 30-11111111-1')
            ->assertSee('Cliente simulado agregado');
    }

    public function test_cliente_minorista_se_guarda_sin_cuit(): void
    {
        Livewire::test(Index::class)
            ->call('abrirFormulario')
            ->set('formNombre', 'Minorista De Prueba')
            ->call('guardarSimulado')
            ->assertHasNoErrors()
            ->assertCount('clientes', 8);
    }

    public function test_nombre_es_obligatorio(): void
    {
        Livewire::test(Index::class)
            ->call('abrirFormulario')
            ->call('guardarSimulado')
            ->assertHasErrors(['formNombre' => 'required'])
            ->assertCount('clientes', 7);
    }

    public function test_cambiar_a_minorista_limpia_el_cuit_ingresado(): void
    {
        Livewire::test(Index::class)
            ->call('abrirFormulario')
            ->set('formTipo', 'Mayorista')
            ->set('formCuit', '20-22222222-2')
            ->set('formTipo', 'Minorista')
            ->assertSet('formCuit', '');
    }

    public function test_busqueda_por_nombre_ignora_tildes_y_mayusculas(): void
    {
        Livewire::test(Index::class)
            ->set('buscar', 'maria fernandez')
            ->assertSee('3743-421587')
            ->assertDontSee('3743-556201');
    }

    public function test_busqueda_por_telefono_ignora_guiones(): void
    {
        Livewire::test(Index::class)
            ->set('buscar', '374342 1587')
            ->assertSee('3743-421587')
            ->assertDontSee('3743-556201');
    }

    public function test_filtro_por_tipo_de_cliente(): void
    {
        Livewire::test(Index::class)
            ->set('filtroTipo', 'Mayorista')
            ->assertSee('CUIT 30-71234567-8')
            ->assertDontSee('3743-421587');
    }

    public function test_busqueda_sin_resultados_muestra_estado_vacio(): void
    {
        Livewire::test(Index::class)
            ->set('buscar', 'zzzz-no-existe')
            ->assertSee('No se encontraron clientes')
            ->call('limpiarFiltros')
            ->assertSee('3743-421587');
    }

    public function test_selector_de_venta_rapida_permite_consumidor_final_o_cliente(): void
    {
        Livewire::test(Index::class)
            ->assertSet('clienteVenta', Index::CONSUMIDOR_FINAL)
            ->assertSee('La venta se registra sin asociar un cliente.')
            ->set('clienteVenta', '3')
            ->assertSee('La venta quedaría asociada a')
            ->assertDontSee('La venta se registra sin asociar un cliente.');
    }
}
