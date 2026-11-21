<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Comprobante {{ $venta['numero'] }}</title>
    <style>
        body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; color: #212529; font-size: 12px; margin: 24px; }
        .header { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        .brand { font-size: 20px; font-weight: bold; color: #0d6efd; }
        .muted { font-size: 11px; color: #6c757d; }
        .badge { display: inline-block; border: 2px solid #dc3545; color: #dc3545; font-weight: bold; font-size: 11px; padding: 4px 8px; }
        .title { font-size: 16px; font-weight: bold; margin-top: 6px; }
        hr.thick { border: none; border-top: 2px solid #212529; margin: 0 0 14px 0; }
        hr.thin { border: none; border-top: 1px solid #dee2e6; margin: 16px 0 8px 0; }
        .meta, .items, .totals { width: 100%; border-collapse: collapse; }
        .meta td { padding: 4px 0; vertical-align: top; }
        .items th, .items td { border: 1px solid #dee2e6; padding: 7px; }
        .items th { background: #f1f3f5; text-align: left; }
        .center { text-align: center; }
        .right { text-align: right; }
        .totals { margin-top: 12px; }
        .totals td { padding: 4px 8px; }
        .grand { background: #f1f3f5; font-size: 14px; font-weight: bold; }
        .grand .amount { color: #0d6efd; }
        .footer { font-size: 10px; color: #6c757d; text-align: center; margin-top: 6px; }
    </style>
</head>
<body>
    <table class="header">
        <tr>
            <td style="vertical-align: top;">
                <div class="brand">Ojotas Capioví</div>
                <div class="muted">Sistema de Gestión Interna</div>
            </td>
            <td style="text-align: right; vertical-align: top;">
                <span class="badge">NO FISCAL — USO INTERNO</span>
                <div class="title">COMPROBANTE INTERNO</div>
                <div class="muted">{{ $venta['numero'] }}</div>
            </td>
        </tr>
    </table>

    <hr class="thick">

    <table class="meta">
        <tr>
            <td style="width:50%;"><strong>Fecha:</strong> {{ $venta['fecha'] }}</td>
            <td><strong>Comprador:</strong> {{ $venta['cliente'] }}</td>
        </tr>
        <tr>
            <td><strong>Modo de precios:</strong> {{ ucfirst($venta['modo']) }}</td>
            <td><strong>Tipo de comprobante:</strong> Interno no fiscal</td>
        </tr>
    </table>

    <table class="items" style="margin-top: 14px;">
        <thead>
            <tr>
                <th>Modelo</th>
                <th class="center">Talle</th>
                <th class="center">Color</th>
                <th class="center">Cant.</th>
                <th class="right">Precio cobrado</th>
                <th class="center">Dto.</th>
                <th class="right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($venta['items'] as $item)
                @php
                    $bruto = (float) $item['precio_unitario'] * (int) $item['cantidad'];
                    $subtotalItem = $bruto * (1 - ((float) $item['descuento'] / 100));
                @endphp
                <tr>
                    <td>{{ $item['modelo'] }}</td>
                    <td class="center">{{ $item['talle'] }}</td>
                    <td class="center">{{ $item['color'] }}</td>
                    <td class="center">{{ $item['cantidad'] }}</td>
                    <td class="right">${{ number_format((float) $item['precio_unitario'], 0, ',', '.') }}</td>
                    <td class="center">{{ rtrim(rtrim(number_format((float) $item['descuento'], 2, ',', '.'), '0'), ',') }}%</td>
                    <td class="right">${{ number_format($subtotalItem, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <td class="right" style="width:75%;"><strong>Subtotal:</strong></td>
            <td class="right">${{ number_format($venta['subtotal'], 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="right"><strong>Descuentos:</strong></td>
            <td class="right" style="color:#dc3545;">− ${{ number_format($venta['descuento'], 0, ',', '.') }}</td>
        </tr>
        <tr class="grand">
            <td class="right">TOTAL:</td>
            <td class="right amount">${{ number_format($venta['total'], 0, ',', '.') }}</td>
        </tr>
    </table>

    <hr class="thin">
    <p class="footer">
        Documento de carácter estrictamente interno. No constituye factura ni comprobante fiscal.<br>
        Ojotas Capioví — Sistema de Gestión Interna. Sin validez tributaria.
    </p>
</body>
</html>
