<div>
    <!-- Encabezado de la Pantalla -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 pb-2 border-bottom">
        <div>
            <h1 class="h4 mb-0 fw-bold text-dark"><i class="bi bi-building text-primary me-2"></i> Proveedores</h1>
            <small class="text-muted">Datos de contacto de los proveedores de calzado y textil. Datos ficticios de ejemplo.</small>
        </div>
        <div class="mt-2 mt-md-0">
            <button wire:click="abrirFormulario" class="btn btn-primary btn-touch fw-semibold">
                <i class="bi bi-plus-lg me-1"></i> Nuevo Proveedor
            </button>
        </div>
    </div>

    @include('livewire.proveedores._pestanas', ['activa' => 'directorio'])

    @if ($mensajeExito !== '')
        <div class="alert alert-success d-flex align-items-center mb-3" role="alert">
            <i class="bi bi-check-circle-fill fs-5 me-2"></i>
            <div>{{ $mensajeExito }}</div>
        </div>
    @endif

    <!-- Búsqueda y filtro -->
    <div class="card p-3 mb-3 bg-white border-0 shadow-sm">
        <div class="row g-2">
            <div class="col-12 col-md-8">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" wire:model.live.debounce.300ms="buscar" class="form-control border-start-0" placeholder="Buscar por proveedor, contacto o teléfono..." aria-label="Buscar proveedor por nombre, contacto o teléfono">
                </div>
            </div>
            <div class="col-12 col-md-4">
                <select wire:model.live="filtroRubro" class="form-select" aria-label="Filtrar por rubro">
                    <option value="">Todos los rubros</option>
                    <option value="Calzado">Calzado</option>
                    <option value="Textil">Textil</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Listado -->
    <div class="table-responsive bg-white shadow-sm">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th scope="col" style="min-width: 220px;">Proveedor</th>
                    <th scope="col" style="min-width: 220px;">Contacto</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($proveedoresFiltrados as $p)
                    <tr wire:key="proveedor-{{ $p['id'] }}">
                        <td>
                            <div class="fw-bold text-dark">{{ $p['nombre'] }}</div>
                            <span class="badge bg-light text-secondary border">{{ $p['rubro'] }}</span>
                            @if ($p['localidad'] !== '')
                                <div class="small text-muted mt-1"><i class="bi bi-geo-alt me-1"></i>{{ $p['localidad'] }}</div>
                            @endif
                        </td>
                        <td>
                            @if ($p['contacto'] !== '')
                                <div class="fw-semibold"><i class="bi bi-person me-1 text-secondary"></i>{{ $p['contacto'] }}</div>
                            @endif
                            @if ($p['telefono'] !== '')
                                <div>
                                    <a href="tel:{{ preg_replace('/[^\d+]/', '', $p['telefono']) }}" class="text-decoration-none">
                                        <i class="bi bi-telephone me-1"></i>{{ $p['telefono'] }}
                                    </a>
                                </div>
                            @endif
                            @if ($p['email'] !== '')
                                <div class="small">
                                    <a href="mailto:{{ $p['email'] }}" class="text-decoration-none text-secondary">
                                        <i class="bi bi-envelope me-1"></i>{{ $p['email'] }}
                                    </a>
                                </div>
                            @endif
                            @if ($p['contacto'] === '' && $p['telefono'] === '' && $p['email'] === '')
                                <span class="text-muted small">Sin datos de contacto</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                            <h6 class="fw-bold">No se encontraron proveedores</h6>
                            <p class="small mb-2">Probá con otro nombre, contacto o teléfono, o quitá el filtro.</p>
                            <button wire:click="limpiarFiltros" class="btn btn-sm btn-outline-secondary">Limpiar filtros</button>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal Formulario de Alta Simulado -->
    @if ($mostrarFormulario)
        <div class="modal fade show d-block" tabindex="-1" role="dialog" aria-modal="true" aria-labelledby="tituloFormularioProveedor" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title fw-bold" id="tituloFormularioProveedor">
                            <i class="bi bi-building-add me-2"></i> Nuevo Proveedor (Simulación)
                        </h5>
                        <button type="button" wire:click="cerrarFormulario" class="btn-close btn-close-white" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body p-3">
                        <div class="mb-3">
                            <label for="formNombre" class="form-label fw-semibold">Nombre del proveedor *</label>
                            <input id="formNombre" type="text" wire:model="formNombre" class="form-control @error('formNombre') is-invalid @enderror" placeholder="Ej: Calzados Río Paraná S.A.">
                            @error('formNombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <span class="form-label fw-semibold d-block">Rubro</span>
                            <div class="btn-group w-100" role="group" aria-label="Rubro del proveedor">
                                <input type="radio" class="btn-check" name="formRubro" id="rubroCalzado" value="Calzado" wire:model.live="formRubro" autocomplete="off">
                                <label class="btn btn-outline-primary btn-touch" for="rubroCalzado">Calzado</label>

                                <input type="radio" class="btn-check" name="formRubro" id="rubroTextil" value="Textil" wire:model.live="formRubro" autocomplete="off">
                                <label class="btn btn-outline-primary btn-touch" for="rubroTextil">Textil</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="formContacto" class="form-label fw-semibold">Persona de contacto</label>
                            <input id="formContacto" type="text" wire:model="formContacto" class="form-control" placeholder="Ej: Hernán Giménez">
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-12 col-sm-6">
                                <label for="formTelefono" class="form-label fw-semibold">Teléfono</label>
                                <input id="formTelefono" type="tel" wire:model="formTelefono" class="form-control" placeholder="Ej: 376-4421900">
                            </div>
                            <div class="col-12 col-sm-6">
                                <label for="formLocalidad" class="form-label fw-semibold">Localidad</label>
                                <input id="formLocalidad" type="text" wire:model="formLocalidad" class="form-control" placeholder="Ej: Posadas">
                            </div>
                        </div>

                        <div>
                            <label for="formEmail" class="form-label fw-semibold">Email</label>
                            <input id="formEmail" type="email" wire:model="formEmail" class="form-control @error('formEmail') is-invalid @enderror" placeholder="Opcional">
                            @error('formEmail') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" wire:click="cerrarFormulario" class="btn btn-outline-secondary btn-touch">Cancelar</button>
                        <button type="button" wire:click="guardarSimulado" class="btn btn-primary btn-touch px-4 fw-bold">Guardar Simulado</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
