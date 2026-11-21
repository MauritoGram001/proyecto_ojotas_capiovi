<?php

namespace Tests\Feature;

use App\Livewire\Clientes\Index;
use App\Models\Cliente;
use Livewire\Livewire;
use Tests\TestCase;

class ClientesViewTest extends TestCase
{
    public function test_pantalla_clientes_carga_correctamente(): void
    {
        $response = $this->get('/clientes');

        $response->assertStatus(200);
        $response->assertSee('Clientes', false);
        $response->assertSee('María González');
        $response->assertSee('Distribuidora Norte S.R.L.');
        $response->assertSee('Buscar por nombre o teléfono...');
    }

    public function test_busqueda_por_nombre_o_telefono(): void
    {
        Livewire::test(Index::class)
            ->set('buscar', '3765 501234')
            ->assertSee('Distribuidora Norte S.R.L.')
            ->assertDontSee('María González');

        Livewire::test(Index::class)
            ->set('buscar', 'Carlos')
            ->assertSee('Carlos Ruiz')
            ->assertDontSee('Distribuidora Norte S.R.L.');
    }

    public function test_campo_cuit_se_activa_solo_para_mayoristas(): void
    {
        Livewire::test(Index::class)
            ->call('abrirFormulario')
            ->assertDontSee('Ej: 30-12345678-9')
            ->set('formTipo', 'Mayorista')
            ->assertSee('CUIT * (obligatorio para mayoristas)')
            ->assertSee('Ej: 30-12345678-9')
            ->set('formTipo', 'Minorista')
            ->assertDontSee('Ej: 30-12345678-9');
    }

    public function test_alta_de_cliente_minorista_no_exige_cuit_y_persiste(): void
    {
        Livewire::test(Index::class)
            ->call('abrirFormulario')
            ->set('formNombre', 'Cliente Prueba')
            ->set('formTipo', 'Minorista')
            ->set('formTelefono', '3765 111222')
            ->call('guardar')
            ->assertHasNoErrors()
            ->assertSet('mostrarFormulario', false)
            ->assertSee('Cliente Prueba');

        $this->assertDatabaseHas('clientes', [
            'nombre' => 'Cliente Prueba',
            'tipo' => Cliente::TIPO_MINORISTA,
            'telefono' => '3765 111222',
            'cuit' => null,
        ]);
    }

    public function test_alta_de_mayorista_exige_cuit(): void
    {
        Livewire::test(Index::class)
            ->call('abrirFormulario')
            ->set('formNombre', 'Mayorista Prueba')
            ->set('formTipo', Cliente::TIPO_MAYORISTA)
            ->call('guardar')
            ->assertHasErrors('formCuit')
            ->assertSet('mostrarFormulario', true)
            ->assertSee('El CUIT es obligatorio para clientes mayoristas.');

        $this->assertDatabaseMissing('clientes', ['nombre' => 'Mayorista Prueba']);
    }

    public function test_cuit_de_mayorista_valida_formato_y_unicidad(): void
    {
        Livewire::test(Index::class)
            ->call('abrirFormulario')
            ->set('formNombre', 'Mayorista con CUIT inválido')
            ->set('formTipo', Cliente::TIPO_MAYORISTA)
            ->set('formCuit', '30-123-4')
            ->call('guardar')
            ->assertHasErrors('formCuit')
            ->assertSee('El CUIT debe tener el formato 30-12345678-9.');

        Livewire::test(Index::class)
            ->call('abrirFormulario')
            ->set('formNombre', 'Mayorista con CUIT duplicado')
            ->set('formTipo', Cliente::TIPO_MAYORISTA)
            ->set('formCuit', '30-71234567-8')
            ->call('guardar')
            ->assertHasErrors('formCuit')
            ->assertSee('Ya existe un cliente con ese CUIT.');

        $this->assertDatabaseMissing('clientes', ['nombre' => 'Mayorista con CUIT inválido']);
        $this->assertDatabaseMissing('clientes', ['nombre' => 'Mayorista con CUIT duplicado']);
    }

    public function test_edicion_y_baja_de_cliente(): void
    {
        $cliente = Cliente::where('nombre', 'Carlos Ruiz')->firstOrFail();

        Livewire::test(Index::class)
            ->call('abrirFormulario', $cliente->id)
            ->assertSet('formNombre', 'Carlos Ruiz')
            ->set('formTelefono', '3765 000111')
            ->call('guardar')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('clientes', [
            'id' => $cliente->id,
            'telefono' => '3765 000111',
        ]);
        $this->assertDatabaseCount('clientes', 5);

        Livewire::test(Index::class)
            ->call('eliminar', $cliente->id);

        $this->assertDatabaseMissing('clientes', ['id' => $cliente->id]);
        $this->assertDatabaseCount('clientes', 4);
    }

    public function test_venta_rapida_sin_identificar_comprador(): void
    {
        Livewire::test(Index::class)
            ->set('compradorRapido', 'final')
            ->call('confirmarCompradorRapido')
            ->assertSet('compradorConfirmado', 'Consumidor Final (sin identificar)');

        $this->assertDatabaseCount('clientes', 5);
    }

    public function test_venta_rapida_con_cliente_existente(): void
    {
        $cliente = Cliente::where('nombre', 'Distribuidora Norte S.R.L.')->firstOrFail();

        Livewire::test(Index::class)
            ->set('compradorRapido', (string) $cliente->id)
            ->call('confirmarCompradorRapido')
            ->assertSet('compradorConfirmado', 'Distribuidora Norte S.R.L.');
    }
}
