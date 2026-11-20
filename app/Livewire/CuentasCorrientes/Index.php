<?php

namespace App\Livewire\CuentasCorrientes;

use App\Services\CuentasEjemplo;
use Illuminate\Support\Str;
use Livewire\Component;

/**
 * Cuentas corrientes de clientes y deudas con proveedores (VIS-005).
 * Fase visual: datos ficticios de solo lectura.
 */
class Index extends Component
{
    public const VISTA_CLIENTES = 'clientes';
    public const VISTA_PROVEEDORES = 'proveedores';

    public string $vista = self::VISTA_CLIENTES;
    public string $buscar = '';
    public bool $soloConDeuda = false;

    // Cuenta abierta para ver sus movimientos (cliente o proveedor, según la vista)
    public ?int $cuentaAbierta = null;

    public function cambiarVista(string $vista): void
    {
        if (in_array($vista, [self::VISTA_CLIENTES, self::VISTA_PROVEEDORES], true)) {
            $this->vista = $vista;
            $this->cuentaAbierta = null;
            $this->buscar = '';
        }
    }

    public function alternarCuenta(int $id): void
    {
        $this->cuentaAbierta = $this->cuentaAbierta === $id ? null : $id;
    }

    public function limpiarFiltros(): void
    {
        $this->buscar = '';
        $this->soloConDeuda = false;
    }

    private function coincide(array $cuenta): bool
    {
        if ($this->soloConDeuda && $cuenta['saldo'] <= 0) {
            return false;
        }

        $q = trim($this->buscar);
        if ($q === '') {
            return true;
        }

        return str_contains(
            Str::lower(Str::ascii($cuenta['nombre'])),
            Str::lower(Str::ascii($q))
        );
    }

    public function render()
    {
        $clientes = CuentasEjemplo::cuentasClientes();
        $proveedores = CuentasEjemplo::deudasProveedores();

        $lista = $this->vista === self::VISTA_CLIENTES ? $clientes : $proveedores;
        $filtradas = array_filter($lista, fn (array $c) => $this->coincide($c));

        $movimientos = [];
        if ($this->cuentaAbierta !== null && $this->vista === self::VISTA_CLIENTES) {
            $movimientos = CuentasEjemplo::movimientosCliente($this->cuentaAbierta);
        }

        return view('livewire.cuentas-corrientes.index', [
            'cuentas' => $filtradas,
            'movimientos' => $movimientos,
            'totalPorCobrar' => round(array_sum(array_column($clientes, 'saldo')), 2),
            'totalPorPagar' => round(array_sum(array_column($proveedores, 'saldo')), 2),
        ])->layout('layouts.app', ['title' => 'Cuentas Corrientes']);
    }
}
