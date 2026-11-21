<div>
    <!-- Encabezado -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 pb-2 border-bottom">
        <div>
            <h1 class="h4 mb-0 fw-bold text-dark"><i class="bi bi-shield-lock text-primary me-2"></i> Usuarios y Permisos</h1>
            <small class="text-muted">Administración de usuarios, asignación de roles y vista previa de permisos. Datos ficticios de la fase visual.</small>
        </div>
        <div class="mt-2 mt-md-0">
            <button wire:click="abrirFormulario" class="btn btn-primary btn-touch fw-semibold">
                <i class="bi bi-person-plus me-1"></i> Nuevo Usuario
            </button>
        </div>
    </div>

    <!-- Invariante: descuento disponible para todos los roles -->
    <div class="alert alert-info border d-flex align-items-start mb-3" role="status">
        <i class="bi bi-info-circle-fill fs-5 me-2"></i>
        <div>
            <strong>{{ $permisoCompartido['titulo'] }}</strong>
            <div class="small">{{ $permisoCompartido['detalle'] }}</div>
        </div>
    </div>

    <div class="row g-3">
        <!-- Listado de usuarios -->
        <div class="col-12 col-xl-7">
            <div class="card border-0 shadow-sm p-3 h-100">
                <h6 class="fw-bold text-secondary text-uppercase small mb-3"><i class="bi bi-people me-1"></i> Usuarios del sistema</h6>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="min-width: 180px;">Usuario</th>
                                <th>Correo</th>
                                <th style="min-width: 160px;">Rol</th>
                                <th class="text-center">Estado</th>
                                <th class="text-center" style="min-width: 120px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($usuarios as $index => $usuario)
                                <tr>
                                    <td>
                                        <div class="fw-bold small text-dark">{{ $usuario['nombre'] }}</div>
                                        <small class="text-muted">Último acceso: {{ $usuario['ultimoAcceso'] }}</small>
                                    </td>
                                    <td class="small text-muted">{{ $usuario['email'] }}</td>
                                    <td>
                                        <select class="form-select form-select-sm"
                                            aria-label="Rol de {{ $usuario['nombre'] }}"
                                            wire:change="cambiarRolUsuario({{ $usuario['id'] }}, $event.target.value)">
                                            @foreach (array_keys($roles) as $rolOpcion)
                                                <option value="{{ $rolOpcion }}" {{ $usuario['rol'] === $rolOpcion ? 'selected' : '' }}>{{ $rolOpcion }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="text-center">
                                        <button wire:click="toggleEstado({{ $usuario['id'] }})" class="btn btn-sm btn-link p-0 text-decoration-none"
                                            title="Cambiar estado (simulación)">
                                            <span class="badge {{ $usuario['estado'] === 'Activo' ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }} border px-2 py-1">
                                                <i class="bi bi-{{ $usuario['estado'] === 'Activo' ? 'check-circle' : 'pause-circle' }} me-1"></i>
                                                {{ $usuario['estado'] }}
                                            </span>
                                        </button>
                                    </td>
                                    <td class="text-center">
                                        <button wire:click="abrirFormulario({{ $usuario['id'] }})" class="btn btn-sm btn-outline-primary btn-touch">
                                            <i class="bi bi-pencil me-1"></i> Editar
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Previsualización de permisos por rol -->
        <div class="col-12 col-xl-5">
            <div class="card border-0 shadow-sm p-3 h-100">
                <h6 class="fw-bold text-secondary text-uppercase small mb-3"><i class="bi bi-eye me-1"></i> Vista previa de permisos</h6>
                <div class="mb-3">
                    <label class="form-label fw-semibold small text-muted text-uppercase mb-1">Rol simulado</label>
                    <select wire:model.live="rolVistaPrevia" class="form-select" aria-label="Rol para vista previa de permisos">
                        @foreach (array_keys($roles) as $rolOpcion)
                            <option value="{{ $rolOpcion }}">{{ $rolOpcion }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="border rounded p-3 bg-light mb-3">
                    <div class="fw-bold text-dark small mb-1">{{ $rolVistaPrevia }}</div>
                    <div class="text-muted small">{{ $permisosRol['descripcion'] }}</div>
                </div>

                <div class="small fw-bold text-secondary text-uppercase mb-2">Módulos accesibles</div>
                <ul class="list-unstyled small mb-3">
                    @foreach ($permisosRol['modulos'] as $moduloPermitido)
                        <li class="d-flex align-items-start mb-1">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            <span>{{ $moduloPermitido }}</span>
                        </li>
                    @endforeach
                </ul>

                @if (count($permisosRol['restricciones']) > 0)
                    <div class="small fw-bold text-secondary text-uppercase mb-2">Restricciones</div>
                    <ul class="list-unstyled small mb-3">
                        @foreach ($permisosRol['restricciones'] as $restriccion)
                            <li class="d-flex align-items-start mb-1">
                                <i class="bi bi-lock-fill text-secondary me-2"></i>
                                <span>{{ $restriccion }}</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="alert alert-success small py-2 px-3 mb-3">
                        <i class="bi bi-unlock-fill me-1"></i> Sin restricciones de módulos.
                    </div>
                @endif

                <div class="border-top pt-3 mt-2">
                    <div class="small fw-bold text-secondary text-uppercase mb-2">Cómo se ve el menú para este rol</div>
                    <div class="d-flex flex-wrap gap-1">
                        @foreach ($navAcceso as $modulo => $habilitado)
                            <span class="badge {{ $habilitado ? 'bg-primary-subtle text-primary' : 'bg-secondary-subtle text-secondary' }} border">
                                <i class="bi bi-{{ $habilitado ? 'check' : 'lock' }} me-1"></i>{{ $modulo }}
                            </span>
                        @endforeach
                    </div>
                    <div class="alert alert-warning small py-2 px-3 mt-3 mb-0">
                        <i class="bi bi-cash-coin me-1"></i>
                        <strong>{{ $permisoCompartido['titulo'] }}:</strong> habilitado para este rol.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal del formulario de usuario -->
    @include('livewire.usuarios.formulario')
</div>
