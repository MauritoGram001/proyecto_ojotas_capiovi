<?php

namespace App\Livewire\Ventas;

use App\Models\Boleta;
use App\Models\Cliente;
use App\Models\CuentaCorriente;
use App\Models\Variante;
use App\Models\Venta;
use App\Models\VentaItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use InvalidArgumentException;
use Livewire\Component;

class Crear extends Component
{
    public string $compradorId = 'final';
    public string $nombreComprador = 'Consumidor Final (sin identificar)';
    public string $modoPrecio = 'minorista';
    public string $productoSeleccionado = '';

    public array $lineas = [];

    public bool $mostrarComprobante = false;
    public string $numeroComprobante = 'INT-PREVIA';
    public ?int $ventaRegistradaId = null;
    public ?string $mensajeEstado = null;

    public function updatedCompradorId(): void
    {
        if ($this->compradorId === 'final') {
            $this->nombreComprador = 'Consumidor Final (sin identificar)';
            $this->modoPrecio = 'minorista';

            return;
        }

        $cliente = is_numeric($this->compradorId)
            ? Cliente::find((int) $this->compradorId)
            : null;

        if (! $cliente) {
            $this->compradorId = 'final';
            $this->nombreComprador = 'Consumidor Final (sin identificar)';
            $this->modoPrecio = 'minorista';

            return;
        }

        $this->nombreComprador = $cliente->nombre;
        $this->modoPrecio = $cliente->tipo === Cliente::TIPO_MAYORISTA ? 'mayorista' : 'minorista';
    }

    public function getDeudaActualProperty(): float
    {
        if ($this->compradorId === 'final' || ! is_numeric($this->compradorId)) {
            return 0.0;
        }

        $cliente = Cliente::with('cuentaCorriente')->find((int) $this->compradorId);

        return $cliente ? $cliente->saldoCuentaCorriente() : 0.0;
    }

    public function updatedModoPrecio(): void
    {
        // Solo se recalculan las líneas cuyo precio no fue modificado manualmente
        foreach ($this->lineas as $index => $linea) {
            if ((float) $linea['precio_unitario'] !== (float) $linea['precio_lista']) {
                continue;
            }

            $nuevoPrecio = $this->precioPara($linea['producto_id']);
            if ($nuevoPrecio !== null) {
                $this->lineas[$index]['precio_lista'] = $nuevoPrecio;
                $this->lineas[$index]['precio_unitario'] = $nuevoPrecio;
            }
        }
    }

    /**
     * RF 19: la autorización se verifica en servidor aunque los botones
     * estén visibles. No se bloquea a ningún usuario autenticado.
     */
    public function updated(string $propertyName, mixed $value): void
    {
        if (str_contains($propertyName, 'precio_unitario')) {
            Gate::authorize('cambiarPrecio', Venta::class);
        }

        if (str_contains($propertyName, 'descuento')) {
            Gate::authorize('aplicarDescuento', Venta::class);
        }
    }

    public function agregarLinea(): void
    {
        if ($this->ventaRegistradaId !== null) {
            $this->mensajeEstado = 'warning:La venta ya fue registrada. Iniciá una nueva venta para agregar productos.';

            return;
        }

        if ($this->productoSeleccionado === '') {
            $this->mensajeEstado = 'warning:Seleccioná una variante de producto para agregarla a la venta.';

            return;
        }

        $variante = Variante::with('producto')->find((int) $this->productoSeleccionado);

        if (! $variante || ! $variante->producto) {
            $this->mensajeEstado = 'warning:La variante seleccionada ya no está disponible.';

            return;
        }

        $precio = $this->modoPrecio === 'mayorista'
            ? (float) $variante->producto->precio_mayorista
            : (float) $variante->producto->precio_minorista;

        foreach ($this->lineas as $index => $linea) {
            if ($linea['producto_id'] === $variante->id) {
                $this->lineas[$index]['cantidad'] = (int) $linea['cantidad'] + 1;

                return;
            }
        }

        $this->lineas[] = [
            'producto_id' => $variante->id,
            'modelo' => $variante->producto->modelo,
            'talle' => $variante->talle,
            'color' => $variante->color,
            'precio_lista' => $precio,
            'precio_unitario' => $precio,
            'cantidad' => 1,
            'descuento' => 0.0,
        ];
    }

    public function quitarLinea(int $index): void
    {
        if ($this->ventaRegistradaId !== null) {
            return;
        }

        unset($this->lineas[$index]);
        $this->lineas = array_values($this->lineas);
    }

    public function verComprobante(): void
    {
        if (count($this->lineas) === 0) {
            $this->mensajeEstado = 'warning:Agregá al menos una variante antes de previsualizar el comprobante.';

            return;
        }

        $this->mostrarComprobante = true;
    }

