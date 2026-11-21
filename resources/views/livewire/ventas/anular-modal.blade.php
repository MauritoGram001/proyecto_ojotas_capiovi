@if ($mostrarModalAnular && $ventaAnular)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.6);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> Confirmar anulación
                    </h5>
                    <button type="button" wire:click="cerrarModalAnular" class="btn-close btn-close-white" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="alert alert-warning small py-2 px-3 mb-3" role="alert">
                        <i class="bi bi-info-circle me-1"></i>
                        Estás por anular la venta <strong>{{ $ventaAnular['numero'] }}</strong> de
                        <strong>{{ $ventaAnular['cliente'] }}</strong> por
                        <strong>${{ number_format($ventaAnular['total'], 0, ',', '.') }}</strong>.
                        La anulación quedará vinculada a esta venta original.
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Justificación de la anulación *</label>
                        <textarea wire:model="justificacionAnulacion" class="form-control" rows="3"
                            placeholder="Ej: Error de carga en la cantidad solicitada"></textarea>
                        <div class="form-text">La justificación es obligatoria para poder confirmar.</div>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" wire:model="confirmacionAnulacion" id="confirmacionAnulacion">
                        <label class="form-check-label small fw-semibold" for="confirmacionAnulacion">
                            Confirmo explícitamente que deseo anular esta venta
                        </label>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" wire:click="cerrarModalAnular" class="btn btn-outline-secondary btn-touch">Cancelar</button>
                    <button type="button" wire:click="confirmarAnulacion" class="btn btn-danger btn-touch px-4 fw-bold"
                        @if (trim($justificacionAnulacion) === '' || ! $confirmacionAnulacion) disabled @endif>
                        <i class="bi bi-x-octagon me-1"></i> Confirmar Anulación (Simulada)
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif
