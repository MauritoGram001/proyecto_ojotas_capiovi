@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card p-4 text-center bg-white shadow-sm">
            <div class="py-4">
                <i class="bi bi-clock-history text-warning fs-1 mb-3 d-block"></i>
                <h3 class="fw-bold text-dark">Pedidos Mayoristas</h3>
                <span class="badge bg-secondary mb-3">Tarea VIS-007 — Próxima en Fase Visual</span>
                <p class="text-muted max-w-lg mx-auto mb-4">Gestión de pedidos para recorridos (pendiente, preparado, entregado) sin reserva de stock.</p>
                <a href="{{ url('/') }}" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-left me-1"></i> Volver al Inicio
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