    public function cerrarComprobante(): void
    {
        $this->mostrarComprobante = false;
    }

    public function confirmarVenta(): void
    {
        $this->mensajeEstado = null;

        if ($this->ventaRegistradaId !== null) {
            $this->mensajeEstado = 'warning:Esta venta ya fue registrada. Podés descargar su comprobante.';

            return;
        }

        if (count($this->lineas) === 0) {
            $this->mensajeEstado = 'warning:Agregá al menos una variante antes de registrar la venta.';

            return;
        }

        try {
            $venta = DB::transaction(function (): Venta {
                $subtotal = 0.0;
                $descuentoTotal = 0.0;
                $total = 0.0;
                $hayPrecioManual = false;
                $items = [];

                // Bloquear variantes en orden estable para consistencia transaccional.
                $ids = array_values(array_unique(array_map(fn ($l) => (int) $l['producto_id'], $this->lineas)));
                sort($ids);
                $variantes = Variante::query()
                    ->whereIn('id', $ids)
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                foreach ($this->lineas as $linea) {
                    $vid = (int) $linea['producto_id'];
                    $cantidad = max(1, (int) $linea['cantidad']);
                    $precioLista = round((float) $linea['precio_lista'], 2);
                    $precioUnitario = round((float) $linea['precio_unitario'], 2);
                    $descuentoPct = max(0.0, min(100.0, (float) $linea['descuento']));

                    /** @var Variante|null $variante */
                    $variante = $variantes->get($vid);
                    if (! $variante) {
                        throw new InvalidArgumentException('Una de las variantes ya no está disponible.');
                    }
                    if ($variante->stock < $cantidad) {
                        throw new InvalidArgumentException('No hay stock suficiente para '.$linea['modelo'].' ('.$linea['talle'].'/'.$linea['color'].').');
                    }

                    $bruto = round($precioUnitario * $cantidad, 2);
                    $descuentoLinea = round($bruto * ($descuentoPct / 100), 2);
                    $subtotalLinea = round($bruto - $descuentoLinea, 2);

                    $subtotal += $bruto;
                    $descuentoTotal += $descuentoLinea;
                    $total += $subtotalLinea;

                    if (abs($precioUnitario - $precioLista) > 0.009) {
                        $hayPrecioManual = true;
                    }

                    $variante->stock = $variante->stock - $cantidad;
                    $variante->save();

                    $items[] = [
                        'variante_id' => $vid,
                        'modelo' => (string) $linea['modelo'],
                        'talle' => (string) $linea['talle'],
                        'color' => (string) $linea['color'],
                        'cantidad' => $cantidad,
                        'precio_lista' => $precioLista,
                        'precio_unitario' => $precioUnitario,
                        'descuento_porcentaje' => $descuentoPct,
                        'subtotal' => $subtotalLinea,
                    ];
                }

                $clienteId = null;
                if ($this->compradorId !== 'final' && is_numeric($this->compradorId)) {
                    $cliente = Cliente::find((int) $this->compradorId);
                    $clienteId = $cliente?->id;
                }

                $venta = Venta::create([
                    'numero' => 'TMP-'.(string) \Illuminate\Support\Str::uuid(),
                    'cliente_id' => $clienteId,
                    'nombre_comprador' => $clienteId ? $this->nombreComprador : 'Consumidor Final (sin identificar)',
                    'modo_precio' => $this->modoPrecio === 'mayorista' ? 'mayorista' : 'minorista',
                    'subtotal' => round($subtotal, 2),
                    'descuento_total' => round($descuentoTotal, 2),
                    'total' => round($total, 2),
                    'precio_modificado_manual' => $hayPrecioManual,
                    'estado' => 'Confirmada',
                    'user_id' => auth()->id(),
                ]);

                foreach ($items as $item) {
                    $venta->items()->create($item);
                }

                $venta->numero = 'INT-'.str_pad((string) $venta->id, 4, '0', STR_PAD_LEFT);
                $venta->save();

                // FUN-005: venta a cliente identificado genera boleta pendiente y actualiza saldo.
                if ($clienteId) {
                    Boleta::query()->create([
                        'numero' => Boleta::generarNumero(),
                        'cliente_id' => $clienteId,
                        'venta_id' => $venta->id,
                        'concepto' => 'Venta '.$venta->numero,
                        'monto' => round($total, 2),
                        'saldo' => round($total, 2),
                        'estado' => 'Pendiente',
                        'fecha' => now()->toDateString(),
                    ]);

                    $cuenta = CuentaCorriente::query()
                        ->where('cliente_id', $clienteId)
                        ->lockForUpdate()
                        ->first();

                    if ($cuenta) {
                        $cuenta->saldo = round((float) $cuenta->saldo + round($total, 2), 2);
                        $cuenta->save();
                    } else {
                        CuentaCorriente::query()->create([
                            'cliente_id' => $clienteId,
                            'saldo' => round($total, 2),
                        ]);
                    }
                }

                return $venta;
            });
        } catch (InvalidArgumentException $e) {
            $this->mensajeEstado = 'warning:'.$e->getMessage();

            return;
        } catch (\Throwable $e) {
            $this->mensajeEstado = 'warning:No se pudo registrar la venta: '.$e->getMessage();

            return;
        }

        $this->ventaRegistradaId = $venta->id;
        $this->numeroComprobante = $venta->numero;
        $this->mostrarComprobante = true;
        $this->productoSeleccionado = '';
        $this->mensajeEstado = 'success:'.$venta->numero;
    }

