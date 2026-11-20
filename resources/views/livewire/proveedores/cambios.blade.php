<div>
    @php
        $fecha = fn (string $iso) => \App\Services\CuentasEjemplo::fecha($iso);
    @endphp

    <!-- Encabezado de la Pantalla -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 pb-2 border-bottom">
        <div>
            <h1 class="h4 mb-0 fw-bold text-dark"><i class="bi bi-arrow-repeat text-primary me-2"></i> Mercadería Defectuosa y Cambios</h1>
            <small class="text-muted">Reclamos y cambios con el proveedor. Datos ficticios de ejemplo.</small>
        </div>
        <div class="mt-2 mt-md-0">
            <button type="button" wire:click="abrirFormulario" class="btn btn-primary btn-touch fw-semibold">
                <i class="bi bi-plus-lg me-1"></i> Registrar mercadería defectuosa
            </button>
        </div>
    </div>

    @include('livewire.proveedores._pestanas', ['activa' => 'cambios'])

    @if ($mensajeExito !== '')
        <div class="alert alert-success d-flex align-items-center mb-3" role="alert">
            <i class="bi bi-check-circle-fill fs-5 me-2"></i>
            <div>{{ $mensajeExito }}</div>
        </div>
    @endif

    <div class="alert alert-secondary d-flex align-items-start small mb-3" role="note">
        <i class="bi bi-info-circle-fill me-2 mt-1"></i>
        <div>La mercadería en <strong>Stock defectuoso</strong> no se suma al stock vendible. Sigue este circuito: <strong>Stock defectuoso</strong> → <strong>Enviada a cambio</strong> → <strong>Reemplazo recibido</strong>.</div>
    </div>

    <!-- Resumen por estado -->
    <div class="row g-2 mb-3">
        <div class="col-12 col-md-4">
            <div class="card p-3 bg-white border-0 shadow-sm h-100">
                <span class="badge bg-danger-subtle text-danger border border-danger-subtle align-self-start mb-1">Stock defectuoso</span>
                <div class="fs-3 fw-bold text-dark">{{ $cantidadDefectuoso }}</div>
                <small class="text-muted">{{ $unidadesDefectuosas }} unidad(es) en stock defectuoso</small>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="card p-3 bg-white border-0 shadow-sm h-100">
                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle align-self-start mb-1">Enviada a cambio</span>
                <div class="fs-3 fw-bold text-dark">{{ $cantidadEnviado }}</div>
                <small class="text-muted">esperando al proveedor</small>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="card p-3 bg-white border-0 shadow-sm h-100">
                <span class="badge bg-success-subtle text-success border border-success-subtle align-self-start mb-1">Reemplazo recibido</span>
                <div class="fs-3 fw-bold text-dark">{{ $cantidadRecibido }}</div>
                <small class="text-muted">cambios cerrados</small>
            </div>
        </div>
    </div>

    <!-- Filtro por estado -->
    <div class="card p-3 mb-3 bg-white border-0 shadow-sm">
        <select wire:model.live="filtroEstado" class="form-select" aria-label="Filtrar por estado">
            <option value="">Todos los estados</option>
            <option value="Stock defectuoso">Stock defectuoso</option>
            <option value="Enviada a cambio">Enviada a cambio</option>
            <option value="Reemplazo recibido">Reemplazo recibido</option>
        </select>
    </div>

    <!-- Listado de registros -->
    <div class="bg-white shadow-sm rounded">
        @forelse ($registrosFiltrados as $r)
            @php $v = $variantes[$r['variante_id']] ?? null; @endphp
            <div class="border-bottom p-3" wire:key="cambio-{{ $r['id'] }}">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
                    <div>
                        <div class="fw-bold text-dark">{{ $r['id'] }} <span class="fw-normal text-muted">· {{ $r['cantidad'] }} unidad(es)</span></div>
                        <div>{{ $v['modelo'] ?? 'Producto desconocido' }}</div>
                        <div class="small text-muted">Talle {{ $v['talle'] ?? '—' }} · {{ $v['color'] ?? '—' }}</div>
                        <div class="small mt-1"><span class="text-muted">Falla:</span> {{ $r['motivo'] }}</div>
                        <div class="small text-muted">{{ $r['origen'] }} · {{ $fecha($r['fecha']) }} · {{ $proveedores[$r['proveedor_id']] ?? 'Proveedor desconocido' }}</div>
                    </div>
                    @if ($r['estado'] === 'Stock defectuoso')
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle fs-6">Stock defectuoso</span>
                    @elseif ($r['estado'] === 'Enviada a cambio')
                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle fs-6">Enviada a cambio</span>
                    @else
                        <span class="badge bg-success-subtle text-success border border-success-subtle fs-6">Reemplazo recibido</span>
                    @endif
                </div>

                @if ($r['estado'] === 'Stock defectuoso')
                    <button type="button" wire:click="enviarACambio('{{ $r['id'] }}')" class="btn btn-sm btn-warning btn-touch mt-2">
                        <i class="bi bi-send me-1"></i> Enviar a cambio (simulado)
                    </button>
                @elseif ($r['estado'] === 'Enviada a cambio')
                    <button type="button" wire:click="registrarReemplazo('{{ $r['id'] }}')" class="btn btn-sm btn-success btn-touch mt-2">
                        <i class="bi bi-box-arrow-in-down me-1"></i> Registrar reemplazo recibido (simulado)
                    </button>
                @endif
            </div>
        @empty
            <div class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                <h6 class="fw-bold">No hay registros con ese estado</h6>
                <button type="button" wire:click="$set('filtroEstado', '')" class="btn btn-sm btn-outline-secondary mt-2">Ver todos</button>
            </div>
        @endforelse
    </div>

    <!-- Modal de registro de mercadería defectuosa -->
    @if ($mostrarFormulario)
        <div class="modal fade show d-block" tabindex="-1" role="dialog" aria-modal="true" aria-labelledby="tituloFormularioCambio" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title fw-bold" id="tituloFormularioCambio">
                            <i class="bi bi-exclamation-octagon me-2"></i> Registrar mercadería defectuosa (Simulación)
                        </h5>
                        <button type="button" wire:click="cerrarFormulario" class="btn-close btn-close-white" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body p-3">
                        <div class="mb-3">
                            <label for="formVariante" class="form-label fw-semibold">Producto *</label>
                            <select id="formVariante" wire:model="formVariante" class="form-select @error('formVariante') is-invalid @enderror">
                                <option value="">Elegí modelo, talle y color...</option>
                                @foreach ($variantes as $id => $va)
                                    <option value="{{ $id }}">{{ $va['modelo'] }} — {{ $va['talle'] }} · {{ $va['color'] }}</option>
                                @endforeach
                            </select>
                            @error('formVariante') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-5">
                                <label for="formCantidad" class="form-label fw-semibold">Cantidad *</label>
                                <input id="formCantidad" type="number" min="1" max="999" inputmode="numeric" wire:model="formCantidad" class="form-control @error('formCantidad') is-invalid @enderror">
                                @error('formCantidad') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-7">
                                <label for="formProveedor" class="form-label fw-semibold">Proveedor *</label>
                                <select id="formProveedor" wire:model="formProveedor" class="form-select @error('formProveedor') is-invalid @enderror">
                                    <option value="">Elegí proveedor...</option>
                                    @foreach ($proveedores as $id => $nombre)
                                        <option value="{{ $id }}">{{ $nombre }}</option>
                                    @endforeach
                                </select>
                                @error('formProveedor') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="formMotivo" class="form-label fw-semibold">Falla *</label>
                            <input id="formMotivo" type="text" wire:model="formMotivo" class="form-control @error('formMotivo') is-invalid @enderror" placeholder="Ej: Suela despegada">
                            @error('formMotivo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div>
                            <span class="form-label fw-semibold d-block">Cómo se detectó</span>
                            <div class="btn-group w-100" role="group" aria-label="Origen de la mercadería defectuosa">
                                <input type="radio" class="btn-check" name="formOrigen" id="origenDevolucion" value="Devolución de cliente" wire:model="formOrigen" autocomplete="off">
                                <label class="btn btn-outline-primary btn-touch" for="origenDevolucion">Devolución de cliente</label>

                                <input type="radio" class="btn-check" name="formOrigen" id="origenStock" value="Detectada en stock" wire:model="formOrigen" autocomplete="off">
                                <label class="btn btn-outline-primary btn-touch" for="origenStock">Detectada en stock</label>
                            </div>
                        </div>

                        <div class="small text-muted mt-3">
                            <i class="bi bi-info-circle me-1"></i> Queda en Stock defectuoso: no se suma al stock vendible.
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" wire:click="cerrarFormulario" class="btn btn-outline-secondary btn-touch">Cancelar</button>
                        <button type="button" wire:click="registrarSimulado" class="btn btn-primary btn-touch px-4 fw-bold">Registrar simulado</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
