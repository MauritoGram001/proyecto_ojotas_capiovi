<div>
    <!-- Encabezado -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 pb-2 border-bottom">
        <div>
            <h1 class="h4 mb-0 fw-bold text-dark"><i class="bi bi-arrow-repeat text-primary me-2"></i> Mercadería Defectuosa y Cambios</h1>
            <small class="text-muted">Registro de fallas y seguimiento del circuito de reemplazo con el proveedor.</small>
        </div>
        <div class="mt-2 mt-md-0 d-flex gap-2">
            <button wire:click="$set('tab', 'pedidos')" class="btn btn-outline-secondary btn-touch">
                <i class="bi bi-arrow-left me-1"></i> Pedidos
            </button>
            <button wire:click="abrirFormulario" class="btn btn-primary btn-touch fw-semibold">
                <i class="bi bi-plus-lg me-1"></i> Registrar mercadería defectuosa
            </button>
        </div>
    </div>

    <!-- Listado de cambios -->
    <div class="table-responsive bg-white shadow-sm">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Registro</th>
                    <th style="min-width: 190px;">Proveedor</th>
                    <th>Producto / Variante</th>
                    <th class="text-center">Cant.</th>
                    <th>Motivo</th>
                    <th class="text-center">Estado</th>
                    <th class="text-center" style="min-width: 160px;">Seguimiento</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($cambios as $cambio)
                    <tr>
                        <td>
                            <div class="fw-bold">{{ $cambio['numero'] }}</div>
                            <small class="text-muted">{{ $cambio['fecha'] }}</small>
                        </td>
                        <td class="small">{{ $cambio['proveedor'] }}</td>
                        <td class="small">{{ $cambio['producto'] }}</td>
                        <td class="text-center fw-semibold">{{ $cambio['cantidad'] }}</td>
                        <td class="small text-muted">{{ $cambio['motivo'] }}</td>
                        <td class="text-center">
                            @if ($cambio['estado'] === 'Detectada')
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                    <i class="bi bi-exclamation-circle me-1"></i> Detectada
                                </span>
                            @elseif ($cambio['estado'] === 'Enviada a cambio')
                                <span class="badge bg-warning-subtle text-dark border border-warning-subtle px-2 py-1">
                                    <i class="bi bi-truck me-1"></i> Enviada a cambio
                                </span>
                            @else
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                    <i class="bi bi-check-circle me-1"></i> Recibida en reemplazo
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if ($cambio['estado'] !== 'Recibida en reemplazo')
                                <button wire:click="avanzarEstado({{ $cambio['id'] }})" class="btn btn-sm btn-outline-primary btn-touch w-100">
                                    <i class="bi bi-arrow-right-circle me-1"></i> Avanzar estado
                                </button>
                            @else
                                <span class="text-success small fw-semibold"><i class="bi bi-check-lg me-1"></i> Circuito cerrado</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal de alta simulada -->
    @if ($mostrarFormulario)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title fw-bold">
                            <i class="bi bi-exclamation-triangle me-2"></i> Registrar mercadería defectuosa
                        </h5>
                        <button type="button" wire:click="cerrarFormulario" class="btn-close btn-close-white" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body p-3">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Proveedor *</label>
                            <select wire:model="formProveedor" class="form-select">
                                <option value="Fábrica de Calzados Misiones S.R.L.">Fábrica de Calzados Misiones S.R.L.</option>
                                <option value="Textiles del Sur S.A.">Textiles del Sur S.A.</option>
                                <option value="Insumos Capioví">Insumos Capioví</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Producto / Variante *</label>
                            <input type="text" wire:model="formProducto" class="form-control" placeholder="Ej: Ojotas Clásicas — 39/40 Azul">
                        </div>
                        <div class="row g-2">
                            <div class="col-4">
                                <label class="form-label fw-semibold">Cantidad</label>
                                <input type="number" min="1" wire:model="formCantidad" class="form-control">
                            </div>
                            <div class="col-8">
                                <label class="form-label fw-semibold">Motivo *</label>
                                <input type="text" wire:model="formMotivo" class="form-control" placeholder="Ej: Costura defectuosa">
                            </div>
                        </div>
                        <div class="form-text mt-2">El registro se crea en estado <strong>Detectada</strong> y luego puede avanzar a <strong>Enviada a cambio</strong> y <strong>Recibida en reemplazo</strong>.</div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" wire:click="cerrarFormulario" class="btn btn-outline-secondary btn-touch">Cancelar</button>
                        <button type="button" wire:click="guardarCambio" class="btn btn-primary btn-touch px-4 fw-bold">Guardar Simulado</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
