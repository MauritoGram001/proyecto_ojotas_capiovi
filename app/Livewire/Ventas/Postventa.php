<?php

namespace App\Livewire\Ventas;

use Livewire\Component;

class Postventa extends Component
{
    public string $tab = 'anulaciones';

    public array $ventas = [
        [
            'id' => 1,
            'numero' => 'INT-0001',
            'fecha' => '18/11/2026',
            'cliente' => 'María González',
            'total' => 11700.0,
            'estado' => 'Confirmada',
            'items' => [
                ['id' => 101, 'modelo' => 'Ojotas Clásicas Capioví', 'talle' => '37/38', 'color' => 'Negro', 'cantidad' => 1, 'precio' => 6500.0],
                ['id' => 201, 'modelo' => 'Sandalias Verano Confort', 'talle' => '37/38', 'color' => 'Beige', 'cantidad' => 1, 'precio' => 5200.0],
            ],
        ],
        [
            'id' => 2,
            'numero' => 'INT-0002',
            'fecha' => '19/11/2026',
            'cliente' => 'Carlos Ruiz',
            'total' => 21400.0,
            'estado' => 'Confirmada',
            'items' => [
                ['id' => 401, 'modelo' => 'Remera Básica Algodón Capioví', 'talle' => 'M', 'color' => 'Gris', 'cantidad' => 1, 'precio' => 12500.0],
                ['id' => 301, 'modelo' => 'Ojotas Playeras Estampadas', 'talle' => '39/40', 'color' => 'Hojas Verde', 'cantidad' => 1, 'precio' => 8900.0],
            ],
        ],
        [
            'id' => 3,
            'numero' => 'INT-0003',
            'fecha' => '20/11/2026',
            'cliente' => 'Distribuidora Norte S.R.L.',
            'total' => 18000.0,
            'estado' => 'Anulada',
            'items' => [
                ['id' => 102, 'modelo' => 'Ojotas Clásicas Capioví', 'talle' => '39/40', 'color' => 'Azul', 'cantidad' => 4, 'precio' => 4500.0],
            ],
        ],
        [
            'id' => 4,
            'numero' => 'INT-0004',
            'fecha' => '20/11/2026',
            'cliente' => 'Comercial El Sol S.A.',
            'total' => 13600.0,
            'estado' => 'Devuelta',
            'items' => [
                ['id' => 201, 'modelo' => 'Sandalias Verano Confort', 'talle' => '37/38', 'color' => 'Beige', 'cantidad' => 2, 'precio' => 6800.0],
            ],
        ],
    ];

    public array $anulaciones = [
        ['numero' => 'ANU-001', 'ventaNumero' => 'INT-0003', 'motivo' => 'Error de carga en el pedido mayorista', 'fecha' => '20/11/2026'],
    ];

    public array $devoluciones = [
        [
            'numero' => 'DEV-001',
            'ventaNumero' => 'INT-0004',
            'devuelto' => 'Sandalias Verano Confort — 37/38 Beige',
            'nuevo' => 'Ojotas Clásicas Capioví — 39/40 Azul',
            'fallaFabricacion' => false,
            'fecha' => '20/11/2026',
        ],
    ];

    public array $stockDefectuoso = [
        ['numero' => 'SD-001', 'modelo' => 'Ojotas Clásicas Capioví', 'talle' => '41/42', 'color' => 'Negro', 'cantidad' => 3, 'origen' => 'CAM-001', 'motivo' => 'Costura defectuosa'],
    ];

    public array $catalogo = [
        ['id' => 101, 'modelo' => 'Ojotas Clásicas Capioví', 'talle' => '37/38', 'color' => 'Negro', 'precio' => 6500.0],
        ['id' => 102, 'modelo' => 'Ojotas Clásicas Capioví', 'talle' => '39/40', 'color' => 'Azul', 'precio' => 6500.0],
        ['id' => 201, 'modelo' => 'Sandalias Verano Confort', 'talle' => '37/38', 'color' => 'Beige', 'precio' => 9200.0],
        ['id' => 301, 'modelo' => 'Ojotas Playeras Estampadas', 'talle' => '39/40', 'color' => 'Hojas Verde', 'precio' => 7800.0],
        ['id' => 401, 'modelo' => 'Remera Básica Algodón Capioví', 'talle' => 'M', 'color' => 'Gris', 'precio' => 12500.0],
    ];

    // Modal de anulación con confirmación obligatoria (invariante VIS-010)
    public bool $mostrarModalAnular = false;
    public ?int $ventaAnularId = null;
    public string $justificacionAnulacion = '';
    public bool $confirmacionAnulacion = false;

    // Formulario de devolución por cambio
    public string $ventaDevolucionId = '1';
    public string $itemDevueltoId = '';
    public string $nuevoArticuloId = '';
    public bool $fallaFabricacion = false;

    public function abrirModalAnular(int $ventaId): void
    {
        foreach ($this->ventas as $venta) {
            if ($venta['id'] === $ventaId && $venta['estado'] === 'Confirmada') {
                $this->ventaAnularId = $ventaId;
                $this->justificacionAnulacion = '';
                $this->confirmacionAnulacion = false;
                $this->mostrarModalAnular = true;

                return;
            }
        }
    }

