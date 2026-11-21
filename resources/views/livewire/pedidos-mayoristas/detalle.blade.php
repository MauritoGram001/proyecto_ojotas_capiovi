<div>
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <div>
            <h6 class="fw-bold mb-1">{{ $pedido['cliente'] }}</h6>
            <small class="text-muted">Pedido {{ $pedido['numero'] }} — {{ $pedido['fecha'] }}</small>
        </div>
        <span class="badge bg-light text-secondary border">Estado: {{ $pedido['estado'] }}</span>
    </div>

    <div class="alert alert-warning small py-2 px-3" role="alert">
        <i class="bi bi-info-circle me-1"></i> Este pedido <strong>no reserva stock</strong>. Las unidades listadas siguen disponibles hasta su conversión a venta.
    </div>

    <div class="table-responsive bg-white border rounded">
        <table class="table table-sm table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Modelo</th>
                    <th>Talle</th>
                    <th>Color</th>
                    <th class="text-center">Cant.</th>
                    <th class="text-end">Precio unit.</th>
                    <th class="text-end">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @php($total = 0.0)
                @foreach ($pedido['items'] as $item)
                    @php($subtotal = (float) $item['precio'] * (int) $item['cantidad'])
                    @php($total += $subtotal)
                    <tr>
                        <td class="fw-semibold small">{{ $item['modelo'] }}</td>
                        <td class="small">{{ $item['talle'] }}</td>
                        <td class="small">{{ $item['color'] }}</td>
                        <td class="text-center">{{ $item['cantidad'] }}</td>
                        <td class="text-end">${{ number_format($item['precio'], 0, ',', '.') }}</td>
                        <td class="text-end fw-semibold">${{ number_format($subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="table-active">
                <tr>
                    <td colspan="5" class="text-end fw-bold">Total del pedido</td>
                    <td class="text-end fw-bold text-primary">${{ number_format($total, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    @if ($pedido['convertido'])
        <div class="alert alert-success small py-2 px-3 mt-3 mb-0" role="status">
            <i class="bi bi-check-circle-fill me-1"></i> Convertido a venta confirmada (simulación).
        </div>
    @endif
</div>
