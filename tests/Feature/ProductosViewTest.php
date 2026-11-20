<?php

namespace Tests\Feature;

use App\Livewire\Productos\Index;
use Livewire\Livewire;
use Tests\TestCase;

class ProductosViewTest extends TestCase
{
    public function test_pantalla_productos_carga_correctamente(): void
    {
        $response = $this->get('/productos');

        $response->assertStatus(200);
        $response->assertSee('Productos & Existencias', false);
        $response->assertSee('Ojotas Clásicas Capioví');
        $response->assertSee('Bajo stock (&lt; 10)!', false);
    }

    public function test_componente_livewire_muestra_variantes_y_precios(): void
    {
        Livewire::test(Index::class)
            ->assertSee('Ojotas Clásicas Capioví')
            ->assertSee('$6.500') // Minorista
            ->assertSee('$4.500') // Mayorista
            ->call('verDetalle', 1)
            ->assertSet('productoDetalle.id', 1)
            ->assertSee('37/38')
            ->assertSee('39/40')
            ->assertSee('41/42');
    }

    public function test_filtro_bajo_stock_funciona(): void
    {
        Livewire::test(Index::class)
            ->set('filtroStock', 'bajo')
            ->assertSee('Ojotas Playeras Estampadas')
            ->assertDontSee('Sandalias Verano Confort'); // Tiene 45 unidades sin bajo stock
    }

    public function test_alta_simulada_de_producto(): void
    {
        Livewire::test(Index::class)
            ->set('formNombre', 'Ojota Urbana Test')
            ->set('formCategoria', 'Calzado')
            ->set('formPrecioMinorista', 8000)
            ->set('formPrecioMayorista', 5500)
            ->set('formStock', 20)
            ->set('formTalle', '41/42')
            ->set('formColor', 'Gris')
            ->call('guardarSimulado')
            ->assertSee('Ojota Urbana Test');
    }
}
