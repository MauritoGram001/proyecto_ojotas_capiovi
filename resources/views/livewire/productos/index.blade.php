<div>
    <!-- Encabezado de la Pantalla -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 pb-2 border-bottom">
        <div>
            <h1 class="h4 mb-0 fw-bold text-dark"><i class="bi bi-grid-3x3-gap text-primary me-2"></i> Productos & Existencias</h1>
            <small class="text-muted">Consulta de variantes (talle/color), precios diferenciados y alerta automática de bajo stock (&lt; 10).</small>
        </div>
        <div class="mt-2 mt-md-0">
            <button wire:click="abrirFormulario" class="btn btn-primary btn-touch fw-semibold">
                <i class="bi bi-plus-lg me-1"></i> Nuevo Producto
            </button>
        </div>
    </div>

    <!-- Barra de Búsqueda y Filtros Simplificada -->
    <div class="card p-3 mb-3 bg-white border-0 shadow-sm">
        <div class="row g-2">
            <div class="col-12 col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" wire:model.live.debounce.300ms="buscar" class="form-control border-start-0" placeholder="Buscar por modelo o nombre...">
                </div>
            </div>
            <div class="col-6 col-md-3">
                <select wire:model.live="filtroCategoria" class="form-select" aria-label="Filtrar productos por categoría">
                    <option value="">Todas las categorías</option>
                    <option value="Calzado">Calzado</option>
                    <option value="Textil">Textil</option>
                </select>
            </div>
            <div class="col-6 col-md-4">
                <select wire:model.live="filtroStock" class="form-select" aria-label="Filtrar productos por nivel de stock">
                    <option value="">Todo el inventario</option>
                    <option value="bajo">⚠️ Solo Bajo Stock (&lt; 10 pares/unid.)</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Listado de Productos -->
    <div class="table-responsive bg-white shadow-sm">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th scope="col" style="min-width: 200px;">Producto / Modelo</th>
                    <th scope="col">Precios (Min / May)</th>
                    <th scope="col">Stock Total</th>
                    <th scope="col" class="text-center" style="min-width: 130px;">Variantes</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($productosFiltrados as $prod)
                    @php
                        $alertaStock = $prod['stock_total'] < 10;
                    @endphp
                    <tr>
                        <td>
                            <div class="fw-bold text-dark">{{ $prod['modelo'] }}</div>
                            <span class="badge bg-light text-secondary border small">{{ $prod['categoria'] }}</span>
                        </td>
                        <td>
                            <div><strong class="text-dark">${{ number_format($prod['precio_minorista'], 0, ',', '.') }}</strong> <small class="text-muted">(Min)</small></div>
                            <small class="text-primary fw-semibold">${{ number_format($prod['precio_mayorista'], 0, ',', '.') }} <span class="text-muted fw-normal">(May)</span></small>
                        </td>
                        <td>
                            @if ($alertaStock)
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 fs-6">
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $prod['stock_total'] }} unid.
                                </span>
                                <div class="text-danger small mt-1 fw-semibold">¡Bajo stock (&lt; 10)!</div>
                            @else
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fs-6">
                                    <i class="bi bi-check-circle me-1"></i> {{ $prod['stock_total'] }} unid.
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <button wire:click="verDetalle({{ $prod['id'] }})" class="btn btn-sm btn-outline-primary btn-touch w-100">
                                <i class="bi bi-eye me-1"></i> Ver Talles ({{ count($prod['variantes']) }})
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                            <h6 class="fw-bold">No se encontraron productos</h6>
                            <p class="small mb-2">Probá modificando los términos de búsqueda o quitando los filtros.</p>
                            <button wire:click="$set('buscar', ''); $set('filtroCategoria', ''); $set('filtroStock', '');" class="btn btn-sm btn-outline-secondary">
                                Limpiar Filtros
                            </button>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal Detalle de Variantes (Talle y Color) -->
    @if ($productoDetalle)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-title fw-bold">
                            <i class="bi bi-boxes text-warning me-2"></i> {{ $productoDetalle['modelo'] }}
                        </h5>
                        <button type="button" wire:click="cerrarDetalle" class="btn-close btn-close-white" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body p-3">
                        <div class="d-flex justify-content-between mb-3 p-2 bg-light rounded">
                            <div>
                                <small class="text-muted d-block">Precio Minorista</small>
                                <strong class="fs-5 text-dark">${{ number_format($productoDetalle['precio_minorista'], 0, ',', '.') }}</strong>
                            </div>
                            <div class="text-end">
                                <small class="text-muted d-block">Precio Mayorista</small>
                                <strong class="fs-5 text-primary">${{ number_format($productoDetalle['precio_mayorista'], 0, ',', '.') }}</strong>
                            </div>
                        </div>

                        <h6 class="fw-bold mb-2 text-secondary">Disponibilidad por Variante (Talle / Color)</h6>
                        <div class="table-responsive border">
                            <table class="table table-sm table-striped align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Talle</th>
                                        <th>Color</th>
                                        <th class="text-end">Stock</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($productoDetalle['variantes'] as $v)
                                        <tr>
                                            <td class="fw-bold">{{ $v['talle'] }}</td>
                                            <td>{{ $v['color'] }}</td>
                                            <td class="text-end">
                                                @if ($v['stock'] < 10)
                                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                                        {{ $v['stock'] }} (Bajo)
                                                    </span>
                                                @else
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle">
                                                        {{ $v['stock'] }}
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" wire:click="cerrarDetalle" class="btn btn-secondary btn-touch px-4">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal de alta de producto -->
    @if ($mostrarFormulario)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title fw-bold">
                            <i class="bi bi-plus-circle me-2"></i> Nuevo Producto
                        </h5>
                        <button type="button" wire:click="cerrarFormulario" class="btn-close btn-close-white" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body p-3">
                        @if ($errors->any())
                            <div class="alert alert-danger py-2" role="alert">
                                <i class="bi bi-exclamation-triangle me-1"></i> {{ $errors->first() }}
                            </div>
                        @endif
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Modelo / Nombre del Producto *</label>
                            <input type="text" wire:model="formNombre" class="form-control" placeholder="Ej: Ojotas Urbanas Capioví">
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-semibold">Categoría</label>
                                <select wire:model="formCategoria" class="form-select">
                                    <option value="Calzado">Calzado</option>
                                    <option value="Textil">Textil</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold">Stock Inicial</label>
                                <input type="number" min="0" wire:model="formStock" class="form-control">
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-semibold">Precio Minorista ($)</label>
                                <input type="number" min="0" step="0.01" wire:model="formPrecioMinorista" class="form-control" placeholder="0">
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold">Precio Mayorista ($)</label>
                                <input type="number" min="0" step="0.01" wire:model="formPrecioMayorista" class="form-control" placeholder="0">
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label fw-semibold">Talle Variante</label>
                                <input type="text" wire:model="formTalle" class="form-control" placeholder="Ej: 39/40 o M">
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold">Color Variante</label>
                                <input type="text" wire:model="formColor" class="form-control" placeholder="Ej: Negro">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" wire:click="cerrarFormulario" class="btn btn-outline-secondary btn-touch">Cancelar</button>
                        <button type="button" wire:click="guardar" class="btn btn-primary btn-touch px-4 fw-bold">Guardar Producto</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
