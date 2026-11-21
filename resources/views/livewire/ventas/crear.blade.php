<div>
    <!-- Encabezado de la Pantalla -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 pb-2 border-bottom">
        <div>
            <h1 class="h4 mb-0 fw-bold text-dark"><i class="bi bi-cart-plus text-primary me-2"></i> Nueva Venta (POS)</h1>
            <small class="text-muted">Mostrador y celular. Comprobante interno no fiscal, sin efectos en contabilidad.</small>
        </div>
        <div class="mt-2 mt-md-0 d-flex flex-wrap gap-2">
            <a href="{{ url('/ventas/postventa') }}" class="btn btn-outline-secondary btn-touch fw-semibold">
                <i class="bi bi-arrow-counterclockwise me-1"></i> Anulaciones y Devoluciones
            </a>
            @if ($ventaRegistrada)
                <button wire:click="nuevaVenta" class="btn btn-outline-success btn-touch fw-semibold">
                    <i class="bi bi-plus-circle me-1"></i> Nueva venta
                </button>
            @else
                <button wire:click="confirmarVenta" class="btn btn-success btn-touch fw-semibold">
                    <i class="bi bi-check2-circle me-1"></i> Registrar venta
                </button>
            @endif
            <button wire:click="verComprobante" class="btn btn-outline-primary btn-touch fw-semibold">
                <i class="bi bi-file-earmark-text me-1"></i> Ver Comprobante
            </button>
        </div>
    </div>

    @if ($mensajeEstado && str_starts_with($mensajeEstado, 'warning:'))
        <div class="alert alert-warning d-flex align-items-center py-2 mb-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <div>{{ \Illuminate\Support\Str::after($mensajeEstado, 'warning:') === 'ya_registrada' ? 'Esta venta ya fue registrada. Podés descargar su comprobante.' : (\Illuminate\Support\Str::after($mensajeEstado, 'warning:') === 'sin_lineas' ? 'Agregá al menos una variante antes de registrar la venta.' : \Illuminate\Support\Str::after($mensajeEstado, 'warning:')) }}</div>
        </div>
    @elseif (session()->has('warning'))
        <div class="alert alert-warning d-flex align-items-center py-2 mb-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <div>{{ session('warning') }}</div>
        </div>
    @endif

    @if ($ventaRegistrada)
        <div class="alert alert-success d-flex align-items-center py-2 mb-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <div>Venta <strong>{{ $comprobante['numero'] }}</strong> registrada. El stock fue descontado y el comprobante interno está disponible para descarga.</div>
        </div>
    @elseif ($mensajeEstado && str_starts_with($mensajeEstado, 'success:'))
        <div class="alert alert-success d-flex align-items-center py-2 mb-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <div>Venta {{ \Illuminate\Support\Str::after($mensajeEstado, 'success:') }} registrada correctamente. Se descontó el stock y podés descargar el comprobante interno.</div>
        </div>
    @elseif (session()->has('success'))
        <div class="alert alert-success d-flex align-items-center py-2 mb-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    <div class="row g-3">
        <!-- Comprador y modo de precios -->
        <div class="col-12 col-lg-5">
            <div class="card border-0 shadow-sm h-100 p-3">
                <h6 class="fw-bold text-secondary text-uppercase small mb-3"><i class="bi bi-person-badge me-1"></i> 1. Comprador</h6>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Cliente o comprador no identificado</label>
                    <select wire:model.live="compradorId" class="form-select" aria-label="Cliente o comprador no identificado">
                        <option value="final">Consumidor Final (sin identificar)</option>
                        @foreach ($clientes as $cliente)
                            <option value="{{ $cliente['id'] }}">{{ $cliente['nombre'] }} ({{ $cliente['tipo'] }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Advertencia informativa NO bloqueante: no pide contraseña ni impide continuar -->
                @if ($compradorId !== 'final' && $this->deudaActual > 0)
                    <div class="alert alert-info small py-2 px-2 mb-3" role="status">
                        <i class="bi bi-info-circle-fill me-1"></i>
                        El cliente tiene deuda pendiente de
                        <strong>${{ number_format($this->deudaActual, 0, ',', '.') }}</strong>.
                        <strong>Podés continuar con la venta normalmente.</strong>
                    </div>
                @endif

                <div>
                    <label class="form-label fw-semibold mb-1">Modo de precios</label>
                    <div class="btn-group w-100" role="group" aria-label="Modo de precios">
                        <button type="button" wire:click="$set('modoPrecio', 'minorista')" class="btn btn-touch {{ $modoPrecio === 'minorista' ? 'btn-primary' : 'btn-outline-primary' }}">
                            <i class="bi bi-person me-1"></i> Minorista
                        </button>
                        <button type="button" wire:click="$set('modoPrecio', 'mayorista')" class="btn btn-touch {{ $modoPrecio === 'mayorista' ? 'btn-primary' : 'btn-outline-primary' }}">
                            <i class="bi bi-people me-1"></i> Mayorista
                        </button>
                    </div>
                    <div class="form-text">Se aplica el precio correspondiente al agregar cada variante.</div>
                </div>
            </div>
        </div>

        <!-- Carga de variantes -->
        <div class="col-12 col-lg-7">
            <div class="card border-0 shadow-sm h-100 p-3">
                <h6 class="fw-bold text-secondary text-uppercase small mb-3"><i class="bi bi-plus-square me-1"></i> 2. Agregar variante</h6>
                <div class="input-group">
                    <select wire:model="productoSeleccionado" class="form-select" aria-label="Variante: modelo, talle y color">
                        <option value="">Seleccionar modelo, talle y color...</option>
                        @foreach ($catalogo as $producto)
                            <option value="{{ $producto['id'] }}">
                                {{ $producto['modelo'] }} — {{ $producto['talle'] }} / {{ $producto['color'] }} ({{ $producto['stock'] }} unid.)
                            </option>
                        @endforeach
                    </select>
                    <button wire:click="agregarLinea" class="btn btn-primary btn-touch px-3" type="button">
                        <i class="bi bi-plus-lg"></i> <span class="d-none d-sm-inline">Agregar</span>
                    </button>
                </div>
                <div class="form-text mt-2">Las variantes y existencias se consultan del catálogo actual.</div>
            </div>
        </div>
    </div>

    <!-- Advertencia por cambio manual de precio -->
    @if ($hayPrecioManual)
        <div class="alert alert-warning d-flex align-items-start mt-3 mb-0 py-2" role="alert">
            <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
            <div>
                <strong>Precio modificado manualmente.</strong>
                El cambio afecta solo a la operación en curso y no modifica la lista de precios.
            </div>
        </div>
    @endif

    <!-- Líneas de la venta -->
    <div class="card border-0 shadow-sm mt-3 p-3">
        <h6 class="fw-bold text-secondary text-uppercase small mb-3"><i class="bi bi-list-check me-1"></i> 3. Detalle de la venta</h6>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="min-width: 210px;">Variante</th>
                        <th style="min-width: 120px;">Precio cobrado ($)</th>
                        <th style="min-width: 80px;">Cant.</th>
                        <th style="min-width: 90px;">Dto. (%)</th>
                        <th class="text-end">Subtotal</th>
                        <th class="text-center" style="width: 50px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($lineas as $index => $linea)
                        @php
                            $bruto = (float) $linea['precio_unitario'] * (int) $linea['cantidad'];
                            $subtotalLinea = $bruto * (1 - ((float) $linea['descuento'] / 100));
                        @endphp
                        <tr>
                            <td>
                                <div class="fw-bold small text-dark">{{ $linea['modelo'] }}</div>
                                <span class="badge bg-light text-secondary border small">{{ $linea['talle'] }} / {{ $linea['color'] }}</span>
                            </td>
                            <td>
                                <input type="number" min="0" step="1" wire:model.live="lineas.{{ $index }}.precio_unitario" class="form-control form-control-sm">
                            </td>
                            <td>
                                <input type="number" min="1" step="1" wire:model.live="lineas.{{ $index }}.cantidad" class="form-control form-control-sm">
                            </td>
                            <td>
                                <input type="number" min="0" max="100" step="1" wire:model.live="lineas.{{ $index }}.descuento" class="form-control form-control-sm">
                            </td>
                            <td class="text-end fw-bold text-dark">${{ number_format($subtotalLinea, 0, ',', '.') }}</td>
                            <td class="text-center">
                                <button wire:click="quitarLinea({{ $index }})" class="btn btn-sm btn-outline-danger btn-touch" title="Quitar línea" aria-label="Quitar línea">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="bi bi-cart-x fs-1 d-block mb-2 text-secondary"></i>
                                <h6 class="fw-bold">Todavía no hay productos en la venta</h6>
                                <p class="small mb-0">Seleccioná una variante arriba para comenzar.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Totales dinámicos -->
        <div class="row justify-content-end mt-3">
            <div class="col-12 col-md-5 col-lg-4">
                <table class="table table-sm mb-0">
                    <tbody>
                        <tr>
                            <td class="text-muted">Subtotal</td>
                            <td class="text-end fw-semibold">${{ number_format($totales['subtotal'], 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Descuentos</td>
                            <td class="text-end fw-semibold text-danger">− ${{ number_format($totales['descuento'], 0, ',', '.') }}</td>
                        </tr>
                        <tr class="table-active">
                            <td class="fw-bold fs-5">Total</td>
                            <td class="text-end fw-bold fs-5 text-primary">${{ number_format($totales['total'], 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal de previsualización del comprobante interno -->
    @if ($mostrarComprobante)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.6);">
            <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-title fw-bold">
                            <i class="bi bi-file-earmark-text text-warning me-2"></i> Comprobante Interno (Vista Previa)
                        </h5>
                        <button type="button" wire:click="cerrarComprobante" class="btn-close btn-close-white" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body bg-secondary p-3">
                        @include('pdf.comprobante-interno', ['venta' => $comprobante])
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" wire:click="cerrarComprobante" class="btn btn-outline-secondary btn-touch">Cerrar</button>
                        @if ($ventaRegistrada)
                            <button type="button" wire:click="descargarComprobante" class="btn btn-primary btn-touch px-4 fw-bold">
                                <i class="bi bi-download me-1"></i> Descargar PDF
                            </button>
                        @else
                            <button type="button" wire:click="confirmarVenta" class="btn btn-success btn-touch px-4 fw-bold">
                                <i class="bi bi-check2-circle me-1"></i> Registrar venta
                            </button>
                            <button type="button" wire:click="descargarComprobante" class="btn btn-primary btn-touch px-4 fw-bold">
                                <i class="bi bi-download me-1"></i> Descargar PDF (Simulado)
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
