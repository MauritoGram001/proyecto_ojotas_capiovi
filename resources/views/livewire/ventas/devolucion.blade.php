<div>
    <!-- Encabezado -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 pb-2 border-bottom">
        <div>
            <h1 class="h4 mb-0 fw-bold text-dark"><i class="bi bi-arrow-counterclockwise text-primary me-2"></i> Anulaciones y Devoluciones</h1>
            <small class="text-muted">Correcciones de postventa. Toda anulación requiere confirmación justificada y queda vinculada a la venta original.</small>
        </div>
        <div class="mt-2 mt-md-0">
            <a href="{{ url('/ventas/crear') }}" class="btn btn-outline-primary btn-touch fw-semibold">
                <i class="bi bi-cart-plus me-1"></i> Nueva Venta
            </a>
        </div>
    </div>

    <!-- Pestañas -->
    <ul class="nav nav-pills mb-3" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link btn-touch {{ $tab === 'anulaciones' ? 'active' : '' }}" wire:click="$set('tab', 'anulaciones')" type="button">
                <i class="bi bi-x-octagon me-1"></i> Anulación de ventas
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link btn-touch {{ $tab === 'devoluciones' ? 'active' : '' }}" wire:click="$set('tab', 'devoluciones')" type="button">
                <i class="bi bi-arrow-repeat me-1"></i> Devoluciones por cambio
            </button>
        </li>
    </ul>

    @if ($tab === 'anulaciones')
        <!-- Listado de ventas -->
        <div class="card border-0 shadow-sm p-3 mb-3">
            <h6 class="fw-bold text-secondary text-uppercase small mb-3"><i class="bi bi-receipt me-1"></i> Ventas registradas</h6>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Comprobante</th>
                            <th>Fecha</th>
                            <th style="min-width: 180px;">Cliente</th>
                            <th class="text-end">Total</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center" style="min-width: 130px;">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($ventas as $venta)
                            <tr>
                                <td class="fw-bold">{{ $venta['numero'] }}</td>
                                <td class="small text-muted">{{ $venta['fecha'] }}</td>
                                <td class="small">{{ $venta['cliente'] }}</td>
                                <td class="text-end fw-semibold">${{ number_format($venta['total'], 0, ',', '.') }}</td>
                                <td class="text-center">
                                    @if ($venta['estado'] === 'Confirmada')
                                        <span class="badge bg-success-subtle text-success border px-2 py-1"><i class="bi bi-check-circle me-1"></i> Confirmada</span>
                                    @elseif ($venta['estado'] === 'Anulada')
                                        <span class="badge bg-danger-subtle text-danger border px-2 py-1"><i class="bi bi-x-circle me-1"></i> Anulada</span>
                                    @else
                                        <span class="badge bg-info-subtle text-info border px-2 py-1"><i class="bi bi-arrow-repeat me-1"></i> Devuelta</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($venta['estado'] === 'Confirmada')
                                        <button wire:click="abrirModalAnular({{ $venta['id'] }})" class="btn btn-sm btn-outline-danger btn-touch w-100">
                                            <i class="bi bi-x-octagon me-1"></i> Anular
                                        </button>
                                    @else
                                        <span class="text-muted small">Sin acciones</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Registros de anulación con vínculo a la venta original -->
        <div class="card border-0 shadow-sm p-3">
            <h6 class="fw-bold text-secondary text-uppercase small mb-3"><i class="bi bi-link-45deg me-1"></i> Anulaciones registradas</h6>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Anulación</th>
                            <th>Venta original</th>
                            <th>Motivo / justificación</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($anulaciones as $anulacion)
                            <tr>
                                <td class="fw-bold text-danger">{{ $anulacion['numero'] }}</td>
                                <td>
                                    <span class="badge bg-light text-secondary border">
                                        <i class="bi bi-arrow-left me-1"></i> {{ $anulacion['ventaNumero'] }}
                                    </span>
                                    <i class="bi bi-arrow-right mx-1 text-muted"></i>
                                    <span class="badge bg-danger-subtle text-danger border">{{ $anulacion['numero'] }}</span>
                                </td>
                                <td class="small">{{ $anulacion['motivo'] }}</td>
                                <td class="small text-muted">{{ $anulacion['fecha'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <!-- Formulario de devolución por cambio -->
        <div class="card border-0 shadow-sm p-3 mb-3">
            <h6 class="fw-bold text-secondary text-uppercase small mb-3"><i class="bi bi-arrow-repeat me-1"></i> Registrar devolución por cambio</h6>
            <div class="row g-3">
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">Venta original *</label>
                    <select wire:model.live="ventaDevolucionId" class="form-select">
                        @foreach ($ventas as $venta)
                            <option value="{{ $venta['id'] }}">{{ $venta['numero'] }} — {{ $venta['cliente'] }}</option>
                        @endforeach
                    </select>
                    @if ($ventaDevolucion)
                        <div class="form-text">
                            <i class="bi bi-link-45deg me-1"></i> Vinculada a <strong>{{ $ventaDevolucion['numero'] }}</strong> ({{ $ventaDevolucion['fecha'] }})
                        </div>
                    @endif
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">Artículo devuelto *</label>
                    <select wire:model="itemDevueltoId" class="form-select">
                        <option value="">Seleccionar de la venta original...</option>
                        @foreach ($itemsVenta as $item)
                            <option value="{{ $item['id'] }}">{{ $item['modelo'] }} — {{ $item['talle'] }} / {{ $item['color'] }} (x{{ $item['cantidad'] }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">Nuevo entregado *</label>
                    <select wire:model="nuevoArticuloId" class="form-select">
                        <option value="">Seleccionar variante...</option>
                        @foreach ($catalogo as $item)
                            <option value="{{ $item['id'] }}">{{ $item['modelo'] }} — {{ $item['talle'] }} / {{ $item['color'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-check mt-3">
                <input class="form-check-input" type="checkbox" wire:model="fallaFabricacion" id="fallaFabricacion">
                <label class="form-check-label small fw-semibold" for="fallaFabricacion">
                    Indicar falla de fabricación e ingresar el artículo a <strong>Stock defectuoso</strong>
                </label>
                <div class="form-text">Fuera de alcance: garantía externa o peritajes técnicos.</div>
            </div>
            <div class="mt-3">
                <button wire:click="registrarDevolucion" class="btn btn-primary btn-touch fw-semibold px-4">
                    <i class="bi bi-check-circle me-1"></i> Registrar Devolución (Simulada)
                </button>
            </div>
        </div>

        <!-- Devoluciones registradas -->
        <div class="card border-0 shadow-sm p-3 mb-3">
            <h6 class="fw-bold text-secondary text-uppercase small mb-3"><i class="bi bi-link-45deg me-1"></i> Devoluciones registradas</h6>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Devolución</th>
                            <th>Venta original</th>
                            <th>Artículo devuelto</th>
                            <th>Nuevo entregado</th>
                            <th class="text-center">Falla de fabricación</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($devoluciones as $devolucion)
                            <tr>
                                <td class="fw-bold text-primary">{{ $devolucion['numero'] }}</td>
                                <td>
                                    <span class="badge bg-light text-secondary border">
                                        <i class="bi bi-arrow-left me-1"></i> {{ $devolucion['ventaNumero'] }}
                                    </span>
                                    <i class="bi bi-arrow-right mx-1 text-muted"></i>
                                    <span class="badge bg-primary-subtle text-primary border">{{ $devolucion['numero'] }}</span>
                                </td>
                                <td class="small">{{ $devolucion['devuelto'] }}</td>
                                <td class="small">{{ $devolucion['nuevo'] }}</td>
                                <td class="text-center">
                                    @if ($devolucion['fallaFabricacion'])
                                        <span class="badge bg-danger-subtle text-danger border"><i class="bi bi-exclamation-triangle me-1"></i> Sí</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary border">No</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Stock defectuoso -->
        <div class="card border-0 shadow-sm p-3">
            <h6 class="fw-bold text-secondary text-uppercase small mb-3"><i class="bi bi-exclamation-triangle me-1"></i> Stock defectuoso (ingreso simulado)</h6>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Registro</th>
                            <th>Variante</th>
                            <th class="text-center">Cant.</th>
                            <th>Origen</th>
                            <th>Motivo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($stockDefectuoso as $item)
                            <tr>
                                <td class="fw-bold">{{ $item['numero'] }}</td>
                                <td class="small">{{ $item['modelo'] }} — {{ $item['talle'] }} / {{ $item['color'] }}</td>
                                <td class="text-center">{{ $item['cantidad'] }}</td>
                                <td><span class="badge bg-warning-subtle text-dark border">{{ $item['origen'] }}</span></td>
                                <td class="small text-muted">{{ $item['motivo'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Modal de confirmación obligatoria de anulación -->
    @include('livewire.ventas.anular-modal', ['ventaAnular' => $ventaAnular])
</div>
