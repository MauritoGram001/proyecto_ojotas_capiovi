<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm py-2">
    <div class="container-fluid px-3">
        <!-- Logo / Nombre del Negocio -->
        <a class="navbar-brand fw-bold d-flex align-items-center" href="{{ url('/') }}">
            <i class="bi bi-box-seam-fill text-warning fs-4 me-2"></i>
            <span>Ojotas Capioví</span>
            <span class="badge bg-secondary ms-2 small fw-normal d-none d-sm-inline">Gestión</span>
        </a>

        <!-- Botón móvil para abrir menú offcanvas -->
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#mainNavbarOffcanvas" aria-controls="mainNavbarOffcanvas" aria-label="Abrir menú">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Menú Offcanvas adaptable (móviles y escritorio) -->
        <div class="offcanvas offcanvas-start bg-dark text-white" tabindex="-1" id="mainNavbarOffcanvas" aria-labelledby="mainNavbarOffcanvasLabel">
            <div class="offcanvas-header border-bottom border-secondary">
                <h5 class="offcanvas-title text-white fw-bold d-flex align-items-center" id="mainNavbarOffcanvasLabel">
                    <i class="bi bi-box-seam-fill text-warning me-2"></i> Ojotas Capioví
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
            </div>

            <div class="offcanvas-body d-flex flex-column flex-lg-row justify-content-between p-3 p-lg-0">
                <!-- Enlaces Principales -->
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('/') ? 'active text-warning' : 'text-white-50' }} py-2" href="{{ url('/') }}">
                            <i class="bi bi-house-door me-1"></i> Inicio
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('ventas*') ? 'active text-warning' : 'text-white-50' }} py-2" href="{{ url('/ventas/crear') }}">
                            <i class="bi bi-cart-check me-1"></i> Ventas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('productos*') ? 'active text-warning' : 'text-white-50' }} py-2" href="{{ url('/productos') }}">
                            <i class="bi bi-grid-3x3-gap me-1"></i> Productos & Stock
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('clientes*') ? 'active text-warning' : 'text-white-50' }} py-2" href="{{ url('/clientes') }}">
                            <i class="bi bi-people me-1"></i> Clientes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('cuentas-corrientes*') ? 'active text-warning' : 'text-white-50' }} py-2" href="{{ url('/cuentas-corrientes') }}">
                            <i class="bi bi-cash-stack me-1"></i> Cuentas & Cobros
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('pedidos-mayoristas*') ? 'active text-warning' : 'text-white-50' }} py-2" href="{{ url('/pedidos-mayoristas') }}">
                            <i class="bi bi-truck me-1"></i> Pedidos Mayoristas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('proveedores*') ? 'active text-warning' : 'text-white-50' }} py-2" href="{{ url('/proveedores') }}">
                            <i class="bi bi-building me-1"></i> Proveedores & Compras
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('informes*') ? 'active text-warning' : 'text-white-50' }} py-2" href="{{ url('/informes') }}">
                            <i class="bi bi-graph-up me-1"></i> Informes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('usuarios*') ? 'active text-warning' : 'text-white-50' }} py-2" href="{{ url('/usuarios') }}">
                            <i class="bi bi-shield-lock me-1"></i> Usuarios
                        </a>
                    </li>
                </ul>

                <!-- Acciones Rápidas y Conmutador de Rol Simulado -->
                <div class="d-flex flex-column flex-lg-row align-items-lg-center gap-2 pt-3 pt-lg-0 border-top border-secondary border-lg-0">
                    <!-- Selector de rol simulado para probar vistas -->
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-light dropdown-toggle w-100" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle me-1"></i> Rol: <strong id="currentRoleLabel">Dueño</strong>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow">
                            <li><h6 class="dropdown-header">Cambiar vista de rol (Simulación)</h6></li>
                            <li><a class="dropdown-item" href="#" onclick="setSimulatedRole('Dueño')"><i class="bi bi-star-fill text-warning me-2"></i> Dueño (Acceso total)</a></li>
                            <li><a class="dropdown-item" href="#" onclick="setSimulatedRole('Administrador')"><i class="bi bi-gear-fill text-primary me-2"></i> Administrador (Operativo)</a></li>
                            <li><a class="dropdown-item" href="#" onclick="setSimulatedRole('Empleado')"><i class="bi bi-shop text-success me-2"></i> Empleado (Ventas y Stock)</a></li>
                        </ul>
                    </div>

                    <!-- Botón directo de Nueva Venta -->
                    <a href="{{ url('/ventas/crear') }}" class="btn btn-sm btn-primary fw-bold text-nowrap">
                        <i class="bi bi-plus-circle me-1"></i> Venta Rápida
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>

<script>
    function setSimulatedRole(role) {
        document.getElementById('currentRoleLabel').innerText = role;
        localStorage.setItem('simulatedRole', role);
        // Despachar evento para componentes reactivos si fuera necesario
        window.dispatchEvent(new CustomEvent('role-changed', { detail: role }));
    }
    document.addEventListener('DOMContentLoaded', () => {
        const saved = localStorage.getItem('simulatedRole');
        if (saved && document.getElementById('currentRoleLabel')) {
            document.getElementById('currentRoleLabel').innerText = saved;
        }
    });
</script>
