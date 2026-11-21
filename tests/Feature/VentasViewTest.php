<?php

namespace Tests\Feature;

use App\Livewire\Ventas\Crear;
use App\Models\Boleta;
use App\Models\CuentaCorriente;
use App\Models\Producto;
use App\Models\Variante;
use App\Models\Venta;
use App\Models\VentaItem;
use Livewire\Livewire;
use Tests\TestCase;

class VentasViewTest extends TestCase
{
    public function test_pantalla_de_venta_carga_correctamente(): void
    {
        $response = $this->get('/ventas/crear');

        $response->assertStatus(200);
        $response->assertSee('Nueva Venta (POS)', false);
        $response->assertSee('Consumidor Final (sin identificar)');
        $response->assertSee('Ojotas Clásicas Capioví');
        $response->assertSee('Ver Comprobante');
        $response->assertSee('Registrar venta');
    }

    public function test_agregar_variante_usa_precio_minorista(): void
    {
        Livewire::test(Crear::class)
            ->set('productoSeleccionado', '101')
            ->call('agregarLinea')
            ->assertSet('lineas.0.modelo', 'Ojotas Clásicas Capioví')
            ->assertSet('lineas.0.talle', '37/38')
            ->assertSet('lineas.0.precio_unitario', 6500.0)
            ->assertSee('$6.500');
    }

    public function test_cambio_de_modo_actualiza_precios_de_lineas_no_modificadas(): void
    {
        Livewire::test(Crear::class)
            ->set('productoSeleccionado', '101')
            ->call('agregarLinea')
            ->assertSet('lineas.0.precio_unitario', 6500.0)
            ->set('modoPrecio', 'mayorista')
            ->assertSet('lineas.0.precio_unitario', 4500.0)
            ->assertSee('$4.500');
    }

    public function test_seleccionar_cliente_mayorista_activa_modo_mayorista(): void
    {
        Livewire::test(Crear::class)
            ->set('compradorId', '3')
            ->assertSet('nombreComprador', 'Distribuidora Norte S.R.L.')
            ->assertSet('modoPrecio', 'mayorista')
            ->set('compradorId', 'final')
            ->assertSet('modoPrecio', 'minorista');
    }

    public function test_cambio_manual_de_precio_muestra_advertencia(): void
    {
        Livewire::test(Crear::class)
            ->set('productoSeleccionado', '101')
            ->call('agregarLinea')
            ->assertDontSee('Precio modificado manualmente.')
            ->set('lineas.0.precio_unitario', 5000)
            ->assertSee('Precio modificado manualmente.')
            ->assertSee('afecta solo a la operación en curso');
    }

    public function test_totales_dinamicos_con_cantidad_y_descuento(): void
    {
        Livewire::test(Crear::class)
            ->set('productoSeleccionado', '101')
            ->call('agregarLinea')
            ->set('lineas.0.cantidad', 2)
            ->set('lineas.0.descuento', 10)
            ->assertSee('$13.000')
            ->assertSee('$1.300')
            ->assertSee('$11.700');
    }

    public function test_advertencia_de_boleta_pendiente_no_bloquea_la_venta(): void
    {
        // María González (id 1) tiene deuda pendiente real en cuenta corriente (seeder).
        Livewire::test(Crear::class)
            ->set('compradorId', '1')
            ->assertSet('modoPrecio', 'minorista')
            ->assertSee('deuda pendiente')
            ->assertSee('$18.500')
            ->assertSee('Podés continuar con la venta normalmente.')
            ->set('compradorId', 'final')
            ->assertDontSee('Podés continuar con la venta normalmente.');
    }

    public function test_comprobante_no_se_abrir_sin_lineas(): void
    {
        Livewire::test(Crear::class)
            ->call('verComprobante')
            ->assertSet('mostrarComprobante', false);
    }

    public function test_previsualizacion_de_comprobante_interno(): void
    {
        Livewire::test(Crear::class)
            ->set('productoSeleccionado', '201')
            ->call('agregarLinea')
            ->set('lineas.0.precio_unitario', 8700)
            ->call('verComprobante')
            ->assertSet('mostrarComprobante', true)
            ->assertSee('COMPROBANTE INTERNO')
            ->assertSee('NO FISCAL — USO INTERNO')
            ->assertSee('Sandalias Verano Confort')
            ->assertSee('Precio cobrado')
            ->assertSee('$8.700')
            ->assertSee('Descargar PDF (Simulado)');
    }

    public function test_confirmar_venta_sin_cliente_descuenta_stock_y_persiste_comprobante(): void
    {
        $stockAntes = Variante::findOrFail(101)->stock; // 5

        Livewire::test(Crear::class)
            ->set('productoSeleccionado', '101')
            ->call('agregarLinea')
            ->set('lineas.0.cantidad', 2)
            ->call('confirmarVenta')
            ->assertSet('mostrarComprobante', true)
            ->assertNotSet('ventaRegistradaId', null);

        $this->assertSame($stockAntes - 2, Variante::findOrFail(101)->fresh()->stock);

        $venta = Venta::with('items')->latest('id')->firstOrFail();
        $this->assertNull($venta->cliente_id);
        $this->assertSame('Consumidor Final (sin identificar)', $venta->nombre_comprador);
        $this->assertSame('minorista', $venta->modo_precio);
        $this->assertCount(1, $venta->items);
        $this->assertSame(101, $venta->items[0]->variante_id);
        $this->assertSame(2, $venta->items[0]->cantidad);
        $this->assertEquals(6500.00, (float) $venta->items[0]->precio_unitario);
    }

