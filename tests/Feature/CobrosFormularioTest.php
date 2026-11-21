<?php

namespace Tests\Feature;

use App\Livewire\Cobros\Formulario;
use Livewire\Livewire;
use Tests\TestCase;

class CobrosFormularioTest extends TestCase
{
    private function boletas(): array
    {
        return [
            ['id' => 101, 'numero' => 'B-0101', 'cliente_id' => 1, 'fecha' => '18/11/2026', 'monto' => 12500.0, 'saldo' => 12500.0],
            ['id' => 102, 'numero' => 'B-0102', 'cliente_id' => 1, 'fecha' => '19/11/2026', 'monto' => 6000.0, 'saldo' => 6000.0],
        ];
    }

    public function test_formulario_muestra_medios_de_pago_y_boletas(): void
    {
        Livewire::test(Formulario::class, [
            'clienteId' => '1',
            'clienteNombre' => 'María González',
            'boletas' => $this->boletas(),
        ])
            ->assertSee('Formulario de cobro (simulación)')
            ->assertSee('Efectivo ($)')
            ->assertSee('Transferencia ($)')
            ->assertSee('Cheque ($)')
            ->assertSee('B-0101')
            ->assertSee('B-0102');
    }

    public function test_pago_combinado_e_imputacion_a_multiples_boletas(): void
    {
        Livewire::test(Formulario::class, [
            'clienteId' => '1',
            'clienteNombre' => 'María González',
            'boletas' => $this->boletas(),
        ])
            ->set('montoEfectivo', 10000)
            ->set('montoTransferencia', 5000)
            ->set('montoCheque', 3500)
            ->set('numeroCheque', '00012345')
            ->set('boletasSeleccionadas', ['101', '102'])
            ->assertSee('$18.500')
            ->assertSee('$18.500')
            ->call('registrarCobro')
            ->assertSet('montoEfectivo', 0.0)
            ->assertSet('montoTransferencia', 0.0)
            ->assertSet('montoCheque', 0.0)
            ->assertSet('numeroCheque', '');
    }

    public function test_no_registra_cobro_si_el_pago_no_coincide_con_la_imputacion(): void
    {
        Livewire::test(Formulario::class, [
            'clienteId' => '1',
            'clienteNombre' => 'María González',
            'boletas' => $this->boletas(),
        ])
            ->set('montoEfectivo', 1000)
            ->set('boletasSeleccionadas', ['101'])
            ->call('registrarCobro')
            ->assertSet('montoEfectivo', 1000.0)
            ->assertSee('Diferencia a imputar');
    }

    public function test_no_registra_cobro_sin_boletas_seleccionadas(): void
    {
        Livewire::test(Formulario::class, [
            'clienteId' => '1',
            'clienteNombre' => 'María González',
            'boletas' => $this->boletas(),
        ])
            ->set('montoEfectivo', 12500)
            ->call('registrarCobro')
            ->assertSet('montoEfectivo', 12500.0);
    }
}
