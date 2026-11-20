<?php

namespace App\Livewire\Ventas;

use App\Livewire\Clientes\Index as ClientesIndex;
use Livewire\Component;

/**
 * Pantalla de venta (POS simplificado) para mostrador y celular.
 *
 * Fase visual (VIS-004): trabaja solo con datos ficticios y estado en memoria.
 * No descuenta stock, no guarda ventas ni genera un PDF real.
 */
class Crear extends Component
{
    public const LISTA_MINORISTA = 'Minorista';
    public const LISTA_MAYORISTA = 'Mayorista';

    // Catálogo ficticio, coherente con la pantalla de Productos (VIS-002).
    private const CATALOGO = [
        [
            'modelo' => 'Ojotas Clásicas Capioví',
            'precio_minorista' => 6500.0,
            'precio_mayorista' => 4500.0,
            'variantes' => [
                ['id' => 101, 'talle' => '37/38', 'color' => 'Negro', 'stock' => 5],
                ['id' => 102, 'talle' => '39/40', 'color' => 'Azul', 'stock' => 4],
                ['id' => 103, 'talle' => '41/42', 'color' => 'Negro', 'stock' => 9],
            ],
        ],
        [
            'modelo' => 'Sandalias Verano Confort',
            'precio_minorista' => 9200.0,
            'precio_mayorista' => 6800.0,
            'variantes' => [
                ['id' => 201, 'talle' => '35/36', 'color' => 'Blanco', 'stock' => 15],
                ['id' => 202, 'talle' => '37/38', 'color' => 'Beige', 'stock' => 20],
                ['id' => 203, 'talle' => '39/40', 'color' => 'Negro', 'stock' => 10],
            ],
        ],
        [
            'modelo' => 'Ojotas Playeras Estampadas',
            'precio_minorista' => 7800.0,
            'precio_mayorista' => 5400.0,
            'variantes' => [
                ['id' => 301, 'talle' => '37/38', 'color' => 'Flores Azul', 'stock' => 3],
                ['id' => 302, 'talle' => '39/40', 'color' => 'Hojas Verde', 'stock' => 5],
            ],
        ],
        [
            'modelo' => 'Remera Básica Algodón Capioví',
            'precio_minorista' => 12500.0,
            'precio_mayorista' => 8900.0,
            'variantes' => [
                ['id' => 401, 'talle' => 'S', 'color' => 'Blanco', 'stock' => 12],
                ['id' => 402, 'talle' => 'M', 'color' => 'Gris', 'stock' => 14],
                ['id' => 403, 'talle' => 'L', 'color' => 'Negro', 'stock' => 6],
            ],
        ],
        [
            'modelo' => 'Bermuda Playera Secado Rápido',
            'precio_minorista' => 16000.0,
            'precio_mayorista' => 11500.0,
            'variantes' => [
                ['id' => 501, 'talle' => 'M', 'color' => 'Azul Marino', 'stock' => 4],
                ['id' => 502, 'talle' => 'L', 'color' => 'Negro', 'stock' => 3],
            ],
        ],
    ];

    // Clientes ficticios, coherentes con la pantalla de Clientes (VIS-003).
    private const CLIENTES = [
        ['id' => 1, 'nombre' => 'María Fernández', 'tipo' => 'Minorista'],
        ['id' => 2, 'nombre' => 'Lucas Benítez', 'tipo' => 'Minorista'],
        ['id' => 3, 'nombre' => 'Almacén Don Rubén', 'tipo' => 'Mayorista'],
        ['id' => 4, 'nombre' => 'Kiosco El Sol', 'tipo' => 'Mayorista'],
        ['id' => 5, 'nombre' => 'Tienda Moda Selva', 'tipo' => 'Mayorista'],
        ['id' => 6, 'nombre' => 'Carolina Acosta', 'tipo' => 'Minorista'],
        ['id' => 7, 'nombre' => 'Bazar Los Pinos', 'tipo' => 'Mayorista'],
    ];

    // Cliente y lista de precios de la venta en curso
    public string $clienteId = ClientesIndex::CONSUMIDOR_FINAL;
    public string $lista = self::LISTA_MINORISTA;

    // Formulario para agregar una línea (los inputs numéricos pueden llegar vacíos)
    public string $varianteSeleccionada = '';
    public $cantidadNueva = 1;

    // Líneas de la venta: variante, cantidad y precio manual opcional
    public array $lineas = [];

