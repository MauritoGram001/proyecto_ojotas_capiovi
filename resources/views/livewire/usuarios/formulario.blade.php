@if ($mostrarFormulario)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-person-{{ $usuarioEditando ? 'badge' : 'plus' }} me-2"></i>
                        {{ $usuarioEditando ? 'Editar Usuario' : 'Nuevo Usuario' }}
                    </h5>
                    <button type="button" wire:click="cerrarFormulario" class="btn-close btn-close-white" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nombre y apellido *</label>
                        <input type="text" wire:model="formNombre" class="form-control" placeholder="Ej: Ana Paiva">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Correo electrónico *</label>
                        <input type="email" wire:model="formEmail" class="form-control" placeholder="ej: ana@ojotascapiovi.com.ar">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Rol *</label>
                        <select wire:model="formRol" class="form-select">
                            @foreach (array_keys($roles) as $rolOpcion)
                                <option value="{{ $rolOpcion }}">{{ $rolOpcion }}</option>
                            @endforeach
                        </select>
                        <div class="form-text mt-2">
                            Los permisos se muestran en la vista previa. Todos los roles pueden aplicar descuentos o cambiar precios en ventas.
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" wire:click="cerrarFormulario" class="btn btn-outline-secondary btn-touch">Cancelar</button>
                    <button type="button" wire:click="guardarUsuario" class="btn btn-primary btn-touch px-4 fw-bold">
                        <i class="bi bi-check-circle me-1"></i> {{ $usuarioEditando ? 'Guardar Cambios' : 'Crear Usuario' }} (Simulado)
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif
