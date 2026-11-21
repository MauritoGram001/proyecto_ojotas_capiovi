<?php

namespace Tests\Feature;

use App\Livewire\CuentasCorrientes\Index;
use Livewire\Livewire;
use Tests\TestCase;

class CuentasCorrientesViewTest extends TestCase
{
    public function test_pantalla_cuentas_carga_correctamente(): void
    {
        $response = $this->get('/cuentas-corrientes');

        $response->assertStatus(200);
        $response->assertSee('Cuentas Corrientes & Cobros', false);
        $response->assertSee('María González');
        $response->assertSee('Deudas con proveedores');
    }

    public function test_saldo_y_movimientos_por_cliente(): void
    {
        Livewire::test(Index::class)
            ->assertSee('$18.500')
            ->assertSee('Venta de ojotas')
            ->set('clienteId', '3')
            ->assertSee('$94.500')
            ->assertSee('Pedido mayorista');
    }

    public function test_deudas_con_proveedores(): void
    {
        Livewire::test(Index::class)
            ->set('tab', 'proveedores')
            ->assertSee('Fábrica de Calzados Misiones S.R.L.')
            ->assertSee('$125.000')
            ->assertSee('Insumos Capioví');
    }

    public function test_modal_de_cobro_incluye_formulario_combinado(): void
    {
        Livewire::test(Index::class)
            ->call('abrirCobro')
            ->assertSet('mostrarCobro', true)
            ->assertSee('Registrar cobro')
            ->assertSee('Desglose de medios de pago')
            ->assertSee('Imputar a boletas pendientes');
    }
}
