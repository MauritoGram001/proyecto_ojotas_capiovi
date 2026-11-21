<div>
    <!-- Encabezado -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 pb-2 border-bottom">
        <div>
            <h1 class="h4 mb-0 fw-bold text-dark"><i class="bi bi-truck text-primary me-2"></i> Pedidos a Proveedores</h1>
            <small class="text-muted">Seguimiento de pedidos y mercadería a recibir. Datos ficticios de la fase visual.</small>
        </div>
        <div class="mt-2 mt-md-0">
            <button wire:click="$set('tab', 'cambios')" class="btn btn-outline-primary btn-touch fw-semibold">
                <i class="bi bi-arrow-repeat me-1"></i> Ver mercadería defectuosa
            </button>
        </div>
    </div>

    <!-- Filtro de estados -->
    <div class="card border-0 shadow-sm p-3 mb-3">
        <div class="row g-2 align-items-end">
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold small text-muted text-uppercase mb-1">Estado del pedido</label>
                <select wire:model.live="filtroEstado" class="form-select" aria-label="Filtrar pedidos de proveedores por estado">
                    <option value="">Todos los estados</option>
                    <option value="Pendiente">Pendiente</option>
                    <option value="Recibido">Recibido</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Listado de pedidos -->
    <div class="table-responsive bg-white shadow-sm">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Pedido</th>
                    <th style="min-width: 200px;">Proveedor</th>
                    <th>Producto / Variante</th>
                    <th class="text-center">Cant.</th>
                    <th>Fechas</th>
                    <th class="text-center">Estado</th>
                    <th class="text-center" style="min-width: 150px;">Acción</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pedidosFiltrados as $pedido)
                    <tr>
                        <td class="fw-bold">{{ $pedido['numero'] }}</td>
                        <td class="small">{{ $pedido['proveedor'] }}</td>
                        <td class="small">{{ $pedido['producto'] }}</td>
                        <td class="text-center fw-semibold">{{ $pedido['cantidad'] }}</td>
                        <td class="small text-muted">
                            <div>Pedido: {{ $pedido['fecha'] }}</div>
                            <div>Esperada: {{ $pedido['esperada'] }}</div>
                        </td>
                        <td class="text-center">
                            @if ($pedido['estado'] === 'Recibido')
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                    <i class="bi bi-check-circle me-1"></i> Recibido
                                </span>
                            @else
                                <span class="badge bg-warning-subtle text-dark border border-warning-subtle px-2 py-1">
                                    <i class="bi bi-hourglass-split me-1"></i> Pendiente
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if ($pedido['estado'] === 'Pendiente')
                                <button wire:click="marcarRecibido({{ $pedido['id'] }})" class="btn btn-sm btn-outline-success btn-touch w-100">
                                    <i class="bi bi-check2 me-1"></i> Marcar recibido
                                </button>
                            @else
                                <span class="text-muted small">Sin acciones</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                            <h6 class="fw-bold">No hay pedidos con ese estado</h6>
                            <button wire:click="$set('filtroEstado', '')" class="btn btn-sm btn-outline-secondary">Ver todos</button>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
