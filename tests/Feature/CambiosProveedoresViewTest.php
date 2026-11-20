<?php

namespace Tests\Feature;

use App\Livewire\Proveedores\Cambios;
use Livewire\Livewire;
use Tests\TestCase;

class CambiosProveedoresViewTest extends TestCase
{
    public function test_pantalla_de_cambios_carga_con_los_tres_estados(): void
    {
        $response = $this->get('/proveedores/cambios');

        $response->assertStatus(200);
        $response->assertSee('Mercadería Defectuosa y Cambios');
        $response->assertSee('D-0001');
        $response->assertSee('Stock defectuoso');
        $response->assertSee('Enviada a cambio');
        $response->assertSee('Reemplazo recibido');
        $response->assertSee('no se suma al stock vendible');
    }

    public function test_resumen_cuenta_las_unidades_en_stock_defectuoso(): void
    {
        Livewire::test(Cambios::class)->assertSee('2 unidad(es) en stock defectuoso');
    }

    public function test_filtro_por_estado(): void
    {
        Livewire::test(Cambios::class)
            ->set('filtroEstado', 'Enviada a cambio')
            ->assertSee('D-0002')
            ->assertDontSee('D-0001');
    }

    public function test_registrar_mercaderia_defectuosa_queda_en_stock_defectuoso(): void
    {
        Livewire::test(Cambios::class)
            ->call('abrirFormulario')
            ->set('formVariante', '203')
            ->set('formCantidad', 4)
            ->set('formProveedor', '3')
            ->set('formMotivo', 'Costura floja')
            ->call('registrarSimulado')
            ->assertHasNoErrors()
            ->assertCount('registros', 4)
            ->assertSet('registros.3.id', 'D-0004')
            ->assertSet('registros.3.estado', 'Stock defectuoso')
            ->assertSee('D-0004')
            ->assertSee('6 unidad(es) en stock defectuoso');
    }

    public function test_el_formulario_exige_producto_proveedor_y_falla(): void
    {
        Livewire::test(Cambios::class)
            ->call('abrirFormulario')
            ->call('registrarSimulado')
            ->assertHasErrors([
                'formVariante' => 'required',
                'formProveedor' => 'required',
                'formMotivo' => 'required',
            ])
            ->assertCount('registros', 3);
    }

    public function test_la_cantidad_minima_es_uno(): void
    {
        Livewire::test(Cambios::class)
            ->call('abrirFormulario')
            ->set('formVariante', '203')
            ->set('formProveedor', '3')
            ->set('formMotivo', 'Costura floja')
            ->set('formCantidad', 0)
            ->call('registrarSimulado')
            ->assertHasErrors(['formCantidad' => 'min']);
    }

    public function test_producto_inexistente_se_rechaza(): void
    {
        Livewire::test(Cambios::class)
            ->call('abrirFormulario')
            ->set('formVariante', '999')
            ->set('formProveedor', '3')
            ->set('formMotivo', 'Costura floja')
            ->call('registrarSimulado')
            ->assertHasErrors(['formVariante'])
            ->assertCount('registros', 3);
    }

    public function test_origen_invalido_se_rechaza(): void
    {
        Livewire::test(Cambios::class)
            ->call('abrirFormulario')
            ->set('formVariante', '203')
            ->set('formProveedor', '3')
            ->set('formMotivo', 'Costura floja')
            ->set('formOrigen', 'otro')
            ->call('registrarSimulado')
            ->assertHasErrors(['formOrigen' => 'in']);
    }

    public function test_el_circuito_avanza_de_defectuoso_a_enviado_y_a_reemplazo_recibido(): void
    {
        Livewire::test(Cambios::class)
            ->assertSet('registros.0.estado', 'Stock defectuoso')
            ->call('enviarACambio', 'D-0001')
            ->assertSet('registros.0.estado', 'Enviada a cambio')
            ->assertSee('0 unidad(es) en stock defectuoso')
            ->call('registrarReemplazo', 'D-0001')
            ->assertSet('registros.0.estado', 'Reemplazo recibido')
            ->assertSee('el stock vendible no cambia');
    }

    public function test_no_se_puede_saltear_pasos_del_circuito(): void
    {
        Livewire::test(Cambios::class)
            ->call('registrarReemplazo', 'D-0001')           // todavía no se envió
            ->assertSet('registros.0.estado', 'Stock defectuoso')
            ->assertSet('mensajeExito', '')
            ->call('enviarACambio', 'D-0003')                // ya está cerrado
            ->assertSet('registros.2.estado', 'Reemplazo recibido')
            ->assertSet('mensajeExito', '');
    }
}
