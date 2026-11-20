<?php

namespace Tests\Feature;

use App\Livewire\Cobros\Formulario;
use Livewire\Livewire;
use Tests\TestCase;

class CobrosFormularioTest extends TestCase
{
    public function test_pantallas_de_cobro_cargan(): void
    {
        $this->get('/cobros/nuevo')->assertStatus(200)->assertSee('Registrar Cobro');
        $this->get('/cobros/nuevo/3')->assertStatus(200)->assertSee('B-0012');
    }

    public function test_entra_con_el_cliente_elegido_desde_la_url(): void
    {
        Livewire::test(Formulario::class, ['cliente' => '3'])->assertSet('clienteId', '3');
    }

    public function test_ignora_clientes_sin_deuda_o_invalidos_en_la_url(): void
    {
        Livewire::test(Formulario::class, ['cliente' => '4'])->assertSet('clienteId', '');
        Livewire::test(Formulario::class, ['cliente' => 'abc'])->assertSet('clienteId', '');
    }

    public function test_solo_se_ofrecen_clientes_con_saldo_pendiente(): void
    {
        Livewire::test(Formulario::class)
            ->assertSee('Bazar Los Pinos')
            ->assertDontSee('Kiosco El Sol');
    }

    public function test_al_elegir_cliente_se_listan_sus_boletas_pendientes(): void
    {
        Livewire::test(Formulario::class)
            ->set('clienteId', '3')
            ->assertSee('B-0012')
            ->assertSee('B-0019');
    }

    public function test_cobro_combinado_se_imputa_a_varias_boletas_de_la_mas_antigua_a_la_nueva(): void
    {
        Livewire::test(Formulario::class)
            ->set('clienteId', '3')
            ->set('boletasSeleccionadas', ['B-0012', 'B-0019'])
            ->set('efectivo', '20000')
            ->set('transferencia', '40000')
            ->assertSee('$60.000')          // total cobrado
            ->assertSee('queda $0')         // B-0012 (saldo 55.000) queda saldada
            ->assertSee('queda $37.000');   // B-0019 (saldo 42.000) recibe los 5.000 restantes
    }

    public function test_el_cheque_se_suma_al_total_y_se_detalla_el_medio(): void
    {
        Livewire::test(Formulario::class)
            ->set('clienteId', '7')
            ->set('efectivo', '5000')
            ->set('cheque', '10000')
            ->assertSee('$15.000')
            ->assertSee('Cheque');
    }

    public function test_avisa_cuando_el_cobro_supera_el_saldo_de_las_boletas_elegidas(): void
    {
        Livewire::test(Formulario::class)
            ->set('clienteId', '7')
            ->set('boletasSeleccionadas', ['B-0018'])
            ->set('efectivo', '30000')
            ->assertSee('Sobran $3.000');
    }

    public function test_seleccionar_todas_y_ninguna(): void
    {
        Livewire::test(Formulario::class)
            ->set('clienteId', '3')
            ->call('seleccionarTodas')
            ->assertCount('boletasSeleccionadas', 2)
            ->call('limpiarSeleccion')
            ->assertCount('boletasSeleccionadas', 0);
    }

    public function test_cambiar_de_cliente_limpia_la_seleccion_y_los_importes(): void
    {
        Livewire::test(Formulario::class)
            ->set('clienteId', '3')
            ->set('boletasSeleccionadas', ['B-0012'])
            ->set('efectivo', '1000')
            ->set('clienteId', '7')
            ->assertSet('boletasSeleccionadas', [])
            ->assertSet('efectivo', '');
    }

    public function test_confirmar_sin_cliente_falla(): void
    {
        Livewire::test(Formulario::class)
            ->call('confirmarCobroSimulado')
            ->assertHasErrors(['clienteId']);
    }

    public function test_confirmar_sin_importes_falla(): void
    {
        Livewire::test(Formulario::class)
            ->set('clienteId', '7')
            ->set('boletasSeleccionadas', ['B-0018'])
            ->call('confirmarCobroSimulado')
            ->assertHasErrors(['importes']);
    }

    public function test_confirmar_sin_boletas_elegidas_falla(): void
    {
        Livewire::test(Formulario::class)
            ->set('clienteId', '7')
            ->set('efectivo', '1000')
            ->call('confirmarCobroSimulado')
            ->assertHasErrors(['boletasSeleccionadas']);
    }

    public function test_importe_negativo_se_rechaza(): void
    {
        Livewire::test(Formulario::class)
            ->set('clienteId', '7')
            ->set('boletasSeleccionadas', ['B-0018'])
            ->set('efectivo', '-5')
            ->call('confirmarCobroSimulado')
            ->assertHasErrors(['efectivo' => 'min']);
    }

    public function test_cobro_simulado_confirmado_reinicia_el_formulario(): void
    {
        Livewire::test(Formulario::class)
            ->set('clienteId', '7')
            ->set('boletasSeleccionadas', ['B-0018'])
            ->set('efectivo', '27000')
            ->call('confirmarCobroSimulado')
            ->assertHasNoErrors()
            ->assertSee('Cobro simulado de $27.000')
            ->assertSet('boletasSeleccionadas', [])
            ->assertSet('efectivo', '');
    }
}
