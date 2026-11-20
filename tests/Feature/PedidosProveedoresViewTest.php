<?php

namespace Tests\Feature;

use App\Livewire\Proveedores\Pedidos;
use Livewire\Livewire;
use Tests\TestCase;

class PedidosProveedoresViewTest extends TestCase
{
    public function test_pantalla_de_pedidos_carga_con_estados_y_pestanas(): void
    {
        $response = $this->get('/proveedores/pedidos');

        $response->assertStatus(200);
        $response->assertSee('Pedidos a Proveedores');
        $response->assertSee('P-0101');
        $response->assertSee('Pendiente');
        $response->assertSee('Recibido');
        $response->assertSeeHtml('/proveedores/cambios');
    }

    public function test_el_directorio_de_proveedores_enlaza_a_pedidos_y_cambios(): void
    {
        $this->get('/proveedores')
            ->assertStatus(200)
            ->assertSeeHtml('/proveedores/pedidos')
            ->assertSeeHtml('/proveedores/cambios');
    }

    public function test_filtro_por_estado(): void
    {
        Livewire::test(Pedidos::class)
            ->set('filtroEstado', 'Pendiente')
            ->assertSee('P-0101')
            ->assertDontSee('P-0102');
    }

    public function test_filtro_por_proveedor(): void
    {
        Livewire::test(Pedidos::class)
            ->set('filtroProveedor', '1')
            ->assertSee('P-0101')
            ->assertSee('P-0104')
            ->assertDontSee('P-0102');
    }

    public function test_busqueda_por_numero_de_pedido(): void
    {
        Livewire::test(Pedidos::class)
            ->set('buscar', 'p-0105')
            ->assertSee('P-0105')
            ->assertDontSee('P-0101');
    }

    public function test_busqueda_por_modelo_ignora_tildes_y_mayusculas(): void
    {
        Livewire::test(Pedidos::class)
            ->set('buscar', 'REMERA basica')
            ->assertSee('P-0102')
            ->assertSee('P-0103')
            ->assertDontSee('P-0101');
    }

    public function test_busqueda_sin_resultados_muestra_estado_vacio(): void
    {
        Livewire::test(Pedidos::class)
            ->set('buscar', 'zzzz-no-existe')
            ->assertSee('No se encontraron pedidos')
            ->call('limpiarFiltros')
            ->assertSee('P-0101');
    }

    public function test_detalle_del_pedido_se_despliega_y_se_oculta(): void
    {
        Livewire::test(Pedidos::class)
            ->assertDontSee('Ojotas Clásicas Capioví')
            ->call('alternarPedido', 'P-0101')
            ->assertSee('Ojotas Clásicas Capioví')
            ->assertSee('39/40')
            ->call('alternarPedido', 'P-0101')
            ->assertDontSee('39/40');
    }

    public function test_marcar_pedido_pendiente_como_recibido_no_toca_el_stock(): void
    {
        Livewire::test(Pedidos::class)
            ->assertSet('pedidos.0.estado', 'Pendiente')
            ->call('marcarRecibido', 'P-0101')
            ->assertSet('pedidos.0.estado', 'Recibido')
            ->assertSee('marcado como recibido')
            ->assertSee('no se modificó el stock');
    }

    public function test_un_pedido_ya_recibido_no_cambia(): void
    {
        Livewire::test(Pedidos::class)
            ->call('marcarRecibido', 'P-0102')
            ->assertSet('pedidos.1.estado', 'Recibido')
            ->assertSet('mensajeExito', '');
    }
}
