<?php

namespace App\Livewire\Productos;

use App\Models\Producto;
use App\Models\Variante;
use Livewire\Component;

class Index extends Component
{
    public string $buscar = '';
    public string $filtroCategoria = '';
    public string $filtroStock = '';

    // Estado del modal de detalle (array para compatibilidad con la vista)
    public ?array $productoDetalle = null;

    // Estado del formulario
    public bool $mostrarFormulario = false;
    public string $formNombre = '';
    public string $formCategoria = 'Calzado';
    public float $formPrecioMinorista = 0.0;
    public float $formPrecioMayorista = 0.0;
    public string $formTalle = '39/40';
    public string $formColor = 'Negro';
    public int $formStock = 15;

    /** @return array<string, list<string>> */
    public function rules(): array
    {
        return [
            'formNombre' => ['required', 'string', 'max:255'],
            'formCategoria' => ['required', 'string', 'max:50'],
            'formPrecioMinorista' => ['required', 'numeric', 'min:0'],
            'formPrecioMayorista' => ['required', 'numeric', 'min:0'],
            'formTalle' => ['required', 'string', 'max:30'],
            'formColor' => ['required', 'string', 'max:50'],
            'formStock' => ['required', 'integer', 'min:0'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'formNombre.required' => 'Por favor ingresá un nombre de modelo para el producto.',
            'formTalle.required' => 'Indicá el talle de la variante inicial.',
            'formColor.required' => 'Indicá el color de la variante inicial.',
        ];
    }

    public function verDetalle(int $productoId): void
    {
        $producto = Producto::with('variantes')->find($productoId);

        if (! $producto) {
            return;
        }

        $this->productoDetalle = $this->mapProducto($producto);
    }

    public function cerrarDetalle(): void
    {
        $this->productoDetalle = null;
    }

    public function abrirFormulario(): void
    {
        $this->reset(['formNombre', 'formPrecioMinorista', 'formPrecioMayorista']);
        $this->resetErrorBag();
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

    public function guardar(): void
    {
        $this->validate();

        $producto = Producto::create([
            'modelo' => trim($this->formNombre),
            'categoria' => $this->formCategoria,
            'precio_minorista' => $this->formPrecioMinorista,
            'precio_mayorista' => $this->formPrecioMayorista,
        ]);

        Variante::create([
            'producto_id' => $producto->id,
            'talle' => trim($this->formTalle),
            'color' => trim($this->formColor),
            'stock' => $this->formStock,
        ]);

        $this->mostrarFormulario = false;
        session()->flash('success', 'Producto guardado correctamente con su variante inicial.');
    }

    /** @return array<string, mixed> */
    private function mapProducto(Producto $producto): array
    {
        $variantes = $producto->variantes->map(fn (Variante $v) => [
            'id' => $v->id,
            'talle' => $v->talle,
            'color' => $v->color,
            'stock' => $v->stock,
        ])->all();

        return [
            'id' => $producto->id,
            'modelo' => $producto->modelo,
            'categoria' => $producto->categoria,
            'precio_minorista' => (float) $producto->precio_minorista,
            'precio_mayorista' => (float) $producto->precio_mayorista,
            'stock_total' => $producto->variantes->sum('stock'),
            'variantes' => $variantes,
        ];
    }

    public function render()
    {
        $productos = Producto::query()
            ->with('variantes')
            ->when($this->buscar !== '', fn ($q) => $q->where('modelo', 'like', '%'.$this->buscar.'%'))
            ->when($this->filtroCategoria !== '', fn ($q) => $q->where('categoria', $this->filtroCategoria))
            ->when($this->filtroStock === 'bajo', function ($q): void {
                $q->where(function ($q): void {
                    $q->whereHas('variantes', fn ($v) => $v->where('stock', '<', 10))
                        ->orWhereRaw('(select coalesce(sum(stock),0) from variantes where variantes.producto_id = productos.id) < 10');
                });
            })
            ->orderBy('modelo')
            ->get()
            ->map(fn (Producto $p) => $this->mapProducto($p))
            ->all();

        return view('livewire.productos.index', [
            'productosFiltrados' => $productos,
        ])->layout('layouts.app', ['title' => 'Productos & Stock']);
    }
}
