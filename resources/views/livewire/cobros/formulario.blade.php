<div>
    @php
        $dinero = fn (float $v) => \App\Livewire\Ventas\Crear::dinero($v);
        $fecha = fn (string $iso) => \App\Services\CuentasEjemplo::fecha($iso);
    @endphp

    <!-- Encabezado de la Pantalla -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 pb-2 border-bottom">
        <div>
            <h1 class="h4 mb-0 fw-bold text-dark"><i class="bi bi-wallet2 text-primary me-2"></i> Registrar Cobro</h1>
            <small class="text-muted">Cobro combinado imputado a una o varias boletas. Datos ficticios: no guarda nada.</small>
        </div>
        <div class="mt-2 mt-md-0">
            <a href="{{ route('cuentas.index') }}" class="btn btn-outline-secondary btn-touch">
                <i class="bi bi-arrow-left me-1"></i> Cuentas corrientes
            </a>
        </div>
    </div>

    @if ($mensajeExito !== '')
        <div class="alert alert-success d-flex align-items-center mb-3" role="alert">
            <i class="bi bi-check-circle-fill fs-5 me-2"></i>
            <div>{{ $mensajeExito }}</div>
        </div>
    @endif

    <div class="row g-3">
        <div class="col-12 col-lg-7">

            <!-- Cliente -->
            <div class="card p-3 mb-3 bg-white border-0 shadow-sm">
                <label for="clienteId" class="form-label fw-semibold mb-1"><i class="bi bi-person me-1 text-primary"></i> Cliente que paga</label>
                <select id="clienteId" wire:model.live="clienteId" class="form-select @error('clienteId') is-invalid @enderror">
                    <option value="">Elegí un cliente con saldo pendiente...</option>
                    @foreach ($clientesConDeuda as $id => $c)
                        <option value="{{ $id }}">{{ $c['nombre'] }} — debe {{ $dinero($c['saldo']) }}</option>
                    @endforeach
                </select>
                @error('clienteId') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <!-- Boletas a imputar -->
            <div class="card p-3 mb-3 bg-white border-0 shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h2 class="h6 fw-bold mb-0"><i class="bi bi-receipt me-1 text-primary"></i> Boletas a cobrar</h2>
                    @if (count($pendientes) > 0)
                        <div class="btn-group btn-group-sm">
                            <button type="button" wire:click="seleccionarTodas" class="btn btn-outline-primary">Todas</button>
                            <button type="button" wire:click="limpiarSeleccion" class="btn btn-outline-secondary">Ninguna</button>
                        </div>
                    @endif
                </div>

                @error('boletasSeleccionadas') <div class="alert alert-danger py-2 small">{{ $message }}</div> @enderror

                @forelse ($pendientes as $b)
                    <label class="d-flex align-items-center border rounded p-2 mb-1" for="boleta-{{ $b['id'] }}" wire:key="boleta-{{ $b['id'] }}" style="cursor:pointer;">
                        <input class="form-check-input me-3 mt-0" style="width:1.4rem;height:1.4rem;" type="checkbox"
                               id="boleta-{{ $b['id'] }}" value="{{ $b['id'] }}" wire:model.live="boletasSeleccionadas">
                        <div class="flex-grow-1">
                            <strong>{{ $b['id'] }}</strong>
                            <span class="small text-muted ms-1">{{ $fecha($b['fecha']) }}</span>
                            <div class="small text-muted">Total {{ $dinero($b['total']) }} · Pagado {{ $dinero($b['pagado']) }}</div>
                        </div>
                        <div class="fw-bold text-danger">{{ $dinero($b['saldo']) }}</div>
                    </label>
                @empty
                    <div class="text-center text-muted py-3">
                        <i class="bi bi-person-check fs-1 d-block mb-2 text-secondary"></i>
                        <p class="small mb-0">Elegí un cliente para ver sus boletas pendientes.</p>
                    </div>
                @endforelse
            </div>

            <!-- Medios de pago -->
            <div class="card p-3 mb-3 bg-white border-0 shadow-sm">
                <h2 class="h6 fw-bold mb-3"><i class="bi bi-cash-coin me-1 text-primary"></i> Cómo paga</h2>
                @error('importes') <div class="alert alert-danger py-2 small">{{ $message }}</div> @enderror

                <div class="row g-2">
                    <div class="col-12 col-sm-4">
                        <label for="efectivo" class="form-label fw-semibold small mb-1">Efectivo</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input id="efectivo" type="number" min="0" step="any" inputmode="decimal" wire:model.live.debounce.400ms="efectivo" class="form-control @error('efectivo') is-invalid @enderror" placeholder="0">
                        </div>
                        @error('efectivo') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12 col-sm-4">
                        <label for="transferencia" class="form-label fw-semibold small mb-1">Transferencia</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input id="transferencia" type="number" min="0" step="any" inputmode="decimal" wire:model.live.debounce.400ms="transferencia" class="form-control @error('transferencia') is-invalid @enderror" placeholder="0">
                        </div>
                        @error('transferencia') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12 col-sm-4">
                        <label for="cheque" class="form-label fw-semibold small mb-1">Cheque</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input id="cheque" type="number" min="0" step="any" inputmode="decimal" wire:model.live.debounce.400ms="cheque" class="form-control @error('cheque') is-invalid @enderror" placeholder="0">
                        </div>
                        @error('cheque') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="small text-muted mt-2">Del cheque solo se registra el medio y el importe.</div>
            </div>
        </div>

        <!-- Resumen e imputación -->
        <div class="col-12 col-lg-5">
            <div class="card p-3 mb-3 bg-white border-0 shadow-sm">
                <h2 class="h6 fw-bold mb-3"><i class="bi bi-calculator me-1 text-primary"></i> Resumen del cobro</h2>

                @foreach ($imputacion['por_medio'] as $medio => $importe)
                    @if ($importe > 0)
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted">{{ $medio }}</span>
                            <span>{{ $dinero($importe) }}</span>
                        </div>
                    @endif
                @endforeach

                <hr class="my-2">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="fs-5 fw-bold">Total cobrado</span>
                    <span class="fs-3 fw-bold text-primary">{{ $dinero($imputacion['total']) }}</span>
                </div>

                <h3 class="h6 fw-bold text-secondary">Imputación a boletas</h3>
                @forelse ($imputacion['filas'] as $f)
                    <div class="d-flex justify-content-between border rounded p-2 mb-1 small">
                        <div>
                            <strong>{{ $f['id'] }}</strong>
                            <span class="text-muted">saldo {{ $dinero($f['saldo']) }}</span>
                        </div>
                        <div class="text-end">
                            <span class="text-success fw-semibold">− {{ $dinero($f['aplicado']) }}</span>
                            <div class="text-muted">queda {{ $dinero($f['queda']) }}</div>
                        </div>
                    </div>
                @empty
                    <div class="small text-muted mb-2">Elegí una o más boletas para ver cómo se reparte el cobro.</div>
                @endforelse

                @if (count($imputacion['filas']) > 0 && $imputacion['sobrante'] > 0)
                    <div class="alert alert-info py-2 small mt-2 mb-0">
                        <i class="bi bi-info-circle me-1"></i>
                        Sobran {{ $dinero($imputacion['sobrante']) }} sin imputar: el cobro supera el saldo de las boletas elegidas.
                    </div>
                @endif

                <button type="button" wire:click="confirmarCobroSimulado" class="btn btn-success btn-touch fw-bold w-100 mt-3">
                    <i class="bi bi-check2-circle me-1"></i> Confirmar cobro (simulado)
                </button>
            </div>
        </div>
    </div>
</div>
