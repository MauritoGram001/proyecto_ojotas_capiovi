<div>
    <!-- Encabezado de la Pantalla -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 pb-2 border-bottom">
        <div>
            <h1 class="h4 mb-0 fw-bold text-dark"><i class="bi bi-building text-primary me-2"></i> Proveedores & Compras</h1>
            <small class="text-muted">Altas, ediciones y bajas de proveedores con datos de contacto para compras y deudas.</small>
        </div>
        <div class="mt-2 mt-md-0 d-flex flex-wrap gap-2">
            <a href="{{ url('/proveedores/pedidos') }}" class="btn btn-outline-primary btn-touch fw-semibold">
                <i class="bi bi-truck me-1"></i> Pedidos y cambios
            </a>
            <button wire:click="abrirFormulario" class="btn btn-primary btn-touch fw-semibold">
                <i class="bi bi-plus-lg me-1"></i> Nuevo Proveedor
            </button>
        </div>
    </div>

    <!-- Barra de Búsqueda y Filtros -->
    <div class="card p-3 mb-3 bg-white border-0 shadow-sm">
        <div class="row g-2">
            <div class="col-12 col-md-8">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" wire:model.live.debounce.300ms="buscar" class="form-control border-start-0" placeholder="Buscar por razón social o teléfono...">
                </div>
            </div>
            <div class="col-12 col-md-4">
                <select wire:model.live="filtroRubro" class="form-select" aria-label="Filtrar proveedores por rubro">
                    <option value="">Todos los rubros</option>
                    <option value="Calzado">Calzado</option>
                    <option value="Textil">Textil</option>
                    <option value="Insumos">Insumos</option>
                    <option value="Logística">Logística</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Listado de Proveedores -->
    <div class="table-responsive bg-white shadow-sm">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th scope="col" style="min-width: 210px;">Razón Social</th>
                    <th scope="col">Rubro</th>
                    <th scope="col">Contacto</th>
                    <th scope="col">Teléfono / Correo</th>
                    <th scope="col">Domicilio</th>
                    <th scope="col">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($proveedoresFiltrados as $proveedor)
                    <tr>
                        <td>
                            <div class="fw-bold text-dark">{{ $proveedor['razon_social'] }}</div>
                            <span class="badge bg-light text-secondary border small">{{ $proveedor['cuit'] }}</span>
                        </td>
                        <td>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">{{ $proveedor['rubro'] }}</span>
                        </td>
                        <td>
                            <div class="small fw-semibold">{{ $proveedor['contacto'] }}</div>
                        </td>
                        <td>
                            <div class="small"><i class="bi bi-telephone me-1 text-muted"></i> {{ $proveedor['telefono'] }}</div>
                            <div class="small text-muted"><i class="bi bi-envelope me-1"></i> {{ $proveedor['email'] }}</div>
                        </td>
                        <td class="small text-muted" style="min-width: 180px;">{{ $proveedor['direccion'] }}</td>
                        <td>
                            <div class="d-flex flex-wrap gap-1">
                                <button wire:click="abrirFormulario({{ $proveedor['id'] }})" class="btn btn-sm btn-outline-primary btn-touch" aria-label="Editar {{ $proveedor['razon_social'] }}">
                                    <i class="bi bi-pencil me-1"></i> Editar
                                </button>
                                <button wire:click="eliminar({{ $proveedor['id'] }})" wire:confirm="¿Eliminar este proveedor?" class="btn btn-sm btn-outline-danger btn-touch" aria-label="Eliminar {{ $proveedor['razon_social'] }}">
                                    <i class="bi bi-trash me-1"></i> Eliminar
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                            <h6 class="fw-bold">No se encontraron proveedores</h6>
                            <p class="small mb-2">Probá con otro nombre o teléfono, o quitá el filtro de rubro.</p>
                            <button wire:click="$set('buscar', ''); $set('filtroRubro', '');" class="btn btn-sm btn-outline-secondary">
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
                            <i class="bi bi-building-add me-2"></i> {{ $proveedorId ? 'Editar Proveedor' : 'Nuevo Proveedor' }}
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
                                <label class="form-label fw-semibold">Razón Social *</label>
                                <input type="text" wire:model="formRazonSocial" class="form-control" placeholder="Ej: Fábrica de Calzados Misiones S.R.L.">
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-semibold">Rubro</label>
                                <select wire:model="formRubro" class="form-select">
                                    <option value="Calzado">Calzado</option>
                                    <option value="Textil">Textil</option>
                                    <option value="Insumos">Insumos</option>
                                    <option value="Logística">Logística</option>
                                </select>
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">CUIT</label>
                                <input type="text" wire:model="formCuit" class="form-control" placeholder="Ej: 30-12345678-9">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Persona de contacto</label>
                                <input type="text" wire:model="formContacto" class="form-control" placeholder="Nombre y apellido">
                            </div>
                        </div>
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
                        <button type="button" wire:click="guardar" class="btn btn-primary btn-touch px-4 fw-bold">Guardar Proveedor</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