    public function cerrarModalAnular(): void
    {
        $this->mostrarModalAnular = false;
        $this->ventaAnularId = null;
    }

    public function confirmarAnulacion(): void
    {
        if (trim($this->justificacionAnulacion) === '') {
            session()->flash('warning', 'La anulación requiere una justificación.');

            return;
        }

        if (! $this->confirmacionAnulacion) {
            session()->flash('warning', 'Debés confirmar explícitamente antes de anular la venta.');

            return;
        }

        foreach ($this->ventas as $index => $venta) {
            if ($venta['id'] !== $this->ventaAnularId) {
                continue;
            }

            $this->ventas[$index]['estado'] = 'Anulada';
            $this->anulaciones[] = [
                'numero' => 'ANU-' . str_pad((string) (count($this->anulaciones) + 1), 3, '0', STR_PAD_LEFT),
                'ventaNumero' => $venta['numero'],
                'motivo' => trim($this->justificacionAnulacion),
                'fecha' => now()->format('d/m/Y'),
            ];
            session()->flash('success', 'Venta ' . $venta['numero'] . ' anulada y vinculada al registro (simulación).');
            break;
        }

        $this->cerrarModalAnular();
    }

    public function updatedVentaDevolucionId(): void
    {
        $this->itemDevueltoId = '';
        $this->nuevoArticuloId = '';
        $this->fallaFabricacion = false;
    }

    public function registrarDevolucion(): void
    {
        $venta = null;
        foreach ($this->ventas as $item) {
            if ((string) $item['id'] === $this->ventaDevolucionId) {
                $venta = $item;
                break;
            }
        }

        if ($venta === null || $this->itemDevueltoId === '' || $this->nuevoArticuloId === '') {
            session()->flash('warning', 'Seleccioná la venta original, el artículo devuelto y el nuevo entregado.');

            return;
        }

        $devuelto = null;
        foreach ($venta['items'] as $item) {
            if ((string) $item['id'] === $this->itemDevueltoId) {
                $devuelto = $item;
                break;
            }
        }

        $nuevo = null;
        foreach ($this->catalogo as $item) {
            if ((string) $item['id'] === $this->nuevoArticuloId) {
                $nuevo = $item;
                break;
            }
        }

        if ($devuelto === null || $nuevo === null) {
            session()->flash('warning', 'Las variantes seleccionadas no son válidas.');

            return;
        }

        $numeroDevolucion = 'DEV-' . str_pad((string) (count($this->devoluciones) + 1), 3, '0', STR_PAD_LEFT);

        $this->devoluciones[] = [
            'numero' => $numeroDevolucion,
            'ventaNumero' => $venta['numero'],
            'devuelto' => $devuelto['modelo'] . ' — ' . $devuelto['talle'] . ' / ' . $devuelto['color'],
            'nuevo' => $nuevo['modelo'] . ' — ' . $nuevo['talle'] . ' / ' . $nuevo['color'],
            'fallaFabricacion' => $this->fallaFabricacion,
            'fecha' => now()->format('d/m/Y'),
        ];

        if ($this->fallaFabricacion) {
            $numeroStock = 'SD-' . str_pad((string) (count($this->stockDefectuoso) + 1), 3, '0', STR_PAD_LEFT);
            $this->stockDefectuoso[] = [
                'numero' => $numeroStock,
                'modelo' => $devuelto['modelo'],
                'talle' => $devuelto['talle'],
                'color' => $devuelto['color'],
                'cantidad' => (int) $devuelto['cantidad'],
                'origen' => $numeroDevolucion,
                'motivo' => 'Falla de fabricación declarada en devolución',
            ];
        }

        foreach ($this->ventas as $index => $item) {
            if ($item['id'] === $venta['id']) {
                $this->ventas[$index]['estado'] = 'Devuelta';
                break;
            }
        }

        session()->flash('success', 'Devolución ' . $numeroDevolucion . ' vinculada a ' . $venta['numero'] . ' (simulación).');

        $this->itemDevueltoId = '';
        $this->nuevoArticuloId = '';
        $this->fallaFabricacion = false;
    }

    public function render()
    {
        $ventaAnular = null;
        foreach ($this->ventas as $venta) {
            if ($venta['id'] === $this->ventaAnularId) {
                $ventaAnular = $venta;
                break;
            }
        }

        $ventaDevolucion = null;
        $itemsVenta = [];
        foreach ($this->ventas as $venta) {
            if ((string) $venta['id'] === $this->ventaDevolucionId) {
                $ventaDevolucion = $venta;
                $itemsVenta = $venta['items'];
                break;
            }
        }

        return view('livewire.ventas.devolucion', [
            'ventaAnular' => $ventaAnular,
            'ventaDevolucion' => $ventaDevolucion,
            'itemsVenta' => $itemsVenta,
        ])->layout('layouts.app', ['title' => 'Anulaciones y Devoluciones']);
    }
}
