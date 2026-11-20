<?php

namespace Tests\Feature;

use App\Livewire\CuentasCorrientes\Index;
use Livewire\Livewire;
use Tests\TestCase;

class CuentasCorrientesViewTest extends TestCase
{
    public function test_pantalla_carga_con_saldos_y_totales(): void
    {
        $response = $this->get('/cuentas-corrientes');

        $response->assertStatus(200);
        $response->assertSee('Cuentas Corrientes');
        $response->assertSee('Almacén Don Rubén');
        $response->assertSee('$97.000');   // saldo de Almacén Don Rubén
        $response->assertSee('$237.700');  // total a cobrar a clientes
        $response->assertSee('$269.000');  // total a pagar a proveedores
    }

    public function test_cliente_sin_saldo_figura_al_dia(): void
    {
        Livewire::test(Index::class)
            ->assertSee('Kiosco El Sol')
            ->assertSee('Al día');
    }

    public function test_ver_movimientos_de_un_cliente_con_saldo_acumulado(): void
    {
        Livewire::test(Index::class)
            ->call('alternarCuenta', 3)
            ->assertSee('Boleta B-0012')
            ->assertSee('Cobro en transferencia')
            ->assertSee('Boleta B-0019')
            ->assertSee('$55.000'); // saldo acumulado después del cobro
    }

    public function test_los_movimientos_se_pueden_ocultar(): void
    {
        Livewire::test(Index::class)
            ->call('alternarCuenta', 3)
            ->assertSee('Cobro en transferencia')
            ->call('alternarCuenta', 3)
            ->assertDontSee('Cobro en transferencia');
    }

    public function test_cliente_con_saldo_ofrece_registrar_cobro(): void
    {
        Livewire::test(Index::class)->assertSeeHtml('/cobros/nuevo/3');
    }

    public function test_filtro_solo_con_saldo_pendiente(): void
    {
        Livewire::test(Index::class)
            ->set('soloConDeuda', true)
            ->assertSee('Almacén Don Rubén')
            ->assertDontSee('Kiosco El Sol');
    }

    public function test_busqueda_por_nombre_ignora_tildes_y_mayusculas(): void
    {
        Livewire::test(Index::class)
            ->set('buscar', 'ALMACEN don ruben')
            ->assertSee('Almacén Don Rubén')
            ->assertDontSee('Bazar Los Pinos');
    }

    public function test_busqueda_sin_resultados_muestra_estado_vacio(): void
    {
        Livewire::test(Index::class)
            ->set('buscar', 'zzzz-no-existe')
            ->assertSee('No se encontraron cuentas')
            ->call('limpiarFiltros')
            ->assertSee('Almacén Don Rubén');
    }

    public function test_vista_de_proveedores_muestra_deudas_y_compras_pendientes(): void
    {
        Livewire::test(Index::class)
            ->assertDontSee('Calzados Río Paraná S.A.')
            ->call('cambiarVista', 'proveedores')
            ->assertSee('Calzados Río Paraná S.A.')
            ->assertSee('Adeudamos')
            ->assertSee('$205.000')
            ->call('alternarCuenta', 1)
            ->assertSee('C-2031')
            ->assertSee('C-2044')
            ->assertDontSee('Almacén Don Rubén');
    }

    public function test_vista_invalida_se_ignora(): void
    {
        Livewire::test(Index::class)
            ->call('cambiarVista', 'otra-cosa')
            ->assertSet('vista', 'clientes');
    }
}
