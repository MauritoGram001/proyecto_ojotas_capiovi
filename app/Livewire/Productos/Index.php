<?php

namespace App\Livewire\Productos;

use Livewire\Component;

class Index extends Component
{
    public string $buscar = '';
    public string $filtroCategoria = '';
    public string $filtroStock = '';

    // Estado del modal de detalle
    public ?array $productoDetalle = null;

    // Estado del formulario simulado
    public bool $mostrarFormulario = false;
    public string $formNombre = '';
    public string $formCategoria = 'Calzado';
    public float $formPrecioMinorista = 0.0;
    public float $formPrecioMayorista = 0.0;
    public string $formTalle = '39/40';
    public string $formColor = 'Negro';
    public int $formStock = 15;

    // Catálogo con datos ficticios para la maqueta
    public array $productos = [
        [
            'id' => 1,
            'modelo' => 'Ojotas Clásicas Capioví',
            'categoria' => 'Calzado',
            'precio_minorista' => 6500.0,
            'precio_mayorista' => 4500.0,
            'stock_total' => 18,
            'variantes' => [
                ['id' => 101, 'talle' => '37/38', 'color' => 'Negro', 'stock' => 5],   // Bajo stock (<10)
                ['id' => 102, 'talle' => '39/40', 'color' => 'Azul', 'stock' => 4],    // Bajo stock (<10)
                ['id' => 103, 'talle' => '41/42', 'color' => 'Negro', 'stock' => 9],   // Bajo stock (<10)
            ],
        ],
        [
            'id' => 2,
            'modelo' => 'Sandalias Verano Confort',
            'categoria' => 'Calzado',
            'precio_minorista' => 9200.0,
            'precio_mayorista' => 6800.0,
            'stock_total' => 45,
            'variantes' => [
                ['id' => 201, 'talle' => '35/36', 'color' => 'Blanco', 'stock' => 15],
                ['id' => 202, 'talle' => '37/38', 'color' => 'Beige', 'stock' => 20],
                ['id' => 203, 'talle' => '39/40', 'color' => 'Negro', 'stock' => 10],
            ],
        ],
        [
            'id' => 3,
            'modelo' => 'Ojotas Playeras Estampadas',
            'categoria' => 'Calzado',
            'precio_minorista' => 7800.0,
            'precio_mayorista' => 5400.0,
            'stock_total' => 8, // Bajo stock general
            'variantes' => [
                ['id' => 301, 'talle' => '37/38', 'color' => 'Flores Azul', 'stock' => 3], // Bajo stock
                ['id' => 302, 'talle' => '39/40', 'color' => 'Hojas Verde', 'stock' => 5], // Bajo stock
            ],
        ],
        [
            'id' => 4,
            'modelo' => 'Remera Básica Algodón Capioví',
            'categoria' => 'Textil',
            'precio_minorista' => 12500.0,
            'precio_mayorista' => 8900.0,
            'stock_total' => 32,
            'variantes' => [
                ['id' => 401, 'talle' => 'S', 'color' => 'Blanco', 'stock' => 12],
                ['id' => 402, 'talle' => 'M', 'color' => 'Gris', 'stock' => 14],
                ['id' => 403, 'talle' => 'L', 'color' => 'Negro', 'stock' => 6], // Bajo stock en talle L
            ],
        ],
        [
            'id' => 5,
            'modelo' => 'Bermuda Playera Secado Rápido',
            'categoria' => 'Textil',
            'precio_minorista' => 16000.0,
            'precio_mayorista' => 11500.0,
            'stock_total' => 7, // Bajo stock general
            'variantes' => [
                ['id' => 501, 'talle' => 'M', 'color' => 'Azul Marino', 'stock' => 4],
                ['id' => 502, 'talle' => 'L', 'color' => 'Negro', 'stock' => 3],
            ],
        ],
    ];

    public function verDetalle(int $productoId): void
    {
        foreach ($this->productos as $p) {
            if ($p['id'] === $productoId) {
                $this->productoDetalle = $p;
                return;
            }
        }
    }

    public function cerrarDetalle(): void
    {
        $this->productoDetalle = null;
    }

    public function abrirFormulario(): void
    {
        $this->reset(['formNombre', 'formPrecioMinorista', 'formPrecioMayorista']);
        $this->formCategoria = 'Calzado';
        $this->formTalle = '39/40';
        $this->formColor = 'Negro';
        $this->formStock = 15;
        $this->mostrarFormulario = true;
    }

    public function cerrarFormulario(): void
    {
        $this->mostrarFormulario = false;
    }

    public function guardarSimulado(): void
    {
        if (trim($this->formNombre) === '') {
            session()->flash('warning', 'Por favor ingresá un nombre de modelo para la simulación.');
            return;
        }

        $nuevoId = count($this->productos) + 1;
        $this->productos[] = [
            'id' => $nuevoId,
            'modelo' => $this->formNombre,
            'categoria' => $this->formCategoria,
            'precio_minorista' => (float)$this->formPrecioMinorista,
            'precio_mayorista' => (float)$this->formPrecioMayorista,
            'stock_total' => (int)$this->formStock,
            'variantes' => [
                [
                    'id' => $nuevoId * 100 + 1,
                    'talle' => $this->formTalle,
                    'color' => $this->formColor,
                    'stock' => (int)$this->formStock,
                ],
            ],
        ];

        $this->mostrarFormulario = false;
        session()->flash('success', '¡Producto simulado agregado exitosamente a la maqueta!');
    }

    public function render()
    {
        $filtrados = array_filter($this->productos, function ($p) {
            // Filtro por búsqueda
            if ($this->buscar !== '' && stripos($p['modelo'], $this->buscar) === false) {
                return false;
            }
            // Filtro por categoría
            if ($this->filtroCategoria !== '' && $p['categoria'] !== $this->filtroCategoria) {
                return false;
            }
            // Filtro por stock (<10 es bajo stock)
            if ($this->filtroStock === 'bajo') {
                $tieneBajoStock = $p['stock_total'] < 10;
                foreach ($p['variantes'] as $v) {
                    if ($v['stock'] < 10) {
                        $tieneBajoStock = true;
                        break;
                    }
                }
                return $tieneBajoStock;
            }

            return true;
        });

        return view('livewire.productos.index', [
            'productosFiltrados' => $filtrados,
        ])->layout('layouts.app', ['title' => 'Productos & Stock']);
    }
}
