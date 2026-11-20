<?php

use Illuminate\Support\Facades\Route;

// Pantalla principal / Dashboard simplificado
Route::get('/', function () {
    return view('inicio');
})->name('inicio');

// Ruta directa para visualizar el Plan Maestro generado
Route::get('/plan-visor', function () {
    $path = base_path('docs/plan-maestro/visualizacion.html');
    if (file_exists($path)) {
        return response()->file($path, ['Content-Type' => 'text/html; charset=UTF-8']);
    }
    return redirect('/');
})->name('plan.visor');

// Rutas de maqueta para las próximas tareas de la Fase B (VIS-002 a VIS-010)
// Tarea VIS-002: Catálogo de productos, variantes y existencias
Route::get('/productos', \App\Livewire\Productos\Index::class)->name('productos.index');

// Tarea VIS-003: Directorio de clientes (CUIT condicional para mayoristas) y selector de venta rápida
Route::get('/clientes', \App\Livewire\Clientes\Index::class)->name('clientes.index');

// Tarea VIS-004: Pantalla de venta (POS) y plantilla del comprobante interno no fiscal
Route::get('/ventas/crear', \App\Livewire\Ventas\Crear::class)->name('ventas.crear');
Route::get('/ventas/comprobante-simulado', \App\Http\Controllers\ComprobanteSimuladoController::class)->name('ventas.comprobante-simulado');

// Tarea VIS-005: Cuentas corrientes (clientes y proveedores) y cobro combinado con imputación a boletas
Route::get('/cuentas-corrientes', \App\Livewire\CuentasCorrientes\Index::class)->name('cuentas.index');
Route::get('/cobros/nuevo/{cliente?}', \App\Livewire\Cobros\Formulario::class)->name('cobros.crear');

Route::get('/pedidos-mayoristas', function () {
    return view('maquetas.pedidos-mayoristas');
})->name('pedidos.index');

// Tarea VIS-003: Directorio de proveedores con datos de contacto
Route::get('/proveedores', \App\Livewire\Proveedores\Index::class)->name('proveedores.index');

// Tarea VIS-006: Seguimiento de pedidos a proveedores y circuito de mercadería defectuosa / cambios
Route::get('/proveedores/pedidos', \App\Livewire\Proveedores\Pedidos::class)->name('proveedores.pedidos');
Route::get('/proveedores/cambios', \App\Livewire\Proveedores\Cambios::class)->name('proveedores.cambios');

Route::get('/informes', function () {
    return view('maquetas.informes');
})->name('informes.index');

Route::get('/usuarios', function () {
    return view('maquetas.usuarios');
})->name('usuarios.index');
