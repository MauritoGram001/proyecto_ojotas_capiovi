<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Comprobante interno {{ $comprobante['numero'] }} — Ojotas Capioví</title>
    {{-- HTML simple y estilos en línea: compatible con DomPDF (sin flex ni grid). --}}
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #212529; margin: 24px; }
        h1 { font-size: 18px; margin: 0 0 2px 0; }
        .centro { text-align: center; }
        .derecha { text-align: right; }
        .tenue { color: #6c757d; }
        .aviso { border: 1px solid #333; padding: 4px 8px; text-align: center; font-weight: bold; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f1f3f5; text-align: left; padding: 6px; border-bottom: 1px solid #adb5bd; }
        td { padding: 6px; border-bottom: 1px solid #dee2e6; vertical-align: top; }
        .totales { width: 45%; margin-left: 55%; margin-top: 10px; }
        .totales td { border: 0; padding: 3px 6px; }
        .total td { font-size: 15px; font-weight: bold; border-top: 2px solid #212529; }
        .pie { margin-top: 24px; font-size: 10px; text-align: center; border-top: 1px solid #adb5bd; padding-top: 8px; }
    </style>
</head>
<body>
    @php
        $dinero = fn (float $v) => \App\Livewire\Ventas\Crear::dinero($v);
    @endphp

    <div class="centro">
        <h1>Ojotas Capioví</h1>
        <div class="tenue">Comprobante interno N.º {{ $comprobante['numero'] }}</div>
        <div class="tenue">{{ $comprobante['fecha'] }}</div>
    </div>

    <div class="aviso">COMPROBANTE INTERNO — NO VÁLIDO COMO FACTURA</div>

    <p>
        <strong>Cliente:</strong> {{ $comprobante['cliente'] }}<br>
        <strong>Lista de precios:</strong> {{ $comprobante['lista'] }}
    </p>

    <table>
        <thead>
            <tr>
                <th>Modelo</th>
                <th>Talle</th>
                <th>Color</th>
                <th class="derecha">Cant.</th>
                <th class="derecha">Precio cobrado</th>
                <th class="derecha">Importe</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($comprobante['lineas'] as $l)
                <tr>
                    <td>{{ $l['modelo'] }}</td>
                    <td>{{ $l['talle'] }}</td>
                    <td>{{ $l['color'] }}</td>
                    <td class="derecha">{{ $l['cantidad'] }}</td>
                    <td class="derecha">{{ $dinero($l['precio_unitario']) }}@if ($l['precio_manual']) *@endif</td>
                    <td class="derecha">{{ $dinero($l['subtotal']) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totales">
        <tr>
            <td>Subtotal</td>
            <td class="derecha">{{ $dinero($comprobante['subtotal']) }}</td>
        </tr>
        @if ($comprobante['descuento_pct'] > 0)
            <tr>
                <td>Descuento ({{ rtrim(rtrim(number_format($comprobante['descuento_pct'], 2, ',', '.'), '0'), ',') }} %)</td>
                <td class="derecha">− {{ $dinero($comprobante['descuento_monto']) }}</td>
            </tr>
        @endif
        <tr class="total">
            <td>TOTAL</td>
            <td class="derecha">{{ $dinero($comprobante['total']) }}</td>
        </tr>
    </table>

    @if ($comprobante['hay_precio_manual'])
        <p class="tenue">* Precio cambiado a mano para esta venta; no modifica el precio del catálogo.</p>
    @endif

    <div class="pie">
        Documento de uso interno. No es una factura ni un comprobante fiscal.
    </div>
</body>
</html>
