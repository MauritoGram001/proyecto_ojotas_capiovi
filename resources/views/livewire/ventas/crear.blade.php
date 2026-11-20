<div>
    @php
        $dinero = fn (float $v) => \App\Livewire\Ventas\Crear::dinero($v);
    @endphp

    <!-- Encabezado de la Pantalla -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 pb-2 border-bottom">
        <div>
            <h1 class="h4 mb-0 fw-bold text-dark"><i class="bi bi-cart-plus text-primary me-2"></i> Nueva Venta</h1>
            <small class="text-muted">Punto de venta de mostrador y celular. Datos ficticios: no guarda ventas ni descuenta stock.</small>
        </div>
    </div>

    @if ($mensajeExito !== '')
        <div class="alert alert-success d-flex align-items-center mb-3" role="alert">
            <i class="bi bi-check-circle-fill fs-5 me-2"></i>
            <div>{{ $mensajeExito }}</div>
        </div>
    @endif

    @if ($mensajeInfo !== '')
        <div class="alert alert-info d-flex align-items-center mb-3" role="alert">
            <i class="bi bi-info-circle-fill fs-5 me-2"></i>
            <div>{{ $mensajeInfo }}</div>
        </div>
    @endif

    <div class="row g-3">
        <!-- Columna izquierda: cliente, productos y líneas -->
        <div class="col-12 col-lg-7">

            <!-- Cliente y lista de precios -->
            <div class="card p-3 mb-3 bg-white border-0 shadow-sm">
                <div class="row g-3">
                    <div class="col-12 col-md-7">
                        <label for="clienteId" class="form-label fw-semibold mb-1">
                            <i class="bi bi-person me-1 text-primary"></i> Cliente
                        </label>
                        <select id="clienteId" wire:model.live="clienteId" class="form-select">
                            <option value="{{ \App\Livewire\Clientes\Index::CONSUMIDOR_FINAL }}">Consumidor final — sin identificar comprador</option>
                            @foreach ($clientes as $c)
                                <option value="{{ $c['id'] }}">{{ $c['nombre'] }} ({{ $c['tipo'] }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-5">
                        <span class="form-label fw-semibold mb-1 d-block">
                            <i class="bi bi-tag me-1 text-primary"></i> Lista de precios
                        </span>
                        <div class="btn-group w-100" role="group" aria-label="Lista de precios">
                            <input type="radio" class="btn-check" name="lista" id="listaMinorista" value="Minorista" wire:model.live="lista" autocomplete="off">
                            <label class="btn btn-outline-primary btn-touch" for="listaMinorista">Minorista</label>

                            <input type="radio" class="btn-check" name="lista" id="listaMayorista" value="Mayorista" wire:model.live="lista" autocomplete="off">
                            <label class="btn btn-outline-primary btn-touch" for="listaMayorista">Mayorista</label>
                        </div>
                    </div>
                </div>
                <div class="small text-muted mt-2">
                    @if ($clienteActual)
                        <i class="bi bi-person-check text-success me-1"></i> Venta a <strong>{{ $clienteActual['nombre'] }}</strong>.
                    @else
                        <i class="bi bi-person-dash text-secondary me-1"></i> La venta se registra sin asociar un cliente.
                    @endif
                    La lista se sugiere según el tipo de cliente; podés cambiarla.
                </div>
            </div>

            <!-- Aviso informativo de deuda previa: no bloquea la venta -->
            @if ($deudaCliente)
                <div class="alert alert-warning d-flex align-items-start mb-3" role="alert">
                    <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
                    <div>
                        <strong>Este cliente tiene {{ $deudaCliente['boletas'] }} boleta(s) pendiente(s) por {{ $dinero($deudaCliente['saldo']) }}.</strong>
                        <div class="small">Es solo un aviso: podés continuar con la venta.</div>
                        <a href="{{ route('cuentas.index') }}" class="small">Ver cuenta corriente</a>
                    </div>
                </div>
            @endif

            <!-- Agregar producto por variante -->
            <div class="card p-3 mb-3 bg-white border-0 shadow-sm">
                <label for="varianteSeleccionada" class="form-label fw-semibold mb-1">
                    <i class="bi bi-box-seam me-1 text-primary"></i> Agregar producto
                </label>
                <div class="row g-2 align-items-start">
                    <div class="col-12 col-md-7">
                        <select id="varianteSeleccionada" wire:model="varianteSeleccionada" class="form-select @error('varianteSeleccionada') is-invalid @enderror">
                            <option value="">Elegí modelo, talle y color...</option>
                            @foreach ($catalogo as $producto)
                                <optgroup label="{{ $producto['modelo'] }}">
                                    @foreach ($producto['variantes'] as $v)
                                        <option value="{{ $v['id'] }}">{{ $v['talle'] }} · {{ $v['color'] }} (stock {{ $v['stock'] }})</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        @error('varianteSeleccionada') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-5 col-md-2">
                        <input type="number" min="1" max="999" inputmode="numeric" wire:model="cantidadNueva" class="form-control @error('cantidadNueva') is-invalid @enderror" aria-label="Cantidad">
                        @error('cantidadNueva') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-7 col-md-3">
                        <button type="button" wire:click="agregarLinea" class="btn btn-primary btn-touch w-100 fw-semibold">
                            <i class="bi bi-plus-lg me-1"></i> Agregar
                        </button>
                    </div>
                </div>
            </div>

            <!-- Líneas de la venta -->
            <div class="card p-3 mb-3 bg-white border-0 shadow-sm">
                <h2 class="h6 fw-bold mb-3"><i class="bi bi-list-check me-1 text-primary"></i> Productos de la venta ({{ count($resumen['lineas']) }})</h2>

                @if ($resumen['hay_precio_manual'])
                    <div class="alert alert-warning py-2 small d-flex align-items-start mb-3" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2 mt-1"></i>
                        <div><strong>Precio cambiado a mano.</strong> Vale solo para esta venta y no modifica el precio del catálogo.</div>
                    </div>
                @endif

                @forelse ($resumen['lineas'] as $l)
                    <div class="border rounded p-2 mb-2" wire:key="linea-{{ $l['variante_id'] }}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="fw-bold text-dark">{{ $l['modelo'] }}</div>
                                <div class="small text-muted">Talle {{ $l['talle'] }} · {{ $l['color'] }}</div>
                            </div>
                            <button type="button" wire:click="quitarLinea({{ $l['indice'] }})" class="btn btn-sm btn-outline-danger btn-touch px-3" aria-label="Quitar {{ $l['modelo'] }} talle {{ $l['talle'] }} de la venta">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>

                        <div class="d-flex flex-wrap justify-content-between align-items-center mt-2 gap-2">
                            <div class="btn-group" role="group" aria-label="Cantidad">
                                <button type="button" wire:click="cambiarCantidad({{ $l['indice'] }}, -1)" class="btn btn-outline-secondary btn-touch px-3" aria-label="Restar una unidad">
                                    <i class="bi bi-dash-lg"></i>
                                </button>
                                <span class="btn btn-light btn-touch disabled px-3 fw-bold text-dark">{{ $l['cantidad'] }}</span>
                                <button type="button" wire:click="cambiarCantidad({{ $l['indice'] }}, 1)" class="btn btn-outline-secondary btn-touch px-3" aria-label="Sumar una unidad">
                                    <i class="bi bi-plus-lg"></i>
                                </button>
                            </div>

                            <div class="text-end">
                                @if ($l['precio_manual'])
                                    <div class="small text-muted text-decoration-line-through">{{ $dinero($l['precio_lista']) }} c/u</div>
                                    <div class="small">
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">Precio manual</span>
                                        <strong>{{ $dinero($l['precio_unitario']) }}</strong> c/u
                                    </div>
                                @else
                                    <div class="small text-muted">{{ $dinero($l['precio_unitario']) }} c/u</div>
                                @endif
                                <div class="fw-bold fs-6 text-dark">{{ $dinero($l['subtotal']) }}</div>
                            </div>
                        </div>

                        <div class="mt-2">
                            <button type="button" wire:click="alternarEdicionPrecio({{ $l['indice'] }})" class="btn btn-sm btn-link text-decoration-none p-0">
                                <i class="bi bi-pencil me-1"></i> {{ $l['editando'] ? 'Cerrar cambio de precio' : 'Cambiar precio' }}
                            </button>
                        </div>

                        @if ($l['editando'])
                            <div class="bg-warning-subtle border border-warning-subtle rounded p-2 mt-2">
                                <div class="small text-warning-emphasis mb-2">
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                    Este precio afecta <strong>solo a esta venta</strong>. El precio del catálogo no cambia.
                                </div>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" min="0" step="any" inputmode="decimal"
                                           wire:model.live.debounce.500ms="lineas.{{ $l['indice'] }}.precio_manual"
                                           class="form-control" placeholder="{{ $l['precio_lista'] }}"
                                           aria-label="Precio unitario manual">
                                    <button type="button" wire:click="restablecerPrecio({{ $l['indice'] }})" class="btn btn-outline-secondary">
                                        Restablecer
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-cart fs-1 d-block mb-2 text-secondary"></i>
                        <h6 class="fw-bold">Todavía no hay productos</h6>
                        <p class="small mb-0">Elegí un modelo, talle y color y tocá "Agregar".</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Columna derecha: totales y comprobante -->
        <div class="col-12 col-lg-5">

            <!-- Totales -->
            <div class="card p-3 mb-3 bg-white border-0 shadow-sm">
                <h2 class="h6 fw-bold mb-3"><i class="bi bi-calculator me-1 text-primary"></i> Resumen</h2>

                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Subtotal</span>
                    <strong>{{ $dinero($resumen['subtotal']) }}</strong>
                </div>

                <div class="row g-2 align-items-center mb-2">
                    <div class="col-7">
                        <label for="descuentoPct" class="text-muted mb-0">Descuento (%)</label>
                    </div>
                    <div class="col-5">
                        <input id="descuentoPct" type="number" min="0" max="100" step="any" inputmode="decimal"
                               wire:model.live.debounce.400ms="descuentoPct" class="form-control text-end" placeholder="0">
                    </div>
                </div>
                @if ($resumen['descuento_pct'] > 0)
                    <div class="d-flex justify-content-between mb-2 text-success">
                        <span>Descuento aplicado</span>
                        <strong>− {{ $dinero($resumen['descuento_monto']) }}</strong>
                    </div>
                @endif
                <div class="small text-muted mb-2">El descuento vale solo para esta venta (0 a 100 %).</div>

                <hr class="my-2">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="fs-5 fw-bold">Total</span>
                    <span class="fs-3 fw-bold text-primary">{{ $dinero($resumen['total']) }}</span>
                </div>

                <div class="d-grid gap-2">
                    <button type="button" wire:click="confirmarVentaSimulada" class="btn btn-success btn-touch fw-bold">
                        <i class="bi bi-check2-circle me-1"></i> Confirmar venta (simulado)
                    </button>
                    <button type="button" wire:click="descargarPdfSimulado" class="btn btn-outline-primary btn-touch">
                        <i class="bi bi-file-earmark-pdf me-1"></i> Descargar PDF (simulado)
                    </button>
                </div>
            </div>

            <!-- Previsualización del comprobante interno no fiscal -->
            <div class="card bg-white border-0 shadow-sm mb-3">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <span class="fw-bold"><i class="bi bi-receipt me-1 text-warning"></i> Comprobante interno</span>
                    <span class="badge bg-warning text-dark">No fiscal</span>
                </div>
                <div class="card-body p-3">
                    @if (count($comprobante['lineas']) > 0)
                        <div class="text-center mb-2">
                            <div class="fw-bold">Ojotas Capioví</div>
                            <div class="small text-muted">Comprobante interno N.º {{ $comprobante['numero'] }}</div>
                            <div class="small text-muted">{{ $comprobante['fecha'] }}</div>
                        </div>
                        <div class="small mb-2">
                            <div><span class="text-muted">Cliente:</span> {{ $comprobante['cliente'] }}</div>
                            <div><span class="text-muted">Lista de precios:</span> {{ $comprobante['lista'] }}</div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-2">
                                <thead class="table-light">
                                    <tr>
                                        <th>Detalle</th>
                                        <th class="text-end">Cant.</th>
                                        <th class="text-end">Precio</th>
                                        <th class="text-end">Importe</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($comprobante['lineas'] as $l)
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">{{ $l['modelo'] }}</div>
                                                <div class="small text-muted">Talle {{ $l['talle'] }} · {{ $l['color'] }}</div>
                                            </td>
                                            <td class="text-end">{{ $l['cantidad'] }}</td>
                                            <td class="text-end">
                                                {{ $dinero($l['precio_unitario']) }}
                                                @if ($l['precio_manual']) <span class="text-warning-emphasis" title="Precio manual">*</span> @endif
                                            </td>
                                            <td class="text-end">{{ $dinero($l['subtotal']) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="small">
                            <div class="d-flex justify-content-between"><span>Subtotal</span><span>{{ $dinero($comprobante['subtotal']) }}</span></div>
                            @if ($comprobante['descuento_pct'] > 0)
                                <div class="d-flex justify-content-between"><span>Descuento ({{ rtrim(rtrim(number_format($comprobante['descuento_pct'], 2, ',', '.'), '0'), ',') }} %)</span><span>− {{ $dinero($comprobante['descuento_monto']) }}</span></div>
                            @endif
                            <div class="d-flex justify-content-between fw-bold fs-6 mt-1"><span>TOTAL</span><span>{{ $dinero($comprobante['total']) }}</span></div>
                        </div>

                        @if ($comprobante['hay_precio_manual'])
                            <div class="small text-muted mt-2">* Precio cambiado a mano para esta venta.</div>
                        @endif
                        <div class="small text-muted text-center mt-3 border-top pt-2">
                            Documento interno sin validez fiscal.
                        </div>
                    @else
                        <div class="text-center text-muted py-3">
                            <i class="bi bi-receipt fs-1 d-block mb-2 text-secondary"></i>
                            <p class="small mb-0">Agregá productos para ver el comprobante.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