    // Descuento general de la venta, en porcentaje (texto: el input puede quedar vacío)
    public string $descuentoPct = '';

    // Número simulado y mensajes dentro del componente
    public int $numeroVenta = 1;
    public string $mensajeExito = '';
    public string $mensajeInfo = '';

    public function updatedClienteId(): void
    {
        // La lista se sugiere según el tipo de cliente, pero el usuario puede cambiarla.
        $cliente = $this->clienteSeleccionado();
        $this->lista = ($cliente && $cliente['tipo'] === 'Mayorista')
            ? self::LISTA_MAYORISTA
            : self::LISTA_MINORISTA;
    }

    public function agregarLinea(): void
    {
        $this->mensajeExito = '';
        $this->mensajeInfo = '';

        $this->validate([
            'varianteSeleccionada' => ['required'],
            'cantidadNueva' => ['required', 'integer', 'min:1', 'max:999'],
        ], [
            'varianteSeleccionada.required' => 'Elegí un producto (modelo, talle y color).',
            'cantidadNueva.required' => 'Ingresá la cantidad.',
            'cantidadNueva.integer' => 'La cantidad debe ser un número entero.',
            'cantidadNueva.min' => 'La cantidad mínima es 1.',
            'cantidadNueva.max' => 'La cantidad máxima por línea es 999.',
        ]);

        $varianteId = (int) $this->varianteSeleccionada;
        if (! isset($this->variantes()[$varianteId])) {
            $this->addError('varianteSeleccionada', 'El producto elegido no existe en el catálogo de ejemplo.');
            return;
        }

        $cantidad = (int) $this->cantidadNueva;
        foreach ($this->lineas as $i => $linea) {
            if ($linea['variante_id'] === $varianteId) {
                $this->lineas[$i]['cantidad'] = min(999, $this->lineas[$i]['cantidad'] + $cantidad);
                $this->reset(['varianteSeleccionada']);
                $this->cantidadNueva = 1;
                return;
            }
        }

        $this->lineas[] = [
            'variante_id' => $varianteId,
            'cantidad' => $cantidad,
            'precio_manual' => '',
            'editando' => false,
        ];
        $this->reset(['varianteSeleccionada']);
        $this->cantidadNueva = 1;
    }

    public function cambiarCantidad(int $indice, int $delta): void
    {
        if (! isset($this->lineas[$indice])) {
            return;
        }

        $nueva = $this->lineas[$indice]['cantidad'] + $delta;
        $this->lineas[$indice]['cantidad'] = max(1, min(999, $nueva));
    }

    public function quitarLinea(int $indice): void
    {
        if (! isset($this->lineas[$indice])) {
            return;
        }

        unset($this->lineas[$indice]);
        $this->lineas = array_values($this->lineas);
    }

    public function alternarEdicionPrecio(int $indice): void
    {
        if (isset($this->lineas[$indice])) {
            $this->lineas[$indice]['editando'] = ! $this->lineas[$indice]['editando'];
        }
    }

    public function restablecerPrecio(int $indice): void
    {
        if (isset($this->lineas[$indice])) {
            $this->lineas[$indice]['precio_manual'] = '';
            $this->lineas[$indice]['editando'] = false;
        }
    }

    public function confirmarVentaSimulada(): void
    {
        if (count($this->lineas) === 0) {
            $this->mensajeExito = '';
            $this->mensajeInfo = 'Agregá al menos un producto para confirmar la venta.';
            return;
        }

        $numero = $this->numeroVenta;
        $this->reset(['lineas', 'descuentoPct', 'varianteSeleccionada']);
        $this->cantidadNueva = 1;
        $this->clienteId = ClientesIndex::CONSUMIDOR_FINAL;
        $this->lista = self::LISTA_MINORISTA;
        $this->numeroVenta = $numero + 1;
        $this->mensajeInfo = '';
        $this->mensajeExito = 'Venta N.º ' . $this->formatearNumero($numero)
            . ' simulada. No se guardó ni se descontó stock.';
    }

    public function descargarPdfSimulado(): void
    {
        if (count($this->lineas) === 0) {
            $this->mensajeExito = '';
            $this->mensajeInfo = 'Agregá productos para previsualizar el comprobante.';
            return;
        }

        // Se deja el comprobante actual en sesión y se abre la plantilla PDF en otra pestaña.
        session(['comprobante_simulado' => $this->comprobante()]);
        $this->mensajeExito = '';
        $this->mensajeInfo = 'Simulación: se abrió la plantilla del comprobante. En la versión funcional se descargará como PDF.';
        $this->js("window.open('" . route('ventas.comprobante-simulado') . "', '_blank')");
    }

