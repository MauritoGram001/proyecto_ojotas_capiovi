<div>
    <!-- Encabezado de la Pantalla -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 pb-2 border-bottom">
        <div>
            <h1 class="h4 mb-0 fw-bold text-dark"><i class="bi bi-people text-primary me-2"></i> Clientes</h1>
            <small class="text-muted">Directorio de clientes minoristas y mayoristas. Datos ficticios de ejemplo.</small>
        </div>
        <div class="mt-2 mt-md-0">
            <button wire:click="abrirFormulario" class="btn btn-primary btn-touch fw-semibold">
                <i class="bi bi-person-plus me-1"></i> Nuevo Cliente
            </button>
        </div>
    </div>

    @if ($mensajeExito !== '')
        <div class="alert alert-success d-flex align-items-center mb-3" role="alert">
            <i class="bi bi-check-circle-fill fs-5 me-2"></i>
            <div>{{ $mensajeExito }}</div>
        </div>
    @endif

    <!-- Selector de cliente para venta rápida (vista previa del flujo de ventas) -->
    <div class="card p-3 mb-3 bg-white border-0 shadow-sm">
        <label for="clienteVenta" class="form-label fw-semibold mb-1">
            <i class="bi bi-lightning-charge text-warning me-1"></i> Venta rápida: ¿a quién se le vende?
        </label>
        <select id="clienteVenta" wire:model.live="clienteVenta" class="form-select">
            <option value="{{ \App\Livewire\Clientes\Index::CONSUMIDOR_FINAL }}">Consumidor final — sin identificar comprador</option>
            @foreach ($clientes as $c)
                <option value="{{ $c['id'] }}">{{ $c['nombre'] }} ({{ $c['tipo'] }})</option>
            @endforeach
        </select>
        <div class="small text-muted mt-2">
            @if ($clienteVentaActual)
                <i class="bi bi-person-check text-success me-1"></i>
                La venta quedaría asociada a <strong>{{ $clienteVentaActual['nombre'] }}</strong> ({{ $clienteVentaActual['tipo'] }}).
            @else
                <i class="bi bi-person-dash text-secondary me-1"></i>
                La venta se registra sin asociar un cliente.
            @endif
            <span class="d-block">Simulación del selector que se usará en la pantalla de ventas.</span>
        </div>
    </div>

    <!-- Búsqueda y filtro -->
    <div class="card p-3 mb-3 bg-white border-0 shadow-sm">
        <div class="row g-2">
            <div class="col-12 col-md-8">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" wire:model.live.debounce.300ms="buscar" class="form-control border-start-0" placeholder="Buscar por nombre o teléfono..." aria-label="Buscar cliente por nombre o teléfono">
                </div>
            </div>
            <div class="col-12 col-md-4">
                <select wire:model.live="filtroTipo" class="form-select" aria-label="Filtrar por tipo de cliente">
                    <option value="">Todos los clientes</option>
                    <option value="Minorista">Solo minoristas</option>
                    <option value="Mayorista">Solo mayoristas</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Listado -->
    <div class="table-responsive bg-white shadow-sm">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th scope="col" style="min-width: 220px;">Cliente</th>
                    <th scope="col" style="min-width: 200px;">Contacto</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($clientesFiltrados as $c)
                    <tr wire:key="cliente-{{ $c['id'] }}">
                        <td>
                            <div class="fw-bold text-dark">{{ $c['nombre'] }}</div>
                            @if ($c['tipo'] === 'Mayorista')
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle">Mayorista</span>
                                <small class="text-muted ms-1">CUIT {{ $c['cuit'] }}</small>
                            @else
                                <span class="badge bg-light text-secondary border">Minorista</span>
                            @endif
                            @if ($c['localidad'] !== '')
                                <div class="small text-muted mt-1"><i class="bi bi-geo-alt me-1"></i>{{ $c['localidad'] }}</div>
                            @endif
                        </td>
                        <td>
                            @if ($c['telefono'] !== '')
                                <div>
                                    <a href="tel:{{ preg_replace('/[^\d+]/', '', $c['telefono']) }}" class="text-decoration-none">
                                        <i class="bi bi-telephone me-1"></i>{{ $c['telefono'] }}
                                    </a>
                                </div>
                            @endif
                            @if ($c['email'] !== '')
                                <div class="small">
                                    <a href="mailto:{{ $c['email'] }}" class="text-decoration-none text-secondary">
                                        <i class="bi bi-envelope me-1"></i>{{ $c['email'] }}
                                    </a>
                                </div>
                            @endif
                            @if ($c['telefono'] === '' && $c['email'] === '')
                                <span class="text-muted small">Sin datos de contacto</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                            <h6 class="fw-bold">No se encontraron clientes</h6>
                            <p class="small mb-2">Probá con otro nombre o teléfono, o quitá el filtro.</p>
                            <button wire:click="limpiarFiltros" class="btn btn-sm btn-outline-secondary">Limpiar filtros</button>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal Formulario de Alta Simulado -->
    @if ($mostrarFormulario)
        <div class="modal fade show d-block" tabindex="-1" role="dialog" aria-modal="true" aria-labelledby="tituloFormularioCliente" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title fw-bold" id="tituloFormularioCliente">
                            <i class="bi bi-person-plus me-2"></i> Nuevo Cliente (Simulación)
                        </h5>
                        <button type="button" wire:click="cerrarFormulario" class="btn-close btn-close-white" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body p-3">
                        <div class="mb-3">
                            <label for="formNombre" class="form-label fw-semibold">Nombre o razón social *</label>
                            <input id="formNombre" type="text" wire:model="formNombre" class="form-control @error('formNombre') is-invalid @enderror" placeholder="Ej: Almacén Don Rubén">
                            @error('formNombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <span class="form-label fw-semibold d-block">Tipo de cliente</span>
                            <div class="btn-group w-100" role="group" aria-label="Tipo de cliente">
                                <input type="radio" class="btn-check" name="formTipo" id="tipoMinorista" value="Minorista" wire:model.live="formTipo" autocomplete="off">
                                <label class="btn btn-outline-primary btn-touch" for="tipoMinorista">Minorista</label>

                                <input type="radio" class="btn-check" name="formTipo" id="tipoMayorista" value="Mayorista" wire:model.live="formTipo" autocomplete="off">
                                <label class="btn btn-outline-primary btn-touch" for="tipoMayorista">Mayorista</label>
                            </div>
                        </div>

                        {{-- El CUIT solo aparece (y es obligatorio) para clientes mayoristas --}}
                        @if ($formTipo === 'Mayorista')
                            <div class="mb-3">
                                <label for="formCuit" class="form-label fw-semibold">CUIT *</label>
                                <input id="formCuit" type="text" inputmode="numeric" wire:model="formCuit" class="form-control @error('formCuit') is-invalid @enderror" placeholder="Ej: 30-12345678-9">
                                @error('formCuit') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        @endif

                        <div class="row g-2 mb-3">
                            <div class="col-12 col-sm-6">
                                <label for="formTelefono" class="form-label fw-semibold">Teléfono</label>
                                <input id="formTelefono" type="tel" wire:model="formTelefono" class="form-control" placeholder="Ej: 3743-456789">
                            </div>
                            <div class="col-12 col-sm-6">
                                <label for="formLocalidad" class="form-label fw-semibold">Localidad</label>
                                <input id="formLocalidad" type="text" wire:model="formLocalidad" class="form-control" placeholder="Ej: Capioví">
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
