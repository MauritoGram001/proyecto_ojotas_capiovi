<?php

namespace Tests\Feature;

use App\Livewire\Informes\Index;
use Livewire\Livewire;
use Tests\TestCase;

class InformesViewTest extends TestCase
{
    public function test_pantalla_de_informes_carga_con_totales_consolidados(): void
    {
        $response = $this->get('/informes');

        $response->assertStatus(200);
        $response->assertSee('Informes e Historial');
        $response->assertSee('Ventas por período');
        $response->assertSee('Total del período');
        $response->assertSee('$135.500');
        $response->assertSee('INT-0001');
    }

    public function test_filtro_por_periodo_presets(): void
    {
        Livewire::test(Index::class)
            ->assertSee('$135.500')
            ->call('aplicarPreset', 'hoy')
            ->assertSee('$50.300')
            ->assertDontSee('INT-0001')
            ->call('aplicarPreset', 'mes')
            ->assertSee('$135.500');
    }

    public function test_filtro_manual_por_rango_de_fechas(): void
    {
        Livewire::test(Index::class)
            ->set('desde', '2026-11-19')
            ->set('hasta', '2026-11-19')
            ->assertSee('$67.000')
            ->assertDontSee('INT-0001')
            ->assertDontSee('INT-0005');
    }

    public function test_resumen_diario_y_mensual_con_subtotales(): void
    {
        Livewire::test(Index::class)
            ->set('vista', 'diario')
            ->assertSee('Resumen diario')
            ->assertSee('$18.200')
            ->assertSee('$67.000')
            ->assertSee('$50.300')
            ->set('vista', 'mensual')
            ->assertSee('Resumen mensual')
            ->assertSee('$135.500');
    }

    public function test_historial_por_cliente_identificado(): void
    {
        Livewire::test(Index::class)
            ->set('tab', 'cliente')
            ->assertSee('Historial por Cliente')
            ->assertSee('Compras de María González')
            ->assertSee('INT-0001')
            ->assertSee('INT-0006')
            ->assertSee('$20.900')
            ->assertDontSee('INT-0003')
            ->set('clienteSeleccionado', 'Carlos Ruiz')
            ->assertSee('INT-0003')
            ->assertSee('$21.400')
            ->assertDontSee('INT-0001');
    }

    public function test_historial_excluye_compradores_no_identificados(): void
    {
        Livewire::test(Index::class)
            ->set('tab', 'cliente')
            ->assertSee('No incluye compradores no identificados')
            ->assertDontSee('Consumidor Final (sin identificar)');
    }
}
