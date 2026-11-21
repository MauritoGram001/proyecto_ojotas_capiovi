<div>
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <div>
            <h6 class="fw-bold mb-1"><i class="bi bi-cash-coin text-primary me-1"></i> Formulario de cobro (simulación)</h6>
            <small class="text-muted">Cliente: <strong>{{ $clienteNombre }}</strong></small>
        </div>
        <span class="badge bg-light text-secondary border">Sin persistencia real</span>
    </div>

    <!-- Desglose de medios de pago -->
    <div class="card border-0 shadow-sm mb-3 p-3">
        <h6 class="fw-bold text-secondary text-uppercase small mb-3">1. Desglose de medios de pago</h6>
        <div class="row g-2">
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold">Efectivo ($)</label>
                <input type="number" min="0" step="1" wire:model.live="montoEfectivo" class="form-control">
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold">Transferencia ($)</label>
                <input type="number" min="0" step="1" wire:model.live="montoTransferencia" class="form-control">
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold">Cheque ($)</label>
                <input type="number" min="0" step="1" wire:model.live="montoCheque" class="form-control">
            </div>
        </div>
        <div class="row g-2 mt-1">
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold small">Nº de cheque (básico)</label>
                <input type="text" wire:model="numeroCheque" class="form-control" placeholder="Ej: 00012345">
                <div class="form-text">No se gestiona cartera bancaria ni cheques rechazados.</div>
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
                        <th class="text-center" style="width: 60px;">Aplicar</th>
                        <th>Boleta</th>
                        <th>Fecha</th>
                        <th class="text-end">Saldo</th>
                        <th style="min-width: 130px;">Monto a imputar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($boletas as $boleta)
                        <tr>
                            <td class="text-center">
                                <input class="form-check-input" type="checkbox" wire:model="boletasSeleccionadas" value="{{ $boleta['id'] }}" aria-label="Seleccionar boleta {{ $boleta['numero'] }}">
                            </td>
                            <td class="fw-semibold">{{ $boleta['numero'] }}</td>
                            <td class="small text-muted">{{ $boleta['fecha'] }}</td>
                            <td class="text-end">${{ number_format($boleta['saldo'], 0, ',', '.') }}</td>
                            <td>
                                <input type="number" min="0" step="1" wire:model.live="montosImputados.{{ $boleta['id'] }}" class="form-control form-control-sm">
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-3 text-muted">El cliente no tiene boletas pendientes.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Resumen -->
    <div class="card border-0 shadow-sm p-3">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <div>
                <div class="small text-muted">Total del pago: <strong class="text-dark fs-5">${{ number_format($totalPago, 0, ',', '.') }}</strong></div>
                <div class="small text-muted mt-1">Total imputado: <strong class="text-dark fs-5">${{ number_format($totalImputado, 0, ',', '.') }}</strong></div>
            </div>
            <div class="mt-2 mt-md-0">
                <button wire:click="registrarCobro" class="btn btn-success btn-touch fw-bold px-4">
                    <i class="bi bi-check-circle me-1"></i> Registrar Cobro Simulado
                </button>
            </div>
        </div>
        @php
            $diferencia = $totalPago - $totalImputado;
        @endphp
        @if (abs($diferencia) > 0.01)
            <div class="alert alert-warning small mt-3 mb-0 py-2">
                <i class="bi bi-exclamation-triangle me-1"></i> Diferencia a imputar: <strong>${{ number_format(abs($diferencia), 0, ',', '.') }}</strong>. Ajustá los montos para continuar.
            </div>
        @endif
    </div>
</div>
