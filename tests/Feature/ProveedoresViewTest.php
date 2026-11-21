<?php

namespace Tests\Feature;

use App\Livewire\Proveedores\Index;
use App\Models\Proveedor;
use Livewire\Livewire;
use Tests\TestCase;

class ProveedoresViewTest extends TestCase
{
    public function test_pantalla_proveedores_carga_con_datos_de_contacto(): void
    {
        $response = $this->get('/proveedores');

        $response->assertStatus(200);
        $response->assertSee('Proveedores & Compras', false);
        $response->assertSee('Fábrica de Calzados Misiones S.R.L.');
        $response->assertSee('Ing. Roberto Acosta');
        $response->assertSee('3765 443322');
        $response->assertSee('Buscar por razón social o teléfono...');
    }

    public function test_busqueda_y_filtro_de_proveedores(): void
    {
        Livewire::test(Index::class)
            ->set('buscar', '3765 498877')
            ->assertSee('Textiles del Sur S.A.')
            ->assertDontSee('Fábrica de Calzados Misiones S.R.L.');

        Livewire::test(Index::class)
            ->set('filtroRubro', 'Logística')
            ->assertSee('Transportes Guaraní')
            ->assertDontSee('Textiles del Sur S.A.');
    }

    public function test_alta_de_proveedor_persiste_y_cuit_es_opcional(): void
    {
        Livewire::test(Index::class)
            ->call('abrirFormulario')
            ->set('formRazonSocial', 'Proveedor Prueba S.R.L.')
            ->set('formContacto', 'Juan Prueba')
            ->set('formTelefono', '3765 999888')
            ->call('guardar')
            ->assertHasNoErrors()
            ->assertSet('mostrarFormulario', false)
            ->assertSee('Proveedor Prueba S.R.L.');

        $this->assertDatabaseHas('proveedores', [
            'razon_social' => 'Proveedor Prueba S.R.L.',
            'contacto' => 'Juan Prueba',
            'telefono' => '3765 999888',
            'cuit' => null,
        ]);
    }

    public function test_cuit_de_proveedor_valida_formato_y_unicidad(): void
    {
        Livewire::test(Index::class)
            ->call('abrirFormulario')
            ->set('formRazonSocial', 'Proveedor CUIT inválido')
            ->set('formCuit', '30-12-3')
            ->call('guardar')
            ->assertHasErrors('formCuit')
            ->assertSee('El CUIT debe tener el formato 30-12345678-9.');

        Livewire::test(Index::class)
            ->call('abrirFormulario')
            ->set('formRazonSocial', 'Proveedor CUIT duplicado')
            ->set('formCuit', '30-71223344-5')
            ->call('guardar')
            ->assertHasErrors('formCuit')
            ->assertSee('Ya existe un proveedor con ese CUIT.');

        $this->assertDatabaseMissing('proveedores', ['razon_social' => 'Proveedor CUIT inválido']);
        $this->assertDatabaseMissing('proveedores', ['razon_social' => 'Proveedor CUIT duplicado']);
    }

    public function test_edicion_y_baja_de_proveedor(): void
    {
        $proveedor = Proveedor::where('razon_social', 'Insumos Capioví')->firstOrFail();

        Livewire::test(Index::class)
            ->call('abrirFormulario', $proveedor->id)
            ->assertSet('formRazonSocial', 'Insumos Capioví')
            ->set('formContacto', 'Nuevo contacto')
            ->call('guardar')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('proveedores', [
            'id' => $proveedor->id,
            'contacto' => 'Nuevo contacto',
        ]);
        $this->assertDatabaseCount('proveedores', 4);

        Livewire::test(Index::class)
            ->call('eliminar', $proveedor->id);

        $this->assertDatabaseMissing('proveedores', ['id' => $proveedor->id]);
        $this->assertDatabaseCount('proveedores', 3);
    }
}
