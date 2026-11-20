{{-- Secciones del módulo de proveedores. Variable: $activa = directorio | pedidos | cambios --}}
<div class="btn-group w-100 mb-3" role="group" aria-label="Secciones de proveedores">
    <a href="{{ route('proveedores.index') }}" class="btn btn-touch {{ $activa === 'directorio' ? 'btn-primary' : 'btn-outline-primary' }}">
        <i class="bi bi-building me-1"></i> Directorio
    </a>
    <a href="{{ route('proveedores.pedidos') }}" class="btn btn-touch {{ $activa === 'pedidos' ? 'btn-primary' : 'btn-outline-primary' }}">
        <i class="bi bi-truck me-1"></i> Pedidos
    </a>
    <a href="{{ route('proveedores.cambios') }}" class="btn btn-touch {{ $activa === 'cambios' ? 'btn-primary' : 'btn-outline-primary' }}">
        <i class="bi bi-arrow-repeat me-1"></i> Cambios
    </a>
</div>
