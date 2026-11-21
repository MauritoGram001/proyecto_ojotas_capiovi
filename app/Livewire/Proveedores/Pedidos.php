<?php

namespace App\Livewire\Proveedores;

use Livewire\Component;

class Pedidos extends Component
{
    public string $tab = 'pedidos';
    public string $filtroEstado = '';

    public array $pedidos = [
        ['id' => 1, 'numero' => 'PED-001', 'proveedor' => 'Fábrica de Calzados Misiones S.R.L.', 'producto' => 'Ojotas Clásicas Capioví — 39/40 Azul', 'cantidad' => 20, 'fecha' => '18/11/2026', 'esperada' => '25/11/2026', 'estado' => 'Pendiente'],
        ['id' => 2, 'numero' => 'PED-002', 'proveedor' => 'Textiles del Sur S.A.', 'producto' => 'Remera Básica Algodón — M Gris', 'cantidad' => 30, 'fecha' => '16/11/2026', 'esperada' => '22/11/2026', 'estado' => 'Recibido'],
        ['id' => 3, 'numero' => 'PED-003', 'proveedor' => 'Insumos Capioví', 'producto' => 'Suelas de goma — Talle 39/40', 'cantidad' => 50, 'fecha' => '19/11/2026', 'esperada' => '28/11/2026', 'estado' => 'Pendiente'],
        ['id' => 4, 'numero' => 'PED-004', 'proveedor' => 'Fábrica de Calzados Misiones S.R.L.', 'producto' => 'Sandalias Verano Confort — 37/38 Beige', 'cantidad' => 12, 'fecha' => '10/11/2026', 'esperada' => '17/11/2026', 'estado' => 'Recibido'],
    ];

    public array $cambios = [
        ['id' => 1, 'numero' => 'CAM-001', 'proveedor' => 'Fábrica de Calzados Misiones S.R.L.', 'producto' => 'Ojotas Clásicas Capioví — 41/42 Negro', 'cantidad' => 3, 'motivo' => 'Costura defectuosa', 'estado' => 'Enviada a cambio', 'fecha' => '19/11/2026'],
        ['id' => 2, 'numero' => 'CAM-002', 'proveedor' => 'Textiles del Sur S.A.', 'producto' => 'Remera Básica Algodón — L Negro', 'cantidad' => 2, 'motivo' => 'Mancha de tinte', 'estado' => 'Detectada', 'fecha' => '20/11/2026'],
        ['id' => 3, 'numero' => 'CAM-003', 'proveedor' => 'Insumos Capioví', 'producto' => 'Suelas de goma — 37/38', 'cantidad' => 5, 'motivo' => 'Material degradado', 'estado' => 'Recibida en reemplazo', 'fecha' => '14/11/2026'],
    ];

    public bool $mostrarFormulario = false;
    public string $formProveedor = 'Fábrica de Calzados Misiones S.R.L.';
    public string $formProducto = '';
    public int $formCantidad = 1;
    public string $formMotivo = '';

    public function marcarRecibido(int $pedidoId): void
    {
        foreach ($this->pedidos as $index => $pedido) {
            if ($pedido['id'] === $pedidoId) {
                $this->pedidos[$index]['estado'] = 'Recibido';
                session()->flash('success', 'Pedido ' . $pedido['numero'] . ' marcado como recibido (simulación).');

                return;
            }
        }
    }

    public function avanzarEstado(int $cambioId): void
    {
        $secuencia = ['Detectada', 'Enviada a cambio', 'Recibida en reemplazo'];

        foreach ($this->cambios as $index => $cambio) {
            if ($cambio['id'] !== $cambioId) {
                continue;
            }

            $posicion = array_search($cambio['estado'], $secuencia, true);
            if ($posicion !== false && $posicion < count($secuencia) - 1) {
                $this->cambios[$index]['estado'] = $secuencia[$posicion + 1];
            }

            return;
        }
    }

    public function abrirFormulario(): void
    {
        $this->reset(['formProducto', 'formCantidad', 'formMotivo']);
        $this->formProveedor = 'Fábrica de Calzados Misiones S.R.L.';
        $this->formCantidad = 1;
        $this->formMotivo = '';
        $this->mostrarFormulario = true;
    }

    public function cerrarFormulario(): void
    {
        $this->mostrarFormulario = false;
    }

    public function guardarCambio(): void
    {
        if (trim($this->formProducto) === '' || trim($this->formMotivo) === '') {
            session()->flash('warning', 'Completá el producto y el motivo de la mercadería defectuosa.');

            return;
        }

        $nuevoId = count($this->cambios) + 1;
        $this->cambios[] = [
            'id' => $nuevoId,
            'numero' => 'CAM-' . str_pad((string) $nuevoId, 3, '0', STR_PAD_LEFT),
            'proveedor' => $this->formProveedor,
            'producto' => trim($this->formProducto),
            'cantidad' => (int) $this->formCantidad,
            'motivo' => trim($this->formMotivo),
            'estado' => 'Detectada',
            'fecha' => now()->format('d/m/Y'),
        ];

        $this->mostrarFormulario = false;
        session()->flash('success', 'Mercadería defectuosa registrada en estado Detectada (simulación).');
    }

    public function render()
    {
        $pedidosFiltrados = array_filter($this->pedidos, function ($pedido) {
            return $this->filtroEstado === '' || $pedido['estado'] === $this->filtroEstado;
        });

        $view = $this->tab === 'cambios'
            ? 'livewire.proveedores.cambios'
            : 'livewire.proveedores.pedidos';

        return view($view, [
            'pedidosFiltrados' => $pedidosFiltrados,
        ])->layout('layouts.app', ['title' => 'Pedidos y Cambios']);
    }
}
