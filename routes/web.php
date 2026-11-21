<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

// Acceso público: solo el formulario de ingreso.
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:6,1');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Toda la gestión queda detrás de credenciales verificadas en servidor.
Route::middleware('auth')->group(function () {
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

    // Vistas compartidas por todos los roles autenticados
    Route::get('/productos', \App\Livewire\Productos\Index::class)->name('productos.index');
    Route::get('/clientes', \App\Livewire\Clientes\Index::class)->name('clientes.index');
    Route::get('/ventas/crear', \App\Livewire\Ventas\Crear::class)->name('ventas.crear');
    Route::get('/ventas/{venta}/comprobante', [\App\Http\Controllers\ComprobanteController::class, 'descargar'])->name('ventas.comprobante');

    // Caja, cobros y corrections posteriores a la venta
    Route::middleware('role:Administrador,Dueño')->group(function () {
        Route::get('/ventas/postventa', \App\Livewire\Ventas\Postventa::class)->name('ventas.postventa');
        Route::get('/cuentas-corrientes', \App\Livewire\CuentasCorrientes\Index::class)->name('cuentas.index');
        Route::get('/pedidos-mayoristas', \App\Livewire\PedidosMayoristas\Index::class)->name('pedidos.index');
        Route::get('/informes', \App\Livewire\Informes\Index::class)->name('informes.index');
        Route::get('/usuarios', \App\Livewire\Usuarios\Index::class)->name('usuarios.index');
    });

    // Operaciones de compras y proveedores: gestión operativa total
    Route::middleware('role:Administrador')->group(function () {
        Route::get('/proveedores', \App\Livewire\Proveedores\Index::class)->name('proveedores.index');
        Route::get('/proveedores/pedidos', \App\Livewire\Proveedores\Pedidos::class)->name('proveedores.pedidos');
    });
});
