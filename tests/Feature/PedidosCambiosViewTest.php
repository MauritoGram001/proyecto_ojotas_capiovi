<?php

namespace Tests\Feature;

use App\Livewire\Proveedores\Pedidos;
use Livewire\Livewire;
use Tests\TestCase;

class PedidosCambiosViewTest extends TestCase
{
    public function test_pantalla_de_pedidos_carga_con_estados_visibles(): void
    {
        $response = $this->get('/proveedores/pedidos');

        $response->assertStatus(200);
        $response->assertSee('Pedidos a Proveedores');
        $response->assertSee('PED-001');
        $response->assertSee('Pendiente');
        $response->assertSee('Recibido');
    }

    public function test_filtro_y_marcado_de_pedido_como_recibido(): void
    {
        Livewire::test(Pedidos::class)
            ->set('filtroEstado', 'Recibido')
            ->assertSee('PED-002')
            ->assertDontSee('PED-001')
            ->set('filtroEstado', '')
            ->call('marcarRecibido', 1)
            ->assertSet('pedidos.0.estado', 'Recibido');
    }

    public function test_seccion_de_mercaderia_defectuosa_con_estados(): void
    {
        Livewire::test(Pedidos::class)
            ->set('tab', 'cambios')
            ->assertSee('Mercadería Defectuosa y Cambios')
            ->assertSee('CAM-001')
            ->assertSee('Enviada a cambio')
            ->assertSee('Recibida en reemplazo');
    }

    public function test_avanzar_estado_del_circuito_de_cambio(): void
    {
        Livewire::test(Pedidos::class)
            ->set('tab', 'cambios')
            ->assertSet('cambios.1.estado', 'Detectada')
            ->call('avanzarEstado', 2)
            ->assertSet('cambios.1.estado', 'Enviada a cambio')
            ->call('avanzarEstado', 2)
            ->assertSet('cambios.1.estado', 'Recibida en reemplazo');
    }

    public function test_registro_simulado_de_mercaderia_defectuosa(): void
    {
        Livewire::test(Pedidos::class)
            ->set('tab', 'cambios')
            ->call('abrirFormulario')
            ->set('formProducto', 'Ojotas Playeras — 37/38 Flores')
            ->set('formMotivo', 'Estampado corrido')
            ->set('formCantidad', 4)
            ->call('guardarCambio')
            ->assertSet('mostrarFormulario', false)
            ->assertSee('CAM-004')
            ->assertSee('Estampado corrido');
    }

    public function test_registro_requiere_producto_y_motivo(): void
    {
        Livewire::test(Pedidos::class)
            ->set('tab', 'cambios')
            ->call('abrirFormulario')
            ->set('formProducto', 'Producto incompleto')
            ->call('guardarCambio')
            ->assertSet('mostrarFormulario', true)
            ->assertCount('cambios', 3);
    }

    public function test_enlace_de_pedidos_desde_proveedores(): void
    {
        $this->get('/proveedores')
            ->assertStatus(200)
            ->assertSee('Pedidos y cambios');
    }
}
