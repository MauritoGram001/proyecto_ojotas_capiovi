<?php

namespace Tests\Feature;

use App\Livewire\Clientes\Index as ClientesIndex;
use App\Livewire\Ventas\Crear;
use Livewire\Livewire;
use Tests\TestCase;

class VentasCrearViewTest extends TestCase
{
    /** Agrega una variante (por id) a la venta con la cantidad indicada. */
    private function conLinea($componente, string $varianteId, int $cantidad = 1)
    {
        return $componente
            ->set('varianteSeleccionada', $varianteId)
            ->set('cantidadNueva', $cantidad)
            ->call('agregarLinea');
    }

    public function test_pantalla_de_venta_carga_correctamente(): void
    {
        $response = $this->get('/ventas/crear');

        $response->assertStatus(200);
        $response->assertSee('Nueva Venta');
        $response->assertSee('Consumidor final');
        $response->assertSee('Todavía no hay productos');
    }

    public function test_agregar_linea_calcula_subtotal_y_total(): void
    {
        $this->conLinea(Livewire::test(Crear::class), '101', 2)
            ->assertHasNoErrors()
            ->assertCount('lineas', 1)
            ->assertSee('$13.000');
    }

    public function test_no_se_puede_agregar_sin_elegir_producto(): void
    {
        Livewire::test(Crear::class)
            ->call('agregarLinea')
            ->assertHasErrors(['varianteSeleccionada' => 'required'])
            ->assertCount('lineas', 0);
    }

    public function test_la_cantidad_minima_es_uno(): void
    {
        $this->conLinea(Livewire::test(Crear::class), '101', 0)
            ->assertHasErrors(['cantidadNueva' => 'min'])
            ->assertCount('lineas', 0);
    }

    public function test_agregar_la_misma_variante_suma_la_cantidad(): void
    {
        $componente = Livewire::test(Crear::class);
        $this->conLinea($componente, '101', 2);
        $this->conLinea($componente, '101', 1);

        $componente->assertCount('lineas', 1)->assertSet('lineas.0.cantidad', 3);
    }

    public function test_cliente_mayorista_sugiere_lista_mayorista(): void
    {
        $componente = Livewire::test(Crear::class)
            ->assertSet('clienteId', ClientesIndex::CONSUMIDOR_FINAL)
            ->assertSet('lista', 'Minorista')
            ->set('clienteId', '3')
            ->assertSet('lista', 'Mayorista');

        $this->conLinea($componente, '101')->assertSee('$4.500');

        $componente->set('clienteId', ClientesIndex::CONSUMIDOR_FINAL)->assertSet('lista', 'Minorista');
    }

    public function test_la_lista_sugerida_se_puede_cambiar_a_mano(): void
    {
        $componente = Livewire::test(Crear::class)
            ->set('clienteId', '3')
            ->set('lista', 'Minorista')
            ->assertSet('lista', 'Minorista');

        $this->conLinea($componente, '101')->assertSee('$6.500');
    }

    public function test_cambio_manual_de_precio_muestra_advertencia_y_se_puede_restablecer(): void
    {
        $componente = Livewire::test(Crear::class);
        $this->conLinea($componente, '101');

        $componente
            ->call('alternarEdicionPrecio', 0)
            ->assertSee('solo a esta venta')
            ->set('lineas.0.precio_manual', 5000)
            ->assertSee('Precio cambiado a mano')
            ->assertSee('$5.000')
            ->call('restablecerPrecio', 0)
            ->assertDontSee('Precio cambiado a mano')
            ->assertSee('$6.500');
    }

    public function test_descuento_en_porcentaje_actualiza_el_total(): void
    {
        $componente = Livewire::test(Crear::class);
        $this->conLinea($componente, '101', 2); // 2 x 6.500 = 13.000

        $componente
            ->set('descuentoPct', '10')
            ->assertSee('$1.300')   // descuento aplicado
            ->assertSee('$11.700'); // total
    }

    public function test_el_descuento_no_puede_superar_el_cien_por_ciento(): void
    {
        $componente = Livewire::test(Crear::class);
        $this->conLinea($componente, '101'); // $6.500

        // Se limita a 100 %: el descuento es todo el subtotal y el total queda en $0.
        $componente->set('descuentoPct', '150')->assertSee('$0');
    }

    public function test_cantidad_con_botones_no_baja_de_uno(): void
    {
        $componente = Livewire::test(Crear::class);
        $this->conLinea($componente, '101');

        $componente
            ->call('cambiarCantidad', 0, -1)
            ->assertSet('lineas.0.cantidad', 1)
            ->call('cambiarCantidad', 0, 1)
            ->assertSet('lineas.0.cantidad', 2);
    }

