<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Muestra la plantilla del comprobante interno no fiscal (VIS-004).
 *
 * Fase visual: renderiza el HTML que luego se convertirá en PDF con DomPDF.
 * Usa el comprobante que dejó la pantalla de venta en sesión o, si no hay,
 * uno de ejemplo con datos ficticios.
 */
class ComprobanteSimuladoController extends Controller
{
    public function __invoke(Request $request)
    {
        $comprobante = $request->session()->get('comprobante_simulado') ?? $this->ejemplo();

        return view('pdf.comprobante-interno', ['comprobante' => $comprobante]);
    }

    private function ejemplo(): array
    {
        return [
            'numero' => '0001-00000001 (ejemplo)',
            'fecha' => now()->format('d/m/Y H:i'),
            'cliente' => 'Consumidor final',
            'lista' => 'Minorista',
            'lineas' => [
                [
                    'modelo' => 'Ojotas Clásicas Capioví',
                    'talle' => '37/38',
                    'color' => 'Negro',
                    'cantidad' => 2,
                    'precio_unitario' => 6500.0,
                    'precio_manual' => false,
                    'subtotal' => 13000.0,
                ],
                [
                    'modelo' => 'Remera Básica Algodón Capioví',
                    'talle' => 'M',
                    'color' => 'Gris',
                    'cantidad' => 1,
                    'precio_unitario' => 12500.0,
                    'precio_manual' => false,
                    'subtotal' => 12500.0,
                ],
            ],
            'subtotal' => 25500.0,
            'descuento_pct' => 10.0,
            'descuento_monto' => 2550.0,
            'total' => 22950.0,
            'hay_precio_manual' => false,
        ];
    }
}
