<?php

namespace App\Livewire\Proveedores;

use App\Services\AbastecimientoEjemplo as Datos;
use Illuminate\Support\Str;
use Livewire\Component;

/**
 * Seguimiento de pedidos a proveedores (VIS-006).
 * Fase visual: marcar un pedido como recibido NO modifica el stock.
 */
class Pedidos extends Component
{
    public string $buscar = '';
    public string $filtroEstado = '';
    public string $filtroProveedor = '';

    // Pedido con el detalle desplegado
    public ?string $pedidoAbierto = null;

    public array $pedidos = [];
    public string $mensajeExito = '';

    public function mount(): void
    {
        $this->pedidos = Datos::pedidos();
    }

    public function alternarPedido(string $id): void
    {
        $this->pedidoAbierto = $this->pedidoAbierto === $id ? null : $id;
    }

    public function marcarRecibido(string $id): void
    {
        foreach ($this->pedidos as $i => $p) {
            if ($p['id'] === $id && $p['estado'] === Datos::PEDIDO_PENDIENTE) {
                $this->pedidos[$i]['estado'] = Datos::PEDIDO_RECIBIDO;
                $this->pedidos[$i]['fecha_recepcion'] = now()->toDateString();
                $this->mensajeExito = 'Pedido ' . $id . ' marcado como recibido. Simulación: no se modificó el stock.';
                return;
            }
        }
    }

    public function limpiarFiltros(): void
    {
        $this->reset(['buscar', 'filtroEstado', 'filtroProveedor']);
    }

    private function normalizar(string $texto): string
    {
        return Str::lower(Str::ascii($texto));
    }

    private function coincide(array $p): bool
    {
        if ($this->filtroEstado !== '' && $p['estado'] !== $this->filtroEstado) {
            return false;
        }
        if ($this->filtroProveedor !== '' && (string) $p['proveedor_id'] !== $this->filtroProveedor) {
            return false;
        }

        $q = $this->normalizar(trim($this->buscar));
        if ($q === '') {
            return true;
        }

        // Búsqueda por número de pedido o por modelo de alguno de sus productos.
        if (str_contains($this->normalizar($p['id']), $q)) {
            return true;
        }
        foreach ($p['items'] as $item) {
            $modelo = Datos::variantes()[$item['variante_id']]['modelo'] ?? '';
            if (str_contains($this->normalizar($modelo), $q)) {
                return true;
            }
        }

        return false;
    }

    public function render()
    {
        $filtrados = array_values(array_filter($this->pedidos, fn (array $p) => $this->coincide($p)));
        usort($filtrados, fn (array $a, array $b) => strcmp($b['fecha'], $a['fecha']));

        $estados = array_count_values(array_column($this->pedidos, 'estado'));

        return view('livewire.proveedores.pedidos', [
            'pedidosFiltrados' => $filtrados,
            'proveedores' => Datos::proveedores(),
            'pendientes' => $estados[Datos::PEDIDO_PENDIENTE] ?? 0,
            'recibidos' => $estados[Datos::PEDIDO_RECIBIDO] ?? 0,
        ])->layout('layouts.app', ['title' => 'Pedidos a Proveedores']);
    }
}
