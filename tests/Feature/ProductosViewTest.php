<?php

namespace Tests\Feature;

use App\Livewire\Productos\Index;
use App\Models\Producto;
use App\Models\Variante;
use InvalidArgumentException;
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
            ->assertDontSee('Sandalias Verano Confort'); // Tiene variantes sin bajo stock y total suficiente
    }

    public function test_alta_de_producto_persiste_con_variante(): void
    {
        Livewire::test(Index::class)
            ->call('abrirFormulario')
            ->set('formNombre', 'Ojota Urbana Test')
            ->set('formCategoria', 'Calzado')
            ->set('formPrecioMinorista', 8000)
            ->set('formPrecioMayorista', 5500)
            ->set('formStock', 20)
            ->set('formTalle', '41/42')
            ->set('formColor', 'Gris')
            ->call('guardar')
            ->assertHasNoErrors()
            ->assertSet('mostrarFormulario', false)
            ->assertSee('Ojota Urbana Test');

        $this->assertDatabaseHas('productos', [
            'modelo' => 'Ojota Urbana Test',
            'categoria' => 'Calzado',
            'precio_minorista' => 8000.00,
            'precio_mayorista' => 5500.00,
        ]);

        $producto = Producto::where('modelo', 'Ojota Urbana Test')->firstOrFail();
        $this->assertDatabaseHas('variantes', [
            'producto_id' => $producto->id,
            'talle' => '41/42',
            'color' => 'Gris',
            'stock' => 20,
        ]);
    }

    public function test_consulta_stock_por_modelo_talle_y_color(): void
    {
        $porModelo = Producto::consultarStock('Clásicas');
        $this->assertCount(3, $porModelo);
        $this->assertTrue($porModelo->every(fn (Variante $v) => str_contains($v->producto->modelo, 'Clásicas')));

        $porTalleYColor = Producto::consultarStock(null, '39/40', 'Azul');
        $this->assertCount(1, $porTalleYColor);
        $this->assertSame(102, $porTalleYColor->first()->id);
    }

    public function test_alerta_de_bajo_stock_por_variante(): void
    {
        $bajo = Variante::findOrFail(101);
        $limite = Variante::findOrFail(203);

        $this->assertTrue($bajo->esBajoStock(10));
        $this->assertFalse($limite->esBajoStock(10));
    }

    public function test_actualizacion_atomica_de_stock_evita_descontar_de_mas(): void
    {
        $variante = Variante::findOrFail(101); // stock 5
        $this->assertSame(5, $variante->stock);

        Variante::ajustarStockAtomico(101, -2);
        $this->assertSame(3, Variante::findOrFail(101)->stock);

        Producto::registrarIngreso([101 => 4]);
        $this->assertSame(7, Variante::findOrFail(101)->stock);

        try {
            Variante::ajustarStockAtomico(101, -8);
            $this->fail('Se esperaba una excepción por stock insuficiente.');
        } catch (InvalidArgumentException $e) {
            $this->assertStringContainsString('No hay stock suficiente', $e->getMessage());
        }

        // El stock no debe cambiar ante un intento inválido (transacción atómica).
        $this->assertSame(7, Variante::findOrFail(101)->fresh()->stock);
    }

    public function test_descuento_atomico_por_venta_no_aplica_parcialmente_si_falla(): void
    {
        try {
            Variante::descontarPorVenta([
                101 => 2, // ok (5 -> 3)
                201 => 99, // falla por insuficiente
            ]);
            $this->fail('Se esperaba una excepción por stock insuficiente en la venta.');
        } catch (InvalidArgumentException $e) {
            $this->assertStringContainsString('No hay stock suficiente', $e->getMessage());
        }

        $this->assertSame(5, Variante::findOrFail(101)->fresh()->stock);
        $this->assertSame(15, Variante::findOrFail(201)->fresh()->stock);
    }
}