    public function test_quitar_linea(): void
    {
        $componente = Livewire::test(Crear::class);
        $this->conLinea($componente, '101');

        $componente->call('quitarLinea', 0)->assertCount('lineas', 0);
    }

    public function test_confirmar_sin_productos_avisa(): void
    {
        Livewire::test(Crear::class)
            ->call('confirmarVentaSimulada')
            ->assertSee('Agregá al menos un producto');
    }

    public function test_confirmar_venta_simulada_reinicia_la_pantalla(): void
    {
        $componente = Livewire::test(Crear::class)->set('clienteId', '3');
        $this->conLinea($componente, '101', 2);

        $componente
            ->call('confirmarVentaSimulada')
            ->assertSee('simulada')
            ->assertCount('lineas', 0)
            ->assertSet('numeroVenta', 2)
            ->assertSet('clienteId', ClientesIndex::CONSUMIDOR_FINAL)
            ->assertSet('lista', 'Minorista');
    }

    public function test_descargar_pdf_simulado_deja_el_comprobante_en_sesion(): void
    {
        $componente = Livewire::test(Crear::class);
        $this->conLinea($componente, '101', 2);

        $componente->call('descargarPdfSimulado')->assertSee('Simulación');

        $comprobante = session('comprobante_simulado');
        $this->assertNotNull($comprobante);
        $this->assertSame(13000.0, $comprobante['subtotal']);
        $this->assertSame('Consumidor final', $comprobante['cliente']);
    }

    public function test_descargar_pdf_sin_productos_avisa(): void
    {
        Livewire::test(Crear::class)
            ->call('descargarPdfSimulado')
            ->assertSee('Agregá productos para previsualizar');
    }

    public function test_plantilla_del_comprobante_muestra_ejemplo_no_fiscal(): void
    {
        $response = $this->get('/ventas/comprobante-simulado');

        $response->assertStatus(200);
        $response->assertSee('NO VÁLIDO COMO FACTURA');
        $response->assertSee('Ojotas Clásicas Capioví');
        $response->assertSee('Precio cobrado');
    }

    public function test_plantilla_del_comprobante_usa_la_venta_guardada_en_sesion(): void
    {
        $comprobante = [
            'numero' => '0001-00000007 (simulado)',
            'fecha' => '19/11/2026 22:00',
            'cliente' => 'Cliente De Prueba (Mayorista)',
            'lista' => 'Mayorista',
            'lineas' => [[
                'modelo' => 'Bermuda Playera Secado Rápido',
                'talle' => 'M',
                'color' => 'Azul Marino',
                'cantidad' => 3,
                'precio_unitario' => 10000.0,
                'precio_manual' => true,
                'subtotal' => 30000.0,
            ]],
            'subtotal' => 30000.0,
            'descuento_pct' => 0.0,
            'descuento_monto' => 0.0,
            'total' => 30000.0,
            'hay_precio_manual' => true,
        ];

        $this->withSession(['comprobante_simulado' => $comprobante])
            ->get('/ventas/comprobante-simulado')
            ->assertStatus(200)
            ->assertSee('Cliente De Prueba (Mayorista)')
            ->assertSee('Bermuda Playera Secado Rápido')
            ->assertSee('$30.000')
            ->assertSee('Precio cambiado a mano para esta venta');
    }

    public function test_cliente_con_deuda_muestra_aviso_informativo(): void
    {
        Livewire::test(Crear::class)
            ->set('clienteId', '3')
            ->assertSee('tiene 2 boleta(s) pendiente(s) por $97.000')
            ->assertSee('podés continuar con la venta');
    }

    public function test_sin_deuda_no_se_muestra_aviso(): void
    {
        Livewire::test(Crear::class)
            ->assertDontSee('boleta(s) pendiente(s)')       // consumidor final
            ->set('clienteId', '4')
            ->assertDontSee('boleta(s) pendiente(s)')       // cliente al día
            ->set('clienteId', '2')
            ->assertDontSee('boleta(s) pendiente(s)');      // cliente sin cuenta
    }

    public function test_la_deuda_previa_no_bloquea_la_venta(): void
    {
        $componente = Livewire::test(Crear::class)->set('clienteId', '3');
        $this->conLinea($componente, '101', 1);

        $componente
            ->call('confirmarVentaSimulada')
            ->assertSee('simulada')
            ->assertHasNoErrors();
    }
}