    public function test_confirmar_venta_con_cliente_identificado_persiste_cliente(): void
    {
        Livewire::test(Crear::class)
            ->set('compradorId', '3') // Distribuidora Norte S.R.L. (Mayorista)
            ->set('productoSeleccionado', '101')
            ->call('agregarLinea')
            ->call('confirmarVenta')
            ->assertNotSet('ventaRegistradaId', null);

        $venta = Venta::with('items')->latest('id')->firstOrFail();
        $this->assertSame(3, $venta->cliente_id);
        $this->assertSame('Distribuidora Norte S.R.L.', $venta->nombre_comprador);
        $this->assertSame('mayorista', $venta->modo_precio);
        $this->assertEquals(4500.00, (float) $venta->items[0]->precio_lista);
        $this->assertEquals(4500.00, (float) $venta->items[0]->precio_unitario);
    }

    public function test_precio_y_descuento_cobrados_se_persisten_sin_alterar_catalogo(): void
    {
        $precioListaAntes = (float) Producto::findOrFail(1)->precio_minorista;
        $precioMayoristaAntes = (float) Producto::findOrFail(1)->precio_mayorista;

        Livewire::test(Crear::class)
            ->set('productoSeleccionado', '101')
            ->call('agregarLinea')
            ->set('lineas.0.precio_unitario', 5000)
            ->set('lineas.0.descuento', 10)
            ->call('confirmarVenta')
            ->assertNotSet('ventaRegistradaId', null);

        $venta = Venta::with('items')->latest('id')->firstOrFail();
        $item = $venta->items->first();
        $this->assertEquals(6500.00, (float) $item->precio_lista);
        $this->assertEquals(5000.00, (float) $item->precio_unitario);
        $this->assertEquals(10.00, (float) $item->descuento_porcentaje);
        $this->assertTrue((bool) $venta->precio_modificado_manual);
        // El ítem guarda el precio efectivamente cobrado (subtotal con descuento aplicado)
        $this->assertEquals(4500.00, (float) $item->subtotal);

        $this->assertEquals($precioListaAntes, (float) Producto::findOrFail(1)->fresh()->precio_minorista);
        $this->assertEquals($precioMayoristaAntes, (float) Producto::findOrFail(1)->fresh()->precio_mayorista);
    }

    public function test_confirmar_venta_con_stock_insuficiente_no_registra_ni_descuenta(): void
    {
        $stockAntes = Variante::findOrFail(101)->stock; // 5
        $ventasAntes = Venta::count();

        Livewire::test(Crear::class)
            ->set('productoSeleccionado', '101')
            ->call('agregarLinea')
            ->set('lineas.0.cantidad', 99)
            ->call('confirmarVenta')
            ->assertSet('ventaRegistradaId', null)
            ->assertSee('No hay stock suficiente');

        $this->assertSame($stockAntes, Variante::findOrFail(101)->fresh()->stock);
        $this->assertSame($ventasAntes, Venta::count());
        $this->assertSame(0, VentaItem::count());
    }

    public function test_descarga_de_comprobante_pdf_de_venta_registrada(): void
    {
        $component = Livewire::test(Crear::class)
            ->set('productoSeleccionado', '201')
            ->call('agregarLinea')
            ->call('confirmarVenta')
            ->assertNotSet('ventaRegistradaId', null);

        $ventaId = $component->get('ventaRegistradaId');
        $venta = Venta::findOrFail($ventaId);

        $response = $this->get(route('ventas.comprobante', $venta));
        $response->assertOk();
        $this->assertStringContainsString('application/pdf', (string) $response->headers->get('content-type'));
        $this->assertStringContainsString($venta->numero, (string) $response->headers->get('content-disposition'));
    }

    public function test_venta_a_cliente_identificado_genera_boleta_pendiente_y_actualiza_saldo(): void
    {
        $clienteId = 1; // Mar�a Gonz�lez (deuda inicial 18500 por seeder)
        $saldoAntes = (float) CuentaCorriente::query()->where('cliente_id', $clienteId)->value('saldo');

        Livewire::test(Crear::class)
            ->set('compradorId', (string) $clienteId)
            ->set('productoSeleccionado', '101')
            ->call('agregarLinea')
            ->call('confirmarVenta')
            ->assertHasNoErrors();

        $venta = Venta::query()->latest('id')->first();
        $this->assertNotNull($venta);
        $this->assertSame($clienteId, $venta->cliente_id);

        $boleta = Boleta::query()->where('venta_id', $venta->id)->first();
        $this->assertNotNull($boleta);
        $this->assertSame('Pendiente', $boleta->estado);
        $this->assertEquals((float) $venta->total, (float) $boleta->monto);
        $this->assertEquals((float) $venta->total, (float) $boleta->saldo);

        $saldoDespues = (float) CuentaCorriente::query()->where('cliente_id', $clienteId)->value('saldo');
        $this->assertEquals(round($saldoAntes + (float) $venta->total, 2), round($saldoDespues, 2));
    }
}
