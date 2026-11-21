<div>
    <!-- Encabezado de la Pantalla -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 pb-2 border-bottom">
        <div>
            <h1 class="h4 mb-0 fw-bold text-dark"><i class="bi bi-people text-primary me-2"></i> Clientes</h1>
            <small class="text-muted">Altas, ediciones y bajas de clientes minoristas y mayoristas con datos de contacto.</small>
        </div>
        <div class="mt-2 mt-md-0">
            <button wire:click="abrirFormulario" class="btn btn-primary btn-touch fw-semibold">
                <i class="bi bi-plus-lg me-1"></i> Nuevo Cliente
            </button>
        </div>
    </div>

    <!-- Selección de comprador para venta rápida; admite consumidor final -->
    <div class="card border-0 shadow-sm mb-3 p-3">
        <div class="row g-2 align-items-end">
            <div class="col-12 col-md-7">
                <label class="form-label fw-semibold mb-1 small text-uppercase text-muted">Venta rápida</label>
                <select wire:model="compradorRapido" class="form-select" aria-label="Comprador para venta rápida">
                    <option value="">Seleccionar comprador...</option>
                    @foreach ($clientesFiltrados as $cliente)
                        <option value="{{ $cliente['id'] }}">{{ $cliente['nombre'] }} ({{ $cliente['tipo'] }})</option>
                    @endforeach
                    <option value="final">Consumidor Final (sin identificar)</option>
                </select>
            </div>
            <div class="col-12 col-md-5">
                <button wire:click="confirmarCompradorRapido" class="btn btn-outline-primary btn-touch w-100">
                    <i class="bi bi-lightning-charge me-1"></i> Usar en venta rápida
                </button>
            </div>
        </div>
        @if ($compradorConfirmado !== '')
            <div class="alert alert-light border small mb-0 mt-2 py-2">
                <i class="bi bi-check-circle text-success me-1"></i> Comprador actual: <strong>{{ $compradorConfirmado }}</strong>
            </div>
        @endif
    </div>

    <!-- Barra de Búsqueda y Filtros -->
    <div class="card p-3 mb-3 bg-white border-0 shadow-sm">
        <div class="row g-2">
            <div class="col-12 col-md-8">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" wire:model.live.debounce.300ms="buscar" class="form-control border-start-0" placeholder="Buscar por nombre o teléfono...">
                </div>
            </div>
            <div class="col-12 col-md-4">
                <select wire:model.live="filtroTipo" class="form-select" aria-label="Filtrar clientes por tipo">
                    <option value="">Todos los clientes</option>
                    <option value="Minorista">Minoristas</option>
                    <option value="Mayorista">Mayoristas</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Listado de Clientes -->
    <div class="table-responsive bg-white shadow-sm">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th scope="col" style="min-width: 200px;">Cliente / Razón Social</th>
                    <th scope="col">Tipo</th>
                    <th scope="col">Contacto</th>
                    <th scope="col">Datos fiscales</th>
                    <th scope="col">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($clientesFiltrados as $cliente)
                    <tr>
                        <td>
                            <div class="fw-bold text-dark">{{ $cliente['nombre'] }}</div>
                            <div class="small text-muted"><i class="bi bi-geo-alt me-1"></i> {{ $cliente['direccion'] }}</div>
                        </td>
                        <td>
                            @if ($cliente['tipo'] === 'Mayorista')
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">
                                    <i class="bi bi-shop me-1"></i> Mayorista
                                </span>
                            @else
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                    <i class="bi bi-person me-1"></i> Minorista
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="small"><i class="bi bi-telephone me-1 text-muted"></i> {{ $cliente['telefono'] }}</div>
                            <div class="small text-muted"><i class="bi bi-envelope me-1"></i> {{ $cliente['email'] }}</div>
                        </td>
                        <td>
                            @if ($cliente['cuit'])
                                <span class="badge bg-light text-dark border small">{{ $cliente['cuit'] }}</span>
                            @else
                                <span class="text-muted small">No requerido</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex flex-wrap gap-1">
                                <button wire:click="abrirFormulario({{ $cliente['id'] }})" class="btn btn-sm btn-outline-primary btn-touch" aria-label="Editar {{ $cliente['nombre'] }}">
                                    <i class="bi bi-pencil me-1"></i> Editar
                                </button>
                                <button wire:click="eliminar({{ $cliente['id'] }})" wire:confirm="¿Eliminar este cliente?" class="btn btn-sm btn-outline-danger btn-touch" aria-label="Eliminar {{ $cliente['nombre'] }}">
                                    <i class="bi bi-trash me-1"></i> Eliminar
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                            <h6 class="fw-bold">No se encontraron clientes</h6>
                            <p class="small mb-2">Probá con otro nombre o teléfono, o quitá el filtro de tipo.</p>
                            <button wire:click="$set('buscar', ''); $set('filtroTipo', '');" class="btn btn-sm btn-outline-secondary">
                                Limpiar Filtros
                            </button>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal de alta y edición -->
    @if ($mostrarFormulario)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title fw-bold">
                            <i class="bi bi-person-plus me-2"></i> {{ $clienteId ? 'Editar Cliente' : 'Nuevo Cliente' }}
                        </h5>
                        <button type="button" wire:click="cerrarFormulario" class="btn-close btn-close-white" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body p-3">
                        @if ($errors->any())
                            <div class="alert alert-danger py-2" role="alert">
                                <i class="bi bi-exclamation-triangle me-1"></i> {{ $errors->first() }}
                            </div>
                        @endif
                        <div class="row g-2 mb-3">
                            <div class="col-12 col-md-8">
                                <label class="form-label fw-semibold">Nombre / Razón Social *</label>
                                <input type="text" wire:model="formNombre" class="form-control" placeholder="Ej: Distribuidora Norte S.R.L.">
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-semibold">Tipo de cliente *</label>
                                <select wire:model.live="formTipo" class="form-select">
                                    <option value="Minorista">Minorista</option>
                                    <option value="Mayorista">Mayorista</option>
                                </select>
                            </div>
                        </div>

                        <!-- El campo de datos fiscales solo se muestra para mayoristas -->
                        @if ($formTipo === 'Mayorista')
                            <div class="row g-2 mb-3">
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold">CUIT * (obligatorio para mayoristas)</label>
                                    <input type="text" wire:model="formCuit" class="form-control" placeholder="Ej: 30-12345678-9">
                                    <div class="form-text">Requerido solo para clientes mayoristas. Se valida el formato, sin consultas a organismos externos.</div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-light border small py-2 mb-3">
                                <i class="bi bi-info-circle me-1"></i> Los datos fiscales se solicitan solo si seleccionás tipo Mayorista.
                            </div>
                        @endif

                        <div class="row g-2">
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-semibold">Teléfono</label>
                                <input type="text" wire:model="formTelefono" class="form-control" placeholder="Ej: 3765 412345">
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-semibold">Correo electrónico</label>
                                <input type="email" wire:model="formEmail" class="form-control" placeholder="ejemplo@correo.com">
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-semibold">Dirección</label>
                                <input type="text" wire:model="formDireccion" class="form-control" placeholder="Calle y número, localidad">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" wire:click="cerrarFormulario" class="btn btn-outline-secondary btn-touch">Cancelar</button>
                        <button type="button" wire:click="guardar" class="btn btn-primary btn-touch px-4 fw-bold">Guardar Cliente</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
