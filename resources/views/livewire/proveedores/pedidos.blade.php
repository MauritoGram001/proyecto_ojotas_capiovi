<div>
    @php
        $fecha = fn (string $iso) => \App\Services\CuentasEjemplo::fecha($iso);
    @endphp

    <!-- Encabezado de la Pantalla -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 pb-2 border-bottom">
        <div>
            <h1 class="h4 mb-0 fw-bold text-dark"><i class="bi bi-truck text-primary me-2"></i> Pedidos a Proveedores</h1>
            <small class="text-muted">Seguimiento de lo pedido a cada proveedor. Datos ficticios de ejemplo.</small>
        </div>
    </div>

    @include('livewire.proveedores._pestanas', ['activa' => 'pedidos'])

    @if ($mensajeExito !== '')
        <div class="alert alert-success d-flex align-items-center mb-3" role="alert">
            <i class="bi bi-check-circle-fill fs-5 me-2"></i>
            <div>{{ $mensajeExito }}</div>
        </div>
    @endif

    <!-- Resumen por estado -->
    <div class="row g-2 mb-3">
        <div class="col-6">
            <div class="card p-3 bg-white border-0 shadow-sm h-100">
                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle align-self-start mb-1">Pendiente</span>
                <div class="fs-3 fw-bold text-dark">{{ $pendientes }}</div>
                <small class="text-muted">pedido(s) esperando entrega</small>
            </div>
        </div>
        <div class="col-6">
            <div class="card p-3 bg-white border-0 shadow-sm h-100">
                <span class="badge bg-success-subtle text-success border border-success-subtle align-self-start mb-1">Recibido</span>
                <div class="fs-3 fw-bold text-dark">{{ $recibidos }}</div>
                <small class="text-muted">pedido(s) ya entregado(s)</small>
            </div>
        </div>
    </div>

    <!-- Búsqueda y filtros -->
    <div class="card p-3 mb-3 bg-white border-0 shadow-sm">
        <div class="row g-2">
            <div class="col-12 col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" wire:model.live.debounce.300ms="buscar" class="form-control border-start-0" placeholder="Buscar por n.º de pedido o modelo..." aria-label="Buscar pedido">
                </div>
            </div>
            <div class="col-6 col-md-3">
                <select wire:model.live="filtroEstado" class="form-select" aria-label="Filtrar por estado">
                    <option value="">Todos los estados</option>
                    <option value="Pendiente">Pendiente</option>
                    <option value="Recibido">Recibido</option>
                </select>
            </div>
            <div class="col-6 col-md-4">
                <select wire:model.live="filtroProveedor" class="form-select" aria-label="Filtrar por proveedor">
                    <option value="">Todos los proveedores</option>
                    @foreach ($proveedores as $id => $nombre)
                        <option value="{{ $id }}">{{ $nombre }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Listado de pedidos -->
    <div class="bg-white shadow-sm rounded">
        @forelse ($pedidosFiltrados as $p)
            @php
                $abierto = $pedidoAbierto === $p['id'];
                $unidades = array_sum(array_column($p['items'], 'cantidad'));
            @endphp
            <div class="border-bottom p-3" wire:key="pedido-{{ $p['id'] }}">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
                    <div>
                        <div class="fw-bold text-dark">{{ $p['id'] }} <span class="fw-normal text-muted">· {{ $proveedores[$p['proveedor_id']] ?? 'Proveedor desconocido' }}</span></div>
                        <div class="small text-muted">Pedido el {{ $fecha($p['fecha']) }} · {{ count($p['items']) }} producto(s) · {{ $unidades }} unidades</div>
                        @if ($p['estado'] === 'Recibido')
                            <div class="small text-success">Recibido el {{ $fecha($p['fecha_recepcion']) }}</div>
                        @else
                            <div class="small text-muted">Entrega estimada: {{ $fecha($p['entrega_estimada']) }}</div>
                        @endif
                    </div>
                    @if ($p['estado'] === 'Recibido')
                        <span class="badge bg-success-subtle text-success border border-success-subtle fs-6">Recibido</span>
                    @else
                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle fs-6">Pendiente</span>
                    @endif
                </div>

                <div class="d-flex flex-wrap gap-2 mt-2">
                    <button type="button" wire:click="alternarPedido('{{ $p['id'] }}')" class="btn btn-sm btn-outline-primary btn-touch">
                        <i class="bi bi-list-ul me-1"></i> {{ $abierto ? 'Ocultar detalle' : 'Ver detalle' }}
                    </button>
                    @if ($p['estado'] === 'Pendiente')
                        <button type="button" wire:click="marcarRecibido('{{ $p['id'] }}')" class="btn btn-sm btn-success btn-touch">
                            <i class="bi bi-box-arrow-in-down me-1"></i> Marcar como recibido (simulado)
                        </button>
                    @endif
                </div>

                @if ($abierto)
                    <div class="table-responsive border mt-3">
                        <table class="table table-sm align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Producto</th>
                                    <th>Talle</th>
                                    <th>Color</th>
                                    <th class="text-end">Cantidad</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($p['items'] as $item)
                                    @php $v = \App\Services\AbastecimientoEjemplo::variantes()[$item['variante_id']] ?? null; @endphp
                                    <tr>
                                        <td>{{ $v['modelo'] ?? 'Producto desconocido' }}</td>
                                        <td>{{ $v['talle'] ?? '—' }}</td>
                                        <td>{{ $v['color'] ?? '—' }}</td>
                                        <td class="text-end fw-semibold">{{ $item['cantidad'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        @empty
            <div class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                <h6 class="fw-bold">No se encontraron pedidos</h6>
                <p class="small mb-2">Probá con otra búsqueda o quitá los filtros.</p>
                <button type="button" wire:click="limpiarFiltros" class="btn btn-sm btn-outline-secondary">Limpiar filtros</button>
            </div>
        @endforelse
    </div>
</div>
