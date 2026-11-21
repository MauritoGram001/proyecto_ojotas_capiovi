<div style="background:#ffffff; color:#212529; max-width:820px; margin:0 auto; padding:32px 36px; font-family:Arial, Helvetica, sans-serif; box-shadow:0 2px 10px rgba(0,0,0,0.15);">

    <table style="width:100%; border-collapse:collapse; margin-bottom:18px;">
        <tr>
            <td style="vertical-align:top;">
                <div style="font-size:20px; font-weight:bold; color:#0d6efd;">
                    <i class="bi bi-box-seam-fill"></i> Ojotas Capioví
                </div>
                <div style="font-size:11px; color:#6c757d; margin-top:3px;">Sistema de Gestión Interna</div>
            </td>
            <td style="text-align:right; vertical-align:top;">
                <div style="display:inline-block; border:2px solid #dc3545; color:#dc3545; font-weight:bold; font-size:12px; padding:5px 10px; transform:rotate(-3deg);">
                    NO FISCAL — USO INTERNO
                </div>
                <div style="font-size:16px; font-weight:bold; margin-top:8px;">COMPROBANTE INTERNO</div>
                <div style="font-size:12px; color:#6c757d;">{{ $venta['numero'] }}</div>
            </td>
        </tr>
    </table>

    <hr style="border:none; border-top:2px solid #212529; margin:0 0 16px 0;">

    <table style="width:100%; border-collapse:collapse; font-size:12px; margin-bottom:18px;">
        <tr>
            <td style="padding:4px 0; width:50%;"><strong>Fecha:</strong> {{ $venta['fecha'] }}</td>
            <td style="padding:4px 0;"><strong>Comprador:</strong> {{ $venta['cliente'] }}</td>
        </tr>
        <tr>
            <td style="padding:4px 0;"><strong>Modo de precios:</strong> {{ ucfirst($venta['modo']) }}</td>
            <td style="padding:4px 0;"><strong>Responsable:</strong> Mostrador</td>
        </tr>
    </table>

    <table style="width:100%; border-collapse:collapse; font-size:12px;">
        <thead>
            <tr style="background:#f1f3f5;">
                <th style="border:1px solid #dee2e6; padding:8px; text-align:left;">Modelo</th>
                <th style="border:1px solid #dee2e6; padding:8px; text-align:center;">Talle</th>
                <th style="border:1px solid #dee2e6; padding:8px; text-align:center;">Color</th>
                <th style="border:1px solid #dee2e6; padding:8px; text-align:center;">Cant.</th>
                <th style="border:1px solid #dee2e6; padding:8px; text-align:right;">Precio cobrado</th>
                <th style="border:1px solid #dee2e6; padding:8px; text-align:center;">Dto.</th>
                <th style="border:1px solid #dee2e6; padding:8px; text-align:right;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($venta['items'] as $item)
                @php
                    $bruto = (float) $item['precio_unitario'] * (int) $item['cantidad'];
                    $subtotalItem = $bruto * (1 - ((float) $item['descuento'] / 100));
                @endphp
                <tr>
                    <td style="border:1px solid #dee2e6; padding:8px;">{{ $item['modelo'] }}</td>
                    <td style="border:1px solid #dee2e6; padding:8px; text-align:center;">{{ $item['talle'] }}</td>
                    <td style="border:1px solid #dee2e6; padding:8px; text-align:center;">{{ $item['color'] }}</td>
                    <td style="border:1px solid #dee2e6; padding:8px; text-align:center;">{{ $item['cantidad'] }}</td>
                    <td style="border:1px solid #dee2e6; padding:8px; text-align:right;">${{ number_format((float) $item['precio_unitario'], 0, ',', '.') }}</td>
                    <td style="border:1px solid #dee2e6; padding:8px; text-align:center;">{{ $item['descuento'] }}%</td>
                    <td style="border:1px solid #dee2e6; padding:8px; text-align:right;">${{ number_format($subtotalItem, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table style="width:100%; border-collapse:collapse; margin-top:14px; font-size:12px;">
        <tr>
            <td style="text-align:right; padding:4px 8px; width:75%;"><strong>Subtotal:</strong></td>
            <td style="text-align:right; padding:4px 8px;">${{ number_format($venta['subtotal'], 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td style="text-align:right; padding:4px 8px;"><strong>Descuentos:</strong></td>
            <td style="text-align:right; padding:4px 8px; color:#dc3545;">− ${{ number_format($venta['descuento'], 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td style="text-align:right; padding:8px; background:#f1f3f5; font-size:15px;"><strong>TOTAL:</strong></td>
            <td style="text-align:right; padding:8px; background:#f1f3f5; font-size:15px; font-weight:bold; color:#0d6efd;">${{ number_format($venta['total'], 0, ',', '.') }}</td>
        </tr>
    </table>

    <hr style="border:none; border-top:1px solid #dee2e6; margin:20px 0 10px 0;">
    <p style="font-size:10px; color:#6c757d; text-align:center; margin:0;">
        Documento de carácter estrictamente interno. No constituye factura ni comprobante fiscal.<br>
        Ojotas Capioví — Maqueta visual (Fase B). Sin validez tributaria.
    </p>
</div>
