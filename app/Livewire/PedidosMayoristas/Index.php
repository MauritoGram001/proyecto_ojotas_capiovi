<?php

namespace App\Livewire\PedidosMayoristas;

use Livewire\Component;

class Index extends Component
{
    public string $filtroEstado = '';
    public ?array $pedidoDetalle = null;

    public array $pedidos = [
        [
            'id' => 1,
            'numero' => 'PM-001',
            'cliente' => 'Distribuidora Norte S.R.L.',
            'fecha' => '20/11/2026',
            'estado' => 'Pendiente',
            'convertido' => false,
            'items' => [
                ['modelo' => 'Ojotas Clásicas Capioví', 'talle' => '39/40', 'color' => 'Azul', 'cantidad' => 20, 'precio' => 4500.0],
                ['modelo' => 'Sandalias Verano Confort', 'talle' => '37/38', 'color' => 'Beige', 'cantidad' => 10, 'precio' => 6800.0],
            ],
        ],
        [
            'id' => 2,
            'numero' => 'PM-002',
            'cliente' => 'Comercial El Sol S.A.',
            'fecha' => '19/11/2026',
            'estado' => 'Preparado',
            'convertido' => false,
            'items' => [
                ['modelo' => 'Remera Básica Algodón Capioví', 'talle' => 'M', 'color' => 'Gris', 'cantidad' => 30, 'precio' => 8900.0],
            ],
        ],
        [
            'id' => 3,
            'numero' => 'PM-003',
            'cliente' => 'Distribuidora Norte S.R.L.',
            'fecha' => '18/11/2026',
            'estado' => 'Entregado',
            'convertido' => true,
            'items' => [
                ['modelo' => 'Ojotas Playeras Estampadas', 'talle' => '37/38', 'color' => 'Flores Azul', 'cantidad' => 15, 'precio' => 5400.0],
            ],
        ],
        [
            'id' => 4,
            'numero' => 'PM-004',
            'cliente' => 'Comercial El Sol S.A.',
            'fecha' => '17/11/2026',
            'estado' => 'Cancelado',
            'convertido' => false,
            'items' => [
                ['modelo' => 'Bermuda Playera Secado Rápido', 'talle' => 'L', 'color' => 'Negro', 'cantidad' => 8, 'precio' => 11500.0],
            ],
        ],
    ];

    public function cambiarEstado(int $pedidoId, string $estado): void
    {
        $permitidos = ['Pendiente', 'Preparado', 'Entregado', 'Cancelado'];
        if (! in_array($estado, $permitidos, true)) {
            return;
        }

        foreach ($this->pedidos as $index => $pedido) {
            if ($pedido['id'] === $pedidoId) {
                $this->pedidos[$index]['estado'] = $estado;
                if ($this->pedidoDetalle && $this->pedidoDetalle['id'] === $pedidoId) {
                    $this->pedidoDetalle['estado'] = $estado;
                }

                return;
            }
        }
    }

    public function verDetalle(int $pedidoId): void
    {
        foreach ($this->pedidos as $pedido) {
            if ($pedido['id'] === $pedidoId) {
                $this->pedidoDetalle = $pedido;

                return;
            }
        }
    }

    public function cerrarDetalle(): void
    {
        $this->pedidoDetalle = null;
    }

    public function convertirAVenta(int $pedidoId): void
    {
        foreach ($this->pedidos as $index => $pedido) {
            if ($pedido['id'] === $pedidoId) {
                $this->pedidos[$index]['convertido'] = true;
                $this->pedidos[$index]['estado'] = 'Entregado';
                if ($this->pedidoDetalle && $this->pedidoDetalle['id'] === $pedidoId) {
                    $this->pedidoDetalle['convertido'] = true;
                    $this->pedidoDetalle['estado'] = 'Entregado';
                }
                session()->flash('success', 'Pedido ' . $pedido['numero'] . ' convertido a venta confirmada (simulación).');

                return;
            }
        }
    }

    public function render()
    {
        $filtrados = array_filter($this->pedidos, function ($pedido) {
            return $this->filtroEstado === '' || $pedido['estado'] === $this->filtroEstado;
        });

        return view('livewire.pedidos-mayoristas.index', [
            'pedidosFiltrados' => $filtrados,
        ])->layout('layouts.app', ['title' => 'Pedidos Mayoristas']);
    }
}
