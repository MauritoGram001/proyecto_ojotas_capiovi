<?php

namespace Tests\Feature;

use App\Livewire\PedidosMayoristas\Index;
use Livewire\Livewire;
use Tests\TestCase;

class PedidosMayoristasViewTest extends TestCase
{
    public function test_pantalla_carga_con_aviso_de_no_reserva_de_stock(): void
    {
        $response = $this->get('/pedidos-mayoristas');

        $response->assertStatus(200);
        $response->assertSee('Pedidos Mayoristas');
        $response->assertSee('NO reservan stock');
        $response->assertSee('PM-001');
    }

    public function test_filtro_por_estado(): void
    {
        Livewire::test(Index::class)
            ->set('filtroEstado', 'Entregado')
            ->assertSee('PM-003')
            ->assertDontSee('PM-001');
    }

    public function test_cambio_de_estado_visual(): void
    {
        Livewire::test(Index::class)
            ->assertSet('pedidos.0.estado', 'Pendiente')
            ->call('cambiarEstado', 1, 'Preparado')
            ->assertSet('pedidos.0.estado', 'Preparado')
            ->call('cambiarEstado', 1, 'Entregado')
            ->assertSet('pedidos.0.estado', 'Entregado');
    }

    public function test_detalle_muestra_variantes_y_no_reserva_de_stock(): void
    {
        Livewire::test(Index::class)
            ->call('verDetalle', 1)
            ->assertSee('Detalle PM-001')
            ->assertSee('Ojotas Clásicas Capioví')
            ->assertSee('39/40')
            ->assertSee('Azul')
            ->assertSee('Total del pedido')
            ->assertSee('$158.000')
            ->assertSee('no reserva stock');
    }

    public function test_conversion_simulada_a_venta_confirmada(): void
    {
        Livewire::test(Index::class)
            ->call('convertirAVenta', 1)
            ->assertSet('pedidos.0.convertido', true)
            ->assertSet('pedidos.0.estado', 'Entregado')
            ->assertSee('Venta confirmada (simulada)');
    }

    public function test_cancelar_pedido(): void
    {
        Livewire::test(Index::class)
            ->call('cambiarEstado', 2, 'Cancelado')
            ->assertSet('pedidos.1.estado', 'Cancelado');
    }
}
