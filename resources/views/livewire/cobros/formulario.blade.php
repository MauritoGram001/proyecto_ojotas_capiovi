<div>
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <div>
            <h6 class="fw-bold mb-1"><i class="bi bi-cash-coin text-primary me-1"></i> Registrar cobro</h6>
            <small class="text-muted">Cliente: <strong>{{ $clienteNombre }}</strong></small>
        </div>
        <span class="badge bg-light text-secondary border">Cobro con imputación a boletas</span>
    </div>

    @error('pago')
        <div class="alert alert-danger py-2 small">{{ $message }}</div>
    @enderror
    @error('boletas')
        <div class="alert alert-danger py-2 small">{{ $message }}</div>
    @enderror

    <!-- Desglose de medios de pago -->
    <div class="card border-0 shadow-sm mb-3 p-3">
        <h6 class="fw-bold text-secondary text-uppercase small mb-3">1. Desglose de medios de pago</h6>
        <div class="row g-2">
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold">Efectivo ($)</label>
                <input type="number" min="0" step="0.01" wire:model.live="montoEfectivo" class="form-control">
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold">Transferencia ($)</label>
                <input type="number" min="0" step="0.01" wire:model.live="montoTransferencia" class="form-control">
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold">Cheque ($)</label>
                <input type="number" min="0" step="0.01" wire:model.live="montoCheque" class="form-control">
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">N° de cheque (referencia)</label>
                <input type="text" wire:model="numeroCheque" class="form-control" placeholder="Ej: 00012345">
                <small class="text-muted">Sin seguimiento bancario (registro básico).</small>
            </div>
            <div class="col-12 col-md-6 d-flex align-items-end">
                <div class="w-100 p-2 bg-white border rounded text-center">
                    <div class="small text-muted">Total a pagar</div>
                    <div class="fs-4 fw-bold text-primary">${{ number_format($this->totalPago(), 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Imputación a boletas -->
    <div class="card border-0 shadow-sm mb-3 p-3">
        <h6 class="fw-bold text-secondary text-uppercase small mb-3">2. Imputar a boletas pendientes</h6>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;"></th>
                        <th>Boleta</th>
                        <th>Fecha</th>
                        <th class="text-end">Saldo</th>
                        <th style="min-width: 140px;">Monto a imputar ($)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($boletas as $boleta)
                        @php($id = (string) $boleta->id)
                        <tr>
                            <td class="text-center">
                                <input class="form-check-input" type="checkbox" value="{{ $id }}" wire:model.live="boletasSeleccionadas">
                            </td>
                            <td class="fw-semibold">{{ $boleta->numero }}</td>
                            <td class="small text-muted">{{ $boleta->fecha ? $boleta->fecha->format('d/m/Y') : '' }}</td>
                            <td class="text-end">${{ number_format($boleta->saldo, 0, ',', '.') }}</td>
                            <td>
                                <input type="number" min="0" step="0.01" max="{{ $boleta->saldo }}"
                                    wire:model.live="montosImputados.{{ $id }}"
                                    class="form-control form-control-sm"
                                    @disabled(! in_array($id, array_map('strval', $boletasSeleccionadas), true))>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-3 text-muted">Este cliente no tiene boletas pendientes de cobro.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Resumen de imputación -->
    <div class="card border-0 shadow-sm mb-3 p-3 {{ abs($this->diferencia()) < 0.01 ? 'bg-success-subtle' : 'bg-warning-subtle' }}">
        <div class="row g-2 align-items-center">
            <div class="col-4 text-center">
                <div class="small text-muted">Total pagado</div>
                <div class="fw-bold">${{ number_format($this->totalPago(), 0, ',', '.') }}</div>
            </div>
            <div class="col-4 text-center">
                <div class="small text-muted">Total imputado</div>
                <div class="fw-bold">${{ number_format($this->totalImputado(), 0, ',', '.') }}</div>
            </div>
            <div class="col-4 text-center">
                <div class="small text-muted">Diferencia</div>
                <div class="fw-bold {{ abs($this->diferencia()) < 0.01 ? 'text-success' : 'text-danger' }}">${{ number_format($this->diferencia(), 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    <div class="d-grid d-md-flex justify-content-md-end gap-2">
        <button type="button" wire:click="registrarCobro" class="btn btn-success btn-touch fw-bold">
            <i class="bi bi-check-circle me-1"></i> Confirmar cobro
        </button>
    </div>
</div>
