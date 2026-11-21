<div>
    <!-- Encabezado de la Pantalla -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 pb-2 border-bottom">
        <div>
            <h1 class="h4 mb-0 fw-bold text-dark"><i class="bi bi-cash-stack text-primary me-2"></i> Cuentas Corrientes & Cobros</h1>
            <small class="text-muted">Saldos de clientes, deudas con proveedores e imputación simulada de pagos.</small>
        </div>
        <div class="mt-2 mt-md-0">
            <button wire:click="abrirCobro" class="btn btn-primary btn-touch fw-semibold" @if ($tab !== 'clientes') disabled @endif>
                <i class="bi bi-cash-coin me-1"></i> Registrar Cobro
            </button>
        </div>
    </div>

    <!-- Pestañas -->
    <ul class="nav nav-pills mb-3" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link btn-touch {{ $tab === 'clientes' ? 'active' : '' }}" wire:click="$set('tab', 'clientes')" type="button">
                <i class="bi bi-people me-1"></i> Clientes
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link btn-touch {{ $tab === 'proveedores' ? 'active' : '' }}" wire:click="$set('tab', 'proveedores')" type="button">
                <i class="bi bi-truck me-1"></i> Deudas con proveedores
            </button>
        </li>
    </ul>

    @if ($tab === 'clientes')
        <div class="row g-3">
            <!-- Selector de cliente -->
            <div class="col-12 col-lg-4">
                <div class="card border-0 shadow-sm p-3 h-100">
                    <h6 class="fw-bold text-secondary text-uppercase small mb-3">Clientes con cuenta</h6>
                    <div class="list-group list-group-flush">
                        @foreach ($clientes as $cliente)
                            <button wire:click="$set('clienteId', '{{ $cliente['id'] }}')" type="button"
                                class="list-group-item list-group-item-action d-flex justify-content-between align-items-center px-2 py-3 {{ (string) $cliente['id'] === $clienteId ? 'active' : '' }}">
                                <span>
                                    <span class="fw-semibold d-block">{{ $cliente['nombre'] }}</span>
                                    <small class="text-muted">{{ $cliente['telefono'] }}</small>
                                </span>
                                <span class="badge {{ $cliente['saldo'] > 0 ? 'bg-warning text-dark' : 'bg-success' }} rounded-pill">
                                    ${{ number_format($cliente['saldo'], 0, ',', '.') }}
                                </span>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Saldo y movimientos -->
            <div class="col-12 col-lg-8">
                <div class="card border-0 shadow-sm mb-3 p-3">
                    <div class="d-flex flex-wrap justify-content-between align-items-center">
                        <div>
                            <h6 class="fw-bold text-secondary text-uppercase small mb-1">Saldo actual</h6>
                            <div class="fs-3 fw-bold text-dark">{{ $clienteSeleccionado['nombre'] ?? 'Consumidor Final' }}</div>
                        </div>
                        <div class="text-md-end mt-2 mt-md-0">
                            <div class="small text-muted">Deuda pendiente</div>
                            <div class="fs-2 fw-bold {{ ($clienteSeleccionado['saldo'] ?? 0) > 0 ? 'text-danger' : 'text-success' }}">
                                ${{ number_format($clienteSeleccionado['saldo'] ?? 0, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm p-3">
                    <h6 class="fw-bold text-secondary text-uppercase small mb-3"><i class="bi bi-clock-history me-1"></i> Movimientos</h6>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Fecha</th>
                                    <th>Tipo</th>
                                    <th>Detalle</th>
                                    <th class="text-end">Monto</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($movimientosCliente as $movimiento)
                                    <tr>
                                        <td class="small">{{ $movimiento['fecha'] }}</td>
                                        <td>
                                            <span class="badge {{ $movimiento['tipo'] === 'Boleta' ? 'bg-primary-subtle text-primary' : 'bg-success-subtle text-success' }} border">
                                                {{ $movimiento['tipo'] }}
                                            </span>
                                        </td>
                                        <td class="small">{{ $movimiento['detalle'] }}</td>
                                        <td class="text-end fw-semibold {{ $movimiento['monto'] < 0 ? 'text-success' : '' }}">
                                            ${{ number_format(abs($movimiento['monto']), 0, ',', '.') }}
                                        </td>
                                        <td><span class="small text-muted">{{ $movimiento['estado'] }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">Este cliente no tiene movimientos registrados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Deudas con proveedores -->
        <div class="card border-0 shadow-sm p-3">
            <h6 class="fw-bold text-secondary text-uppercase small mb-3"><i class="bi bi-truck me-1"></i> Deudas pendientes con proveedores</h6>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="min-width: 220px;">Proveedor</th>
                            <th>Contacto</th>
                            <th>Vencimiento</th>
                            <th class="text-end">Deuda</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($proveedores as $proveedor)
                            <tr>
                                <td class="fw-semibold">{{ $proveedor['razon_social'] }}</td>
                                <td class="small text-muted">{{ $proveedor['contacto'] }}</td>
                                <td class="small">{{ $proveedor['vencimiento'] }}</td>
                                <td class="text-end fw-bold text-danger">${{ number_format($proveedor['deuda'], 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Modal de cobro combinado -->
    @if ($mostrarCobro && $tab === 'clientes')
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.6);">
            <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title fw-bold">
                            <i class="bi bi-cash-coin me-2"></i> Cobro a {{ $clienteSeleccionado['nombre'] ?? 'Consumidor Final' }}
                        </h5>
                        <button type="button" wire:click="cerrarCobro" class="btn-close btn-close-white" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body bg-light p-3">
                        <livewire:cobros.formulario
                            :cliente-id="$clienteId"
                            :cliente-nombre="$clienteSeleccionado['nombre'] ?? 'Consumidor Final'"
                            :boletas="$boletasPendientes"
                        />
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" wire:click="cerrarCobro" class="btn btn-outline-secondary btn-touch">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
