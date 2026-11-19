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
Route::get('/productos', function () {
    return view('maquetas.productos');
})->name('productos.index');

Route::get('/clientes', function () {
    return view('maquetas.clientes');
})->name('clientes.index');

Route::get('/ventas/crear', function () {
    return view('maquetas.ventas-crear');
})->name('ventas.crear');

Route::get('/cuentas-corrientes', function () {
    return view('maquetas.cuentas-corrientes');
})->name('cuentas.index');

Route::get('/pedidos-mayoristas', function () {
    return view('maquetas.pedidos-mayoristas');
})->name('pedidos.index');

Route::get('/proveedores', function () {
    return view('maquetas.proveedores');
})->name('proveedores.index');

Route::get('/informes', function () {
    return view('maquetas.informes');
})->name('informes.index');

Route::get('/usuarios', function () {
    return view('maquetas.usuarios');
})->name('usuarios.index');
