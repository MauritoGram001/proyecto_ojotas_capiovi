<?php

namespace Tests\Feature;

use App\Livewire\Cobros\Formulario;
use App\Models\Boleta;
use App\Models\Cliente;
use App\Models\Cobro;
use App\Models\CuentaCorriente;
use Livewire\Livewire;
use Tests\TestCase;

class CobrosFormularioTest extends TestCase
{
    public function test_formulario_muestra_medios_de_pago_y_boletas_pendientes(): void
    {
        $cliente = Cliente::query()->where('nombre', 'María González')->firstOrFail();

        Livewire::test(Formulario::class, ['clienteId' => $cliente->id])
            ->assertSee('Registrar cobro')
            ->assertSee('Efectivo ($)')
            ->assertSee('Transferencia ($)')
            ->assertSee('Cheque ($)')
            ->assertSee('B-0101')
            ->assertSee('B-0102');
    }

    public function test_pago_combinado_e_imputacion_a_multiples_boletas_actualiza_saldos(): void
    {
        $cliente = Cliente::query()->where('nombre', 'María González')->firstOrFail();
        $boleta1 = Boleta::query()->where('numero', 'B-0101')->firstOrFail();
        $boleta2 = Boleta::query()->where('numero', 'B-0102')->firstOrFail();

        Livewire::test(Formulario::class, ['clienteId' => $cliente->id])
            ->set('montoEfectivo', 10000)
            ->set('montoTransferencia', 5000)
            ->set('montoCheque', 3500)
            ->set('numeroCheque', '00012345')
            ->set('boletasSeleccionadas', [(string) $boleta1->id, (string) $boleta2->id])
            ->set('montosImputados.'. $boleta1->id, 12500)
            ->set('montosImputados.'. $boleta2->id, 6000)
            ->call('registrarCobro')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('cobros', [
            'cliente_id' => $cliente->id,
            'monto_efectivo' => 10000.00,
            'monto_transferencia' => 5000.00,
            'monto_cheque' => 3500.00,
            'numero_cheque' => '00012345',
            'total' => 18500.00,
        ]);

        $cobro = Cobro::query()->where('cliente_id', $cliente->id)->latest('id')->firstOrFail();

        $this->assertDatabaseHas('cobro_imputaciones', [
            'cobro_id' => $cobro->id,
            'boleta_id' => $boleta1->id,
            'monto' => 12500.00,
        ]);
        $this->assertDatabaseHas('cobro_imputaciones', [
            'cobro_id' => $cobro->id,
            'boleta_id' => $boleta2->id,
            'monto' => 6000.00,
        ]);

        $this->assertDatabaseHas('boletas', ['id' => $boleta1->id, 'saldo' => 0.00, 'estado' => 'Pagada']);
        $this->assertDatabaseHas('boletas', ['id' => $boleta2->id, 'saldo' => 0.00, 'estado' => 'Pagada']);

        $cuenta = CuentaCorriente::query()->where('cliente_id', $cliente->id)->firstOrFail();
        $this->assertSame(0.0, (float) $cuenta->saldo);
    }

    public function test_cobro_parcial_actualiza_saldo_remanente(): void
    {
        $cliente = Cliente::query()->where('nombre', 'María González')->firstOrFail();
        $boleta1 = Boleta::query()->where('numero', 'B-0101')->firstOrFail();

        Livewire::test(Formulario::class, ['clienteId' => $cliente->id])
            ->set('montoEfectivo', 5000)
            ->set('boletasSeleccionadas', [(string) $boleta1->id])
            ->set('montosImputados.'. $boleta1->id, 5000)
            ->call('registrarCobro')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('boletas', ['id' => $boleta1->id, 'saldo' => 7500.00, 'estado' => 'Parcial']);
        $cuenta = CuentaCorriente::query()->where('cliente_id', $cliente->id)->firstOrFail();
        $this->assertSame(13500.0, (float) $cuenta->saldo);
    }

    public function test_no_registra_cobro_si_imputacion_no_coincide_con_total(): void
    {
        $cliente = Cliente::query()->where('nombre', 'María González')->firstOrFail();
        $boleta1 = Boleta::query()->where('numero', 'B-0101')->firstOrFail();
        $cobrosAntes = Cobro::query()->count();

        Livewire::test(Formulario::class, ['clienteId' => $cliente->id])
            ->set('montoEfectivo', 1000)
            ->set('boletasSeleccionadas', [(string) $boleta1->id])
            ->set('montosImputados.'. $boleta1->id, 2000)
            ->call('registrarCobro')
            ->assertHasErrors(['boletas']);

        $this->assertSame($cobrosAntes, Cobro::query()->count());
        $this->assertDatabaseHas('boletas', ['id' => $boleta1->id, 'saldo' => 12500.00, 'estado' => 'Pendiente']);
    }

    public function test_no_registra_cobro_sin_boletas_seleccionadas(): void
    {
        $cliente = Cliente::query()->where('nombre', 'María González')->firstOrFail();
        $cobrosAntes = Cobro::query()->count();

        Livewire::test(Formulario::class, ['clienteId' => $cliente->id])
            ->set('montoEfectivo', 12500)
            ->set('boletasSeleccionadas', [])
            ->call('registrarCobro')
            ->assertHasErrors(['boletas']);

        $this->assertSame($cobrosAntes, Cobro::query()->count());
    }
}
