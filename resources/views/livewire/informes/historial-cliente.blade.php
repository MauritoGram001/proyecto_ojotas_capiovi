<div>
    <!-- Encabezado -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 pb-2 border-bottom">
        <div>
            <h1 class="h4 mb-0 fw-bold text-dark"><i class="bi bi-clock-history text-primary me-2"></i> Historial por Cliente</h1>
            <small class="text-muted">Compras de clientes identificados. No incluye compradores no identificados.</small>
        </div>
        <div class="mt-2 mt-md-0">
            <button wire:click="$set('tab', 'ventas')" class="btn btn-outline-primary btn-touch fw-semibold">
                <i class="bi bi-calendar-range me-1"></i> Ventas por período
            </button>
        </div>
    </div>

    <!-- Pestañas -->
    <ul class="nav nav-pills mb-3" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link btn-touch" type="button" wire:click="$set('tab', 'ventas')">
                <i class="bi bi-calendar-range me-1"></i> Ventas por período
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link btn-touch active" type="button" wire:click="$set('tab', 'cliente')">
                <i class="bi bi-clock-history me-1"></i> Historial por cliente
            </button>
        </li>
    </ul>

    <!-- Selector de cliente -->
    <div class="card border-0 shadow-sm p-3 mb-3">
        <div class="row g-2 align-items-end">
            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold small text-muted text-uppercase mb-1">Cliente identificado</label>
                <select wire:model.live="clienteSeleccionado" class="form-select">
                    @foreach ($clientesIdentificados as $cliente)
                        <option value="{{ $cliente }}">{{ $cliente }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Totales del cliente -->
    <div class="row g-2 mb-3">
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm p-3 h-100">
                <div class="small text-muted text-uppercase fw-semibold">Total de compras</div>
                <div class="fs-3 fw-bold text-primary">${{ number_format($totalesCliente['total'], 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm p-3 h-100">
                <div class="small text-muted text-uppercase fw-semibold">Cantidad de compras</div>
                <div class="fs-3 fw-bold text-dark">{{ $totalesCliente['cantidad'] }}</div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm p-3 h-100">
                <div class="small text-muted text-uppercase fw-semibold">Última compra</div>
                <div class="fs-5 fw-bold text-dark mt-2">
                    {{ $totalesCliente['ultimaCompra'] ? \Carbon\Carbon::parse($totalesCliente['ultimaCompra'])->format('d/m/Y') : '—' }}
                </div>
            </div>
        </div>
    </div>

    <!-- Detalle de compras -->
    <div class="card border-0 shadow-sm p-3">
        <h6 class="fw-bold text-secondary text-uppercase small mb-3"><i class="bi bi-list-check me-1"></i> Compras de {{ $clienteSeleccionado }}</h6>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Comprobante</th>
                        <th>Fecha</th>
                        <th class="text-center">Modo</th>
                        <th class="text-center">Ítems</th>
                        <th class="text-end">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($historial as $venta)
                        <tr>
                            <td class="fw-bold">{{ $venta['numero'] }}</td>
                            <td class="small text-muted">{{ \Carbon\Carbon::parse($venta['fecha'])->format('d/m/Y') }}</td>
                            <td class="text-center">
                                <span class="badge {{ $venta['modo'] === 'Mayorista' ? 'bg-info-subtle text-info' : 'bg-light text-secondary' }} border">
                                    {{ $venta['modo'] }}
                                </span>
                            </td>
                            <td class="text-center">{{ $venta['items'] }}</td>
                            <td class="text-end fw-semibold">${{ number_format($venta['total'], 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Este cliente no tiene compras registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
                @if (count($historial) > 0)
                    <tfoot class="table-active">
                        <tr>
                            <td colspan="4" class="text-end fw-bold">Total acumulado</td>
                            <td class="text-end fw-bold text-primary">${{ number_format($totalesCliente['total'], 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
        <div class="form-text mt-2">Promedio por compra: ${{ number_format($totalesCliente['promedio'], 0, ',', '.') }}. Datos ficticios de la fase visual.</div>
    </div>
</div>