    /**
     * Formatea un importe en pesos con separadores argentinos (sin decimales si es entero).
     */
    public static function dinero(float $valor): string
    {
        $decimales = abs($valor - round($valor)) < 0.005 ? 0 : 2;

        return '$' . number_format($valor, $decimales, ',', '.');
    }

    private function formatearNumero(int $numero): string
    {
        return '0001-' . str_pad((string) $numero, 8, '0', STR_PAD_LEFT);
    }

    /**
     * Variantes del catálogo en una lista plana, indexada por id.
     */
    private function variantes(): array
    {
        $plano = [];
        foreach (self::CATALOGO as $producto) {
            foreach ($producto['variantes'] as $v) {
                $plano[$v['id']] = $v + [
                    'modelo' => $producto['modelo'],
                    'precio_minorista' => $producto['precio_minorista'],
                    'precio_mayorista' => $producto['precio_mayorista'],
                ];
            }
        }

        return $plano;
    }

    private function clienteSeleccionado(): ?array
    {
        foreach (self::CLIENTES as $c) {
            if ((string) $c['id'] === $this->clienteId) {
                return $c;
            }
        }

        return null;
    }

    private function descuentoNormalizado(): float
    {
        $texto = str_replace(',', '.', trim($this->descuentoPct));
        if ($texto === '' || ! is_numeric($texto)) {
            return 0.0;
        }

        return max(0.0, min(100.0, (float) $texto));
    }

    /**
     * Líneas con precio resuelto y totales de la venta en curso.
     */
    private function resumen(): array
    {
        $variantes = $this->variantes();
        $campoPrecio = $this->lista === self::LISTA_MAYORISTA ? 'precio_mayorista' : 'precio_minorista';

        $detalle = [];
        $subtotal = 0.0;
        $hayPrecioManual = false;

        foreach ($this->lineas as $i => $linea) {
            $v = $variantes[$linea['variante_id']] ?? null;
            if ($v === null) {
                continue;
            }

            $manual = $linea['precio_manual'];
            $esManual = is_numeric($manual) && (float) $manual > 0;
            $unitario = $esManual ? (float) $manual : $v[$campoPrecio];
            $importe = round($unitario * $linea['cantidad'], 2);

            $hayPrecioManual = $hayPrecioManual || $esManual;
            $subtotal += $importe;

            $detalle[] = [
                'indice' => $i,
                'variante_id' => $v['id'],
                'modelo' => $v['modelo'],
                'talle' => $v['talle'],
                'color' => $v['color'],
                'stock' => $v['stock'],
                'cantidad' => $linea['cantidad'],
                'precio_lista' => $v[$campoPrecio],
                'precio_unitario' => $unitario,
                'precio_manual' => $esManual,
                'editando' => $linea['editando'],
                'subtotal' => $importe,
            ];
        }

        $pct = $this->descuentoNormalizado();
        $subtotal = round($subtotal, 2);
        $descuento = round($subtotal * $pct / 100, 2);

        return [
            'lineas' => $detalle,
            'subtotal' => $subtotal,
            'descuento_pct' => $pct,
            'descuento_monto' => $descuento,
            'total' => round($subtotal - $descuento, 2),
            'hay_precio_manual' => $hayPrecioManual,
        ];
    }

    /**
     * Datos del comprobante interno no fiscal de la venta en curso.
     */
    private function comprobante(): array
    {
        $resumen = $this->resumen();
        $cliente = $this->clienteSeleccionado();

        return [
            'numero' => $this->formatearNumero($this->numeroVenta) . ' (simulado)',
            'fecha' => now()->format('d/m/Y H:i'),
            'cliente' => $cliente ? $cliente['nombre'] . ' (' . $cliente['tipo'] . ')' : 'Consumidor final',
            'lista' => $this->lista,
        ] + $resumen;
    }

    public function render()
    {
        return view('livewire.ventas.crear', [
            'catalogo' => self::CATALOGO,
            'clientes' => self::CLIENTES,
            'resumen' => $this->resumen(),
            'comprobante' => $this->comprobante(),
            'clienteActual' => $this->clienteSeleccionado(),
        ])->layout('layouts.app', ['title' => 'Nueva Venta']);
    }
}
