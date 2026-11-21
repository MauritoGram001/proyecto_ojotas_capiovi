<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class ComprobanteController extends Controller
{
    public function descargar(Venta $venta): Response
    {
        $venta->loadMissing('items');
        $comprobante = $venta->toComprobanteArray();

        $pdf = Pdf::loadView('pdf.comprobante', ['venta' => $comprobante])
            ->setPaper('a4');

        $nombre = 'comprobante-'.$venta->numero.'.pdf';

        return $pdf->download($nombre);
    }
}
