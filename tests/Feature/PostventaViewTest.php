<?php

namespace Tests\Feature;

use App\Livewire\Ventas\Postventa;
use Livewire\Livewire;
use Tests\TestCase;

class PostventaViewTest extends TestCase
{
    public function test_pantalla_de_postventa_carga_con_vinculos(): void
    {
        $response = $this->get('/ventas/postventa');

        $response->assertStatus(200);
        $response->assertSee('Anulaciones y Devoluciones');
        $response->assertSee('INT-0001');
        $response->assertSee('ANU-001');
        $response->assertSee('Venta original');
    }

    public function test_enlace_desde_el_pos(): void
    {
        $this->get('/ventas/crear')
            ->assertStatus(200)
            ->assertSee('Anulaciones y Devoluciones');
    }

    public function test_modal_de_anulacion_requiere_confirmacion_y_justificacion(): void
    {
        Livewire::test(Postventa::class)
            ->call('abrirModalAnular', 1)
            ->assertSet('mostrarModalAnular', true)
            ->assertSee('Confirmar anulación')
            ->assertSee('Justificación de la anulación')
            ->call('confirmarAnulacion')
            ->assertSet('mostrarModalAnular', true)
            ->assertSet('ventas.0.estado', 'Confirmada');
    }

    public function test_anulacion_justificada_y_confirmada(): void
    {
        Livewire::test(Postventa::class)
            ->call('abrirModalAnular', 1)
            ->set('justificacionAnulacion', 'Cliente se arrepintió de la compra')
            ->set('confirmacionAnulacion', true)
            ->call('confirmarAnulacion')
            ->assertSet('mostrarModalAnular', false)
            ->assertSet('ventas.0.estado', 'Anulada')
            ->assertSet('anulaciones.1.numero', 'ANU-002')
            ->assertSee('ANU-002')
            ->assertSee('Cliente se arrepintió de la compra');
    }

    public function test_no_se_puede_anular_una_venta_ya_anulada(): void
    {
        Livewire::test(Postventa::class)
            ->call('abrirModalAnular', 3)
            ->assertSet('mostrarModalAnular', false);
    }

    public function test_formulario_de_devolucion_muestra_vinculo_con_venta_original(): void
    {
        Livewire::test(Postventa::class)
            ->set('tab', 'devoluciones')
            ->assertSee('Registrar devolución por cambio')
            ->assertSee('Artículo devuelto')
            ->assertSee('Nuevo entregado')
            ->assertSee('Vinculada a')
            ->assertSee('Stock defectuoso');
    }

    public function test_devolucion_con_falla_de_fabricacion_ingresa_a_stock_defectuoso(): void
    {
        Livewire::test(Postventa::class)
            ->set('tab', 'devoluciones')
            ->set('ventaDevolucionId', '1')
            ->set('itemDevueltoId', '101')
            ->set('nuevoArticuloId', '102')
            ->set('fallaFabricacion', true)
            ->call('registrarDevolucion')
            ->assertSet('devoluciones.1.numero', 'DEV-002')
            ->assertSet('ventas.0.estado', 'Devuelta')
            ->assertSet('stockDefectuoso.1.origen', 'DEV-002')
            ->assertSee('Falla de fabricación declarada en devolución')
            ->assertSee('SD-002');
    }

    public function test_devolucion_sin_falla_no_genera_stock_defectuoso(): void
    {
        Livewire::test(Postventa::class)
            ->set('tab', 'devoluciones')
            ->set('ventaDevolucionId', '2')
            ->set('itemDevueltoId', '401')
            ->set('nuevoArticuloId', '301')
            ->call('registrarDevolucion')
            ->assertSet('devoluciones.1.numero', 'DEV-002')
            ->assertCount('stockDefectuoso', 1);
    }

    public function test_devolucion_requiere_articulos_seleccionados(): void
    {
        Livewire::test(Postventa::class)
            ->set('tab', 'devoluciones')
            ->call('registrarDevolucion')
            ->assertCount('devoluciones', 1);
    }
}
