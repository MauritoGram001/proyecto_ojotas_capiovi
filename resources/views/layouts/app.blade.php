<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Gestión Interna' }} — Ojotas Capioví</title>
    
    <!-- Bootstrap 5.3.3 CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.min.css') }}">
    
    <style>
        :root {
            --oc-primary: #0d6efd;
            --oc-surface: #f8f9fa;
        }
        body {
            background-color: #f4f6f9;
            color: #212529;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        main {
            flex: 1;
        }
        /* Botones táctiles optimizados para celular (RNF 06) */
        .btn-touch {
            min-height: 48px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            font-weight: 500;
        }
        /* Tablas adaptables con scroll horizontal limpio */
        .table-responsive {
            background: #ffffff;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .table > :not(caption) > * > * {
            padding: 0.75rem 0.85rem;
        }
        /* Banner de etapa visual */
        .mock-banner {
            background-color: #fff3cd;
            color: #664d03;
            border-bottom: 1px solid #ffe69c;
            font-size: 0.875rem;
        }
        .nav-link.active {
            font-weight: 600;
            color: var(--oc-primary) !important;
        }
        .card {
            border: 1px solid #e9ecef;
            border-radius: 0.6rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }
        /* El menú expandido debe ajustarse al ancho disponible (sin scroll horizontal) */
        @media (min-width: 992px) {
            .navbar .offcanvas-body {
                flex-wrap: wrap;
                row-gap: 0.25rem;
                max-width: 100%;
            }
            .navbar .navbar-nav {
                flex-wrap: wrap;
                row-gap: 0.25rem;
            }
            .navbar .navbar-nav .nav-link {
                padding-top: 0.4rem;
                padding-bottom: 0.4rem;
            }
        }
    </style>
    
    @livewireStyles
</head>
<body>

    <!-- Aviso de Etapa Visual -->
    <div class="mock-banner py-1 px-3 text-center d-flex justify-content-between align-items-center">
        <span><i class="bi bi-eye me-1"></i> <strong>Modo Visual (Fase B):</strong> Vistas con datos ficticios para validación de experiencia.</span>
        <a href="{{ url('plan-visor') }}" class="badge bg-warning text-dark text-decoration-none py-1 px-2">Ver Plan Maestro</a>
    </div>

    <!-- Navegación Principal -->
    @include('components.navbar')

    <!-- Contenido Principal -->
    <main class="container-fluid px-3 px-md-4 py-3">
        <!-- Notificaciones y Alertas Flash -->
        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-3" role="alert">
                <i class="bi bi-check-circle-fill fs-5 me-2"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        @endif

        @if (session()->has('warning'))
            <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center mb-3" role="alert">
                <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
                <div>{{ session('warning') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center mb-3" role="alert">
                <i class="bi bi-x-circle-fill fs-5 me-2"></i>
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        @endif

        <!-- Encabezado de página opcional -->
        @isset($header)
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 pb-2 border-bottom">
                <h1 class="h4 mb-0 text-dark fw-bold">{{ $header }}</h1>
                @isset($actions)
                    <div class="mt-2 mt-md-0">
                        {{ $actions }}
                    </div>
                @endisset
            </div>
        @endisset

        {{ $slot ?? '' }}
        @yield('content')
    </main>

    <!-- Pie de página simplificado -->
    <footer class="bg-white border-top py-2 px-3 text-muted small text-center mt-auto">
        <div class="container-fluid d-flex flex-wrap justify-content-between align-items-center">
            <span><strong>Ojotas Capioví</strong> — Sistema de Gestión Interna</span>
            <span><i class="bi bi-wifi text-success me-1"></i> Online &bull; Laravel 12 &bull; Bootstrap 5</span>
        </div>
    </footer>

    <!-- Bootstrap 5.3.3 JS Bundle -->
    <script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
    
    @livewireScripts
</body>
</html>
