<div>
    <!-- Encabezado -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 pb-2 border-bottom">
        <div>
            <h1 class="h4 mb-0 fw-bold text-dark"><i class="bi bi-truck text-primary me-2"></i> Pedidos Mayoristas</h1>
            <small class="text-muted">Gestión de pedidos de clientes mayoristas y su seguimiento visual.</small>
        </div>
    </div>

    <!-- Indicador explícito: los pedidos NO reservan stock -->
    <div class="alert alert-warning border d-flex align-items-start mb-3" role="alert">
        <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
        <div>
            <strong>Importante: los pedidos mayoristas NO reservan stock.</strong>
            Registrar un pedido no descuenta unidades del inventario. La existencia solo se afecta al convertirlo en venta confirmada (simulación).
        </div>
    </div>

    <!-- Filtro -->
    <div class="card border-0 shadow-sm p-3 mb-3">
        <div class="row g-2 align-items-end">
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold small text-muted text-uppercase mb-1">Estado del pedido</label>
                <select wire:model.live="filtroEstado" class="form-select" aria-label="Filtrar pedidos mayoristas por estado">
                    <option value="">Todos los estados</option>
                    <option value="Pendiente">Pendiente</option>
                    <option value="Preparado">Preparado</option>
                    <option value="Entregado">Entregado</option>
                    <option value="Cancelado">Cancelado</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Listado -->
    <div class="table-responsive bg-white shadow-sm">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Pedido</th>
                    <th style="min-width: 190px;">Cliente mayorista</th>
                    <th>Fecha</th>
                    <th class="text-center">Ítems</th>
                    <th class="text-center">Estado</th>
                    <th class="text-center" style="min-width: 210px;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pedidosFiltrados as $pedido)
                    <tr>
                        <td class="fw-bold">{{ $pedido['numero'] }}</td>
                        <td class="small">{{ $pedido['cliente'] }}</td>
                        <td class="small text-muted">{{ $pedido['fecha'] }}</td>
                        <td class="text-center">
                            <button wire:click="verDetalle({{ $pedido['id'] }})" class="btn btn-sm btn-outline-primary btn-touch">
                                <i class="bi bi-eye me-1"></i> {{ count($pedido['items']) }}
                            </button>
                        </td>
                        <td class="text-center">
                            @switch($pedido['estado'])
                                @case('Pendiente')
                                    <span class="badge bg-warning-subtle text-dark border border-warning-subtle px-2 py-1"><i class="bi bi-hourglass-split me-1"></i> Pendiente</span>
                                    @break
                                @case('Preparado')
                                    <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1"><i class="bi bi-box-seam me-1"></i> Preparado</span>
                                    @break
                                @case('Entregado')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"><i class="bi bi-check-circle me-1"></i> Entregado</span>
                                    @break
                                @default
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1"><i class="bi bi-x-circle me-1"></i> Cancelado</span>
                            @endswitch

                            @if ($pedido['convertido'])
                                <div class="small text-success fw-semibold mt-1">Venta confirmada (simulada)</div>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex flex-wrap gap-1 justify-content-center">
                                @if ($pedido['estado'] === 'Pendiente')
                                    <button wire:click="cambiarEstado({{ $pedido['id'] }}, 'Preparado')" class="btn btn-sm btn-outline-primary btn-touch">Preparado</button>
                                @endif
                                @if ($pedido['estado'] === 'Preparado')
                                    <button wire:click="cambiarEstado({{ $pedido['id'] }}, 'Entregado')" class="btn btn-sm btn-outline-success btn-touch">Entregado</button>
                                @endif
                                @if (in_array($pedido['estado'], ['Pendiente', 'Preparado']))
                                    <button wire:click="cambiarEstado({{ $pedido['id'] }}, 'Cancelado')" class="btn btn-sm btn-outline-danger btn-touch">Cancelar</button>
                                    <button wire:click="convertirAVenta({{ $pedido['id'] }})" class="btn btn-sm btn-success btn-touch fw-semibold">
                                        <i class="bi bi-cash-coin me-1"></i> Convertir a venta
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                            <h6 class="fw-bold">No hay pedidos con ese estado</h6>
                            <button wire:click="$set('filtroEstado', '')" class="btn btn-sm btn-outline-secondary">Ver todos</button>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal de detalle -->
    @if ($pedidoDetalle)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-title fw-bold">
                            <i class="bi bi-file-earmark-text text-warning me-2"></i> Detalle {{ $pedidoDetalle['numero'] }}
                        </h5>
                        <button type="button" wire:click="cerrarDetalle" class="btn-close btn-close-white" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body bg-light p-3">
                        @include('livewire.pedidos-mayoristas.detalle', ['pedido' => $pedidoDetalle])
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" wire:click="cerrarDetalle" class="btn btn-outline-secondary btn-touch">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
