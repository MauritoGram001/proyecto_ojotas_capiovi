@extends('layouts.app')

@section('content')
<div class="row g-3">
    <!-- Encabezado de Bienvenida y Estado -->
    <div class="col-12">
        <div class="card bg-white p-3 p-md-4 border-0 shadow-sm">
            <div class="d-flex flex-wrap justify-content-between align-items-center">
                <div>
                    <h2 class="h3 fw-bold text-dark mb-1 d-flex align-items-center">
                        <i class="bi bi-shop text-primary me-2"></i> Ojotas Capioví
                    </h2>
                    <p class="text-muted mb-0 small">Sistema interno de ventas textiles y calzado &bull; Modo maquetas interactivas (Fase B)</p>
                </div>
                <div class="mt-2 mt-md-0 d-flex gap-2">
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 d-flex align-items-center">
                        <i class="bi bi-check-circle-fill me-1"></i> Servidor Local Operativo
                    </span>
                    <a href="{{ url('plan-visor') }}" class="btn btn-sm btn-outline-primary fw-semibold">
                        <i class="bi bi-diagram-3 me-1"></i> Visor de Plan
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Acciones Rápidas Principales (Tarjetas táctiles de acceso directo) -->
    <div class="col-12">
        <h5 class="fw-bold text-secondary mb-3"><i class="bi bi-lightning-charge me-1"></i> Acciones Principales</h5>
        <div class="row g-3">
            <!-- 1. Venta Rápida -->
            <div class="col-12 col-sm-6 col-lg-3">
                <a href="{{ url('/ventas/crear') }}" class="card text-decoration-none h-100 p-3 border-primary border-opacity-25 hover-shadow bg-white">
                    <div class="d-flex align-items-center mb-2">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-3 me-3">
                            <i class="bi bi-cart-plus fs-3"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-0">Nueva Venta</h6>
                            <span class="badge bg-primary text-white small">Mostrador / Móvil</span>
                        </div>
                    </div>
                    <p class="text-muted small mb-0">Venta minorista sin cliente o mayorista con cuenta corriente.</p>
                </a>
            </div>

            <!-- 2. Catálogo y Stock -->
            <div class="col-12 col-sm-6 col-lg-3">
                <a href="{{ url('/productos') }}" class="card text-decoration-none h-100 p-3 border-success border-opacity-25 hover-shadow bg-white">
                    <div class="d-flex align-items-center mb-2">
                        <div class="bg-success bg-opacity-10 text-success rounded-3 p-3 me-3">
                            <i class="bi bi-boxes fs-3"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-0">Stock & Variantes</h6>
                            <span class="badge bg-success text-white small">Talle y Color</span>
                        </div>
                    </div>
                    <p class="text-muted small mb-0">Consulta rápida de existencias y precios mayorista/minorista.</p>
                </a>
            </div>

            <!-- 3. Cuentas y Cobros -->
            <div class="col-12 col-sm-6 col-lg-3">
                <a href="{{ url('/cuentas-corrientes') }}" class="card text-decoration-none h-100 p-3 border-warning border-opacity-25 hover-shadow bg-white">
                    <div class="d-flex align-items-center mb-2">
                        <div class="bg-warning bg-opacity-10 text-warning-emphasis rounded-3 p-3 me-3">
                            <i class="bi bi-cash-coin fs-3"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-0">Cuentas Corrientes</h6>
                            <span class="badge bg-warning text-dark small">Cobro de boletas</span>
                        </div>
                    </div>
                    <p class="text-muted small mb-0">Saldos de clientes, boletas pendientes y pagos combinados.</p>
                </a>
            </div>

            <!-- 4. Pedidos Mayoristas -->
            <div class="col-12 col-sm-6 col-lg-3">
                <a href="{{ url('/pedidos-mayoristas') }}" class="card text-decoration-none h-100 p-3 border-info border-opacity-25 hover-shadow bg-white">
                    <div class="d-flex align-items-center mb-2">
                        <div class="bg-info bg-opacity-10 text-info-emphasis rounded-3 p-3 me-3">
                            <i class="bi bi-truck fs-3"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-0">Pedidos Mayoristas</h6>
                            <span class="badge bg-info text-dark small">Recorridos</span>
                        </div>
                    </div>
                    <p class="text-muted small mb-0">Preparación y entrega de pedidos (sin reserva de stock).</p>
                </a>
            </div>
        </div>
    </div>

    <!-- Accesos Secundarios -->
    <div class="col-12">
        <div class="row g-3">
            <!-- Proveedores y Compras -->
            <div class="col-12 col-md-4">
                <a href="{{ url('/proveedores') }}" class="card text-decoration-none p-3 bg-white h-100">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-building fs-4 text-secondary me-3"></i>
                        <div>
                            <h6 class="fw-bold text-dark mb-0">Proveedores & Compras</h6>
                            <small class="text-muted">Pedidos, recepción y mercadería defectuosa</small>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Clientes -->
            <div class="col-12 col-md-4">
                <a href="{{ url('/clientes') }}" class="card text-decoration-none p-3 bg-white h-100">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-people fs-4 text-secondary me-3"></i>
                        <div>
                            <h6 class="fw-bold text-dark mb-0">Directorio de Clientes</h6>
                            <small class="text-muted">Minoristas y mayoristas con CUIT</small>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Informes -->
            <div class="col-12 col-md-4">
                <a href="{{ url('/informes') }}" class="card text-decoration-none p-3 bg-white h-100">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-graph-up-arrow fs-4 text-secondary me-3"></i>
                        <div>
                            <h6 class="fw-bold text-dark mb-0">Informes & Resúmenes</h6>
                            <small class="text-muted">Ventas por período, historial y caja diaria</small>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Resumen de Reglas Confirmadas -->
    <div class="col-12">
        <div class="card bg-white p-3 border-0 shadow-sm">
            <h6 class="fw-bold text-dark mb-2"><i class="bi bi-info-circle text-primary me-1"></i> Reglas Operativas Confirmadas</h6>
            <div class="row g-2 text-muted small">
                <div class="col-12 col-md-4">
                    <i class="bi bi-check2 text-success me-1"></i> <strong>Alerta de stock:</strong> Aviso visual activo con menos de 10 unidades (RF 13).
                </div>
                <div class="col-12 col-md-4">
                    <i class="bi bi-check2 text-success me-1"></i> <strong>Precios y Descuentos:</strong> Cambios manuales afectan solo la venta en curso (RF 19).
                </div>
                <div class="col-12 col-md-4">
                    <i class="bi bi-check2 text-success me-1"></i> <strong>Devoluciones:</strong> Se realiza cambio; el par fallado va a "Stock defectuoso" (RF 22).
                </div>
                <div class="col-12 col-md-4">
                    <i class="bi bi-check2 text-success me-1"></i> <strong>Deuda de clientes:</strong> Advertencia visual sin bloquear la venta (RF 09).
                </div>
                <div class="col-12 col-md-4">
                    <i class="bi bi-check2 text-success me-1"></i> <strong>Pedidos mayoristas:</strong> No descuentan ni reservan stock hasta vender (RF 17).
                </div>
                <div class="col-12 col-md-4">
                    <i class="bi bi-check2 text-success me-1"></i> <strong>Comprobantes:</strong> Estrictamente internos y no fiscales (RF 06).
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