    public function descargarComprobante(): void
    {
        if ($this->ventaRegistradaId === null) {
            $this->mensajeEstado = 'warning:Registrá la venta para generar y descargar el comprobante interno (PDF).';

            return;
        }

        $this->redirectRoute('ventas.comprobante', ['venta' => $this->ventaRegistradaId]);
    }

    public function nuevaVenta(): void
    {
        $this->reset([
            'compradorId',
            'nombreComprador',
            'modoPrecio',
            'productoSeleccionado',
            'lineas',
            'mostrarComprobante',
            'numeroComprobante',
            'ventaRegistradaId',
            'mensajeEstado',
        ]);
        $this->compradorId = 'final';
        $this->nombreComprador = 'Consumidor Final (sin identificar)';
        $this->modoPrecio = 'minorista';
        $this->numeroComprobante = 'INT-PREVIA';
        $this->resetErrorBag();
    }

    private function precioPara(int $productoId): ?float
    {
        $variante = Variante::with('producto')->find($productoId);

        if (! $variante || ! $variante->producto) {
            return null;
        }

        return $this->modoPrecio === 'mayorista'
            ? (float) $variante->producto->precio_mayorista
            : (float) $variante->producto->precio_minorista;
    }

    /** @return array<int, array<string, mixed>> */
    private function catalogo(): array
    {
        return Variante::query()
            ->with('producto')
            ->orderBy('producto_id')
            ->orderBy('talle')
            ->get()
            ->map(fn (Variante $v) => [
                'id' => $v->id,
                'modelo' => $v->producto?->modelo ?? '',
                'talle' => $v->talle,
                'color' => $v->color,
                'precio_minorista' => (float) ($v->producto?->precio_minorista ?? 0),
                'precio_mayorista' => (float) ($v->producto?->precio_mayorista ?? 0),
                'stock' => $v->stock,
            ])
            ->all();
    }

    public function render()
    {
        $subtotal = 0.0;
        $descuentoTotal = 0.0;
        $total = 0.0;
        $hayPrecioManual = false;

        foreach ($this->lineas as $linea) {
            $bruto = (float) $linea['precio_unitario'] * (int) $linea['cantidad'];
            $descuento = $bruto * ((float) $linea['descuento'] / 100);
            $subtotal += $bruto;
            $descuentoTotal += $descuento;
            $total += $bruto - $descuento;

            if ((float) $linea['precio_unitario'] !== (float) $linea['precio_lista']) {
                $hayPrecioManual = true;
            }
        }

        $comprobante = null;
        if ($this->ventaRegistradaId !== null) {
            $venta = Venta::with('items')->find($this->ventaRegistradaId);
            if ($venta) {
                $comprobante = $venta->toComprobanteArray();
                $hayPrecioManual = (bool) $venta->precio_modificado_manual;
            }
        }

        if ($comprobante === null) {
            $comprobante = [
                'numero' => $this->numeroComprobante,
                'fecha' => now()->format('d/m/Y H:i'),
                'cliente' => $this->nombreComprador,
                'modo' => $this->modoPrecio,
                'items' => $this->lineas,
                'subtotal' => $subtotal,
                'descuento' => $descuentoTotal,
                'total' => $total,
            ];
        }

        return view('livewire.ventas.crear', [
            'clientes' => Cliente::query()->orderBy('nombre')->get(['id', 'nombre', 'tipo']),
            'catalogo' => $this->catalogo(),
            'totales' => [
                'subtotal' => $subtotal,
                'descuento' => $descuentoTotal,
                'total' => $total,
            ],
            'hayPrecioManual' => $hayPrecioManual,
            'comprobante' => $comprobante,
            'ventaRegistrada' => $this->ventaRegistradaId !== null,
        ])->layout('layouts.app', ['title' => 'Nueva Venta']);
    }
}
