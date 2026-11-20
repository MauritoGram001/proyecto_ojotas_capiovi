<div>
    @php
        $dinero = fn (float $v) => \App\Livewire\Ventas\Crear::dinero($v);
        $fecha = fn (string $iso) => \App\Services\CuentasEjemplo::fecha($iso);
        $esClientes = $vista === \App\Livewire\CuentasCorrientes\Index::VISTA_CLIENTES;
    @endphp

    <!-- Encabezado de la Pantalla -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 pb-2 border-bottom">
        <div>
            <h1 class="h4 mb-0 fw-bold text-dark"><i class="bi bi-cash-stack text-primary me-2"></i> Cuentas Corrientes</h1>
            <small class="text-muted">Saldos y movimientos de clientes y deudas con proveedores. Datos ficticios de ejemplo.</small>
        </div>
        <div class="mt-2 mt-md-0">
            <a href="{{ route('cobros.crear') }}" class="btn btn-primary btn-touch fw-semibold">
                <i class="bi bi-wallet2 me-1"></i> Registrar cobro
            </a>
        </div>
    </div>

    <!-- Totales -->
    <div class="row g-2 mb-3">
        <div class="col-6">
            <div class="card p-3 bg-white border-0 shadow-sm h-100">
                <small class="text-muted">A cobrar a clientes</small>
                <div class="fs-4 fw-bold text-danger">{{ $dinero($totalPorCobrar) }}</div>
            </div>
        </div>
        <div class="col-6">
            <div class="card p-3 bg-white border-0 shadow-sm h-100">
                <small class="text-muted">A pagar a proveedores</small>
                <div class="fs-4 fw-bold text-dark">{{ $dinero($totalPorPagar) }}</div>
            </div>
        </div>
    </div>

    <!-- Pestañas: clientes / proveedores -->
    <div class="btn-group w-100 mb-3" role="group" aria-label="Tipo de cuenta">
        <button type="button" wire:click="cambiarVista('clientes')" class="btn btn-touch {{ $esClientes ? 'btn-primary' : 'btn-outline-primary' }}">
            <i class="bi bi-people me-1"></i> Clientes
        </button>
        <button type="button" wire:click="cambiarVista('proveedores')" class="btn btn-touch {{ $esClientes ? 'btn-outline-primary' : 'btn-primary' }}">
            <i class="bi bi-building me-1"></i> Proveedores
        </button>
    </div>

    <!-- Búsqueda -->
    <div class="card p-3 mb-3 bg-white border-0 shadow-sm">
        <div class="row g-2 align-items-center">
            <div class="col-12 col-md-8">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" wire:model.live.debounce.300ms="buscar" class="form-control border-start-0"
                           placeholder="{{ $esClientes ? 'Buscar cliente...' : 'Buscar proveedor...' }}"
                           aria-label="Buscar por nombre">
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="soloConDeuda" wire:model.live="soloConDeuda">
                    <label class="form-check-label" for="soloConDeuda">Solo con saldo pendiente</label>
                </div>
            </div>
        </div>
    </div>

    <!-- Listado de cuentas -->
    <div class="bg-white shadow-sm rounded">
        @forelse ($cuentas as $id => $c)
            @php $abierta = $cuentaAbierta === $id; @endphp
            <div class="border-bottom p-3" wire:key="cuenta-{{ $vista }}-{{ $id }}">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <div class="fw-bold text-dark">{{ $c['nombre'] }}</div>
                        @if ($esClientes)
                            <span class="badge {{ $c['tipo'] === 'Mayorista' ? 'bg-primary-subtle text-primary border border-primary-subtle' : 'bg-light text-secondary border' }}">{{ $c['tipo'] }}</span>
                        @endif
                        <span class="small text-muted ms-1">{{ count($c['pendientes']) }} {{ $esClientes ? 'boleta(s)' : 'compra(s)' }} pendiente(s)</span>
                    </div>
                    <div class="text-end">
                        @if ($c['saldo'] > 0)
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle">{{ $esClientes ? 'Debe' : 'Adeudamos' }}</span>
                            <div class="fs-5 fw-bold text-danger">{{ $dinero($c['saldo']) }}</div>
                        @else
                            <span class="badge bg-success-subtle text-success border border-success-subtle">Al día</span>
                            <div class="fs-5 fw-bold text-success">{{ $dinero(0) }}</div>
                        @endif
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2 mt-2">
                    @if ($esClientes)
                        <button type="button" wire:click="alternarCuenta({{ $id }})" class="btn btn-sm btn-outline-primary btn-touch">
                            <i class="bi bi-list-ul me-1"></i> {{ $abierta ? 'Ocultar movimientos' : 'Ver movimientos' }}
                        </button>
                        @if ($c['saldo'] > 0)
                            <a href="{{ route('cobros.crear', ['cliente' => $id]) }}" class="btn btn-sm btn-primary btn-touch">
                                <i class="bi bi-wallet2 me-1"></i> Registrar cobro
                            </a>
                        @endif
                    @else
                        <button type="button" wire:click="alternarCuenta({{ $id }})" class="btn btn-sm btn-outline-primary btn-touch">
                            <i class="bi bi-list-ul me-1"></i> {{ $abierta ? 'Ocultar compras' : 'Ver compras pendientes' }}
                        </button>
                    @endif
                </div>

                @if ($abierta)
                    <div class="mt-3">
                        @if ($esClientes)
                            <h2 class="h6 fw-bold text-secondary">Movimientos de la cuenta</h2>
                            <div class="table-responsive border">
                                <table class="table table-sm align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Detalle</th>
                                            <th class="text-end">Debe</th>
                                            <th class="text-end">Haber</th>
                                            <th class="text-end">Saldo</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($movimientos as $m)
                                            <tr>
                                                <td>{{ $fecha($m['fecha']) }}</td>
                                                <td>{{ $m['detalle'] }}</td>
                                                <td class="text-end">{{ $m['debe'] > 0 ? $dinero($m['debe']) : '—' }}</td>
                                                <td class="text-end text-success">{{ $m['haber'] > 0 ? $dinero($m['haber']) : '—' }}</td>
                                                <td class="text-end fw-semibold">{{ $dinero($m['saldo']) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <h2 class="h6 fw-bold text-secondary">Compras con saldo pendiente</h2>
                            @forelse ($c['pendientes'] as $p)
                                <div class="d-flex justify-content-between border rounded p-2 mb-1">
                                    <div>
                                        <strong>{{ $p['id'] }}</strong>
                                        <span class="small text-muted ms-1">{{ $fecha($p['fecha']) }}</span>
                                        <div class="small text-muted">Total {{ $dinero($p['total']) }} · Pagado {{ $dinero($p['pagado']) }}</div>
                                    </div>
                                    <div class="fw-bold text-danger align-self-center">{{ $dinero($p['saldo']) }}</div>
                                </div>
                            @empty
                                <div class="small text-muted">No hay compras pendientes de pago.</div>
                            @endforelse
                        @endif
                    </div>
                @endif
            </div>
        @empty
            <div class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                <h6 class="fw-bold">No se encontraron cuentas</h6>
                <p class="small mb-2">Probá con otro nombre o quitá el filtro de saldo pendiente.</p>
                <button type="button" wire:click="limpiarFiltros" class="btn btn-sm btn-outline-secondary">Limpiar filtros</button>
            </div>
        @endforelse
    </div>
</div>
