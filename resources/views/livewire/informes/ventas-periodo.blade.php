<div>
    <!-- Encabezado -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 pb-2 border-bottom">
        <div>
            <h1 class="h4 mb-0 fw-bold text-dark"><i class="bi bi-graph-up text-primary me-2"></i> Informes e Historial</h1>
            <small class="text-muted">Consultas de ventas por día y período, e historial por cliente. Uso interno para dueño y contador.</small>
        </div>
        <div class="mt-2 mt-md-0">
            <button wire:click="$set('tab', 'cliente')" class="btn btn-outline-primary btn-touch fw-semibold">
                <i class="bi bi-person-lines-fill me-1"></i> Historial por cliente
            </button>
        </div>
    </div>

    <!-- Pestañas -->
    <ul class="nav nav-pills mb-3" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link btn-touch active" type="button" wire:click="$set('tab', 'ventas')">
                <i class="bi bi-calendar-range me-1"></i> Ventas por período
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link btn-touch" type="button" wire:click="$set('tab', 'cliente')">
                <i class="bi bi-clock-history me-1"></i> Historial por cliente
            </button>
        </li>
    </ul>

    <!-- Filtros de fecha -->
    <div class="card border-0 shadow-sm p-3 mb-3">
        <div class="row g-2 align-items-end">
            <div class="col-12 col-md-3">
                <label class="form-label fw-semibold small text-muted text-uppercase mb-1">Desde</label>
                <input type="date" wire:model.live="desde" class="form-control" aria-label="Fecha desde">
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label fw-semibold small text-muted text-uppercase mb-1">Hasta</label>
                <input type="date" wire:model.live="hasta" class="form-control" aria-label="Fecha hasta">
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label fw-semibold small text-muted text-uppercase mb-1">Vista</label>
                <select wire:model.live="vista" class="form-select" aria-label="Vista del informe">
                    <option value="detalle">Detalle de ventas</option>
                    <option value="diario">Resumen diario</option>
                    <option value="mensual">Resumen mensual</option>
                </select>
            </div>
            <div class="col-12 col-md-3">
                <div class="btn-group w-100" role="group" aria-label="Períodos rápidos">
                    <button wire:click="aplicarPreset('hoy')" class="btn btn-outline-secondary btn-touch">Hoy</button>
                    <button wire:click="aplicarPreset('semana')" class="btn btn-outline-secondary btn-touch">Semana</button>
                    <button wire:click="aplicarPreset('mes')" class="btn btn-outline-secondary btn-touch">Mes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Totales consolidados -->
    <div class="row g-2 mb-3">
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm p-3 h-100">
                <div class="small text-muted text-uppercase fw-semibold">Total del período</div>
                <div class="fs-3 fw-bold text-primary">${{ number_format($totales['total'], 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm p-3 h-100">
                <div class="small text-muted text-uppercase fw-semibold">Cantidad de ventas</div>
                <div class="fs-3 fw-bold text-dark">{{ $totales['cantidad'] }}</div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm p-3 h-100">
                <div class="small text-muted text-uppercase fw-semibold">Ticket promedio</div>
                <div class="fs-3 fw-bold text-dark">${{ number_format($totales['promedio'], 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    <!-- Detalle o resumen -->
    <div class="card border-0 shadow-sm p-3">
        @if ($vista === 'detalle')
            <h6 class="fw-bold text-secondary text-uppercase small mb-3"><i class="bi bi-list-check me-1"></i> Detalle de ventas</h6>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Comprobante</th>
                            <th>Fecha</th>
                            <th style="min-width: 190px;">Cliente</th>
                            <th class="text-center">Modo</th>
                            <th class="text-center">Ítems</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($ventasFiltradas as $venta)
                            <tr>
                                <td class="fw-bold">{{ $venta['numero'] }}</td>
                                <td class="small text-muted">{{ \Carbon\Carbon::parse($venta['fecha'])->format('d/m/Y') }}</td>
                                <td class="small">{{ $venta['cliente'] }}</td>
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
                                <td colspan="6" class="text-center py-4 text-muted">No hay ventas en el período seleccionado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if (count($ventasFiltradas) > 0)
                        <tfoot class="table-active">
                            <tr>
                                <td colspan="5" class="text-end fw-bold">Total del período</td>
                                <td class="text-end fw-bold text-primary">${{ number_format($totales['total'], 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        @else
            <h6 class="fw-bold text-secondary text-uppercase small mb-3">
                <i class="bi bi-calendar3 me-1"></i> {{ $vista === 'diario' ? 'Resumen diario' : 'Resumen mensual' }}
            </h6>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ $vista === 'diario' ? 'Fecha' : 'Mes' }}</th>
                            <th class="text-center">Cantidad de ventas</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($agrupado as $grupo)
                            <tr>
                                <td class="fw-semibold">
                                    {{ $vista === 'diario' ? \Carbon\Carbon::parse($grupo['clave'])->format('d/m/Y') : \Carbon\Carbon::createFromFormat('Y-m', $grupo['clave'])->format('m/Y') }}
                                </td>
                                <td class="text-center">{{ $grupo['cantidad'] }}</td>
                                <td class="text-end fw-semibold">${{ number_format($grupo['total'], 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">No hay ventas en el período seleccionado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="table-active">
                        <tr>
                            <td class="fw-bold">Total del período</td>
                            <td class="text-center fw-bold">{{ $totales['cantidad'] }}</td>
                            <td class="text-end fw-bold text-primary">${{ number_format($totales['total'], 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
        <div class="form-text mt-2">Datos ficticios de la fase visual. Sin exportaciones fiscales ni gráficos decorativos.</div>
    </div>
</div>
