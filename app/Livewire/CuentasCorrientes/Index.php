<?php

namespace App\Livewire\CuentasCorrientes;

use Livewire\Component;

class Index extends Component
{
    public string $tab = 'clientes';
    public string $clienteId = '1';
    public bool $mostrarCobro = false;

    public array $clientes = [
        ['id' => 1, 'nombre' => 'María González', 'saldo' => 18500.0, 'telefono' => '3765 412345'],
        ['id' => 2, 'nombre' => 'Carlos Ruiz', 'saldo' => 0.0, 'telefono' => '3765 489012'],
        ['id' => 3, 'nombre' => 'Distribuidora Norte S.R.L.', 'saldo' => 94500.0, 'telefono' => '3765 501234'],
        ['id' => 4, 'nombre' => 'Comercial El Sol S.A.', 'saldo' => 32000.0, 'telefono' => '3765 557890'],
    ];

    public array $boletas = [
        ['id' => 101, 'numero' => 'B-0101', 'cliente_id' => 1, 'fecha' => '18/11/2026', 'monto' => 12500.0, 'saldo' => 12500.0],
        ['id' => 102, 'numero' => 'B-0102', 'cliente_id' => 1, 'fecha' => '19/11/2026', 'monto' => 6000.0, 'saldo' => 6000.0],
        ['id' => 103, 'numero' => 'B-0103', 'cliente_id' => 3, 'fecha' => '20/11/2026', 'monto' => 94500.0, 'saldo' => 94500.0],
        ['id' => 104, 'numero' => 'B-0104', 'cliente_id' => 4, 'fecha' => '20/11/2026', 'monto' => 32000.0, 'saldo' => 32000.0],
    ];

    public array $movimientos = [
        ['id' => 1, 'cliente_id' => 1, 'fecha' => '19/11/2026', 'tipo' => 'Boleta', 'detalle' => 'Venta de ojotas', 'monto' => 6000.0, 'estado' => 'Pendiente'],
        ['id' => 2, 'cliente_id' => 1, 'fecha' => '17/11/2026', 'tipo' => 'Pago', 'detalle' => 'Efectivo en mostrador', 'monto' => -8000.0, 'estado' => 'Aplicado'],
        ['id' => 3, 'cliente_id' => 1, 'fecha' => '18/11/2026', 'tipo' => 'Boleta', 'detalle' => 'Venta de remeras', 'monto' => 12500.0, 'estado' => 'Pendiente'],
        ['id' => 4, 'cliente_id' => 3, 'fecha' => '20/11/2026', 'tipo' => 'Boleta', 'detalle' => 'Pedido mayorista', 'monto' => 94500.0, 'estado' => 'Pendiente'],
        ['id' => 5, 'cliente_id' => 4, 'fecha' => '20/11/2026', 'tipo' => 'Boleta', 'detalle' => 'Pedido mayorista', 'monto' => 32000.0, 'estado' => 'Pendiente'],
    ];

    public array $proveedores = [
        ['id' => 1, 'razon_social' => 'Fábrica de Calzados Misiones S.R.L.', 'deuda' => 125000.0, 'vencimiento' => '30/11/2026', 'contacto' => 'Ing. Roberto Acosta'],
        ['id' => 2, 'razon_social' => 'Textiles del Sur S.A.', 'deuda' => 68400.0, 'vencimiento' => '05/12/2026', 'contacto' => 'Ana Paiva'],
        ['id' => 3, 'razon_social' => 'Insumos Capioví', 'deuda' => 15200.0, 'vencimiento' => '10/12/2026', 'contacto' => 'Jorge Benítez'],
    ];

    public function abrirCobro(): void
    {
        $this->mostrarCobro = true;
    }

    public function cerrarCobro(): void
    {
        $this->mostrarCobro = false;
    }

    public function render()
    {
        $cliente = null;
        $boletasPendientes = [];
        $movimientosCliente = [];

        foreach ($this->clientes as $item) {
            if ((string) $item['id'] === $this->clienteId) {
                $cliente = $item;
                break;
            }
        }

        foreach ($this->boletas as $boleta) {
            if ((string) $boleta['cliente_id'] === $this->clienteId) {
                $boletasPendientes[] = $boleta;
            }
        }

        foreach ($this->movimientos as $movimiento) {
            if ((string) $movimiento['cliente_id'] === $this->clienteId) {
                $movimientosCliente[] = $movimiento;
            }
        }

        return view('livewire.cuentas-corrientes.index', [
            'clienteSeleccionado' => $cliente,
            'boletasPendientes' => $boletasPendientes,
            'movimientosCliente' => $movimientosCliente,
        ])->layout('layouts.app', ['title' => 'Cuentas & Cobros']);
    }
}
