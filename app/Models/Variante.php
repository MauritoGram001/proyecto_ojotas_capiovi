<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class Variante extends Model
{
    protected $fillable = [
        'producto_id',
        'talle',
        'color',
        'stock',
    ];

    protected function casts(): array
    {
        return [
            'stock' => 'integer',
        ];
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    public function esBajoStock(int $umbral = 10): bool
    {
        return $this->stock < $umbral;
    }

    /**
     * Actualiza el stock de forma atómica y evita inconsistencias concurrentes.
     * $delta positivo = ingreso; negativo = egreso (venta). No permite stock negativo.
     */
    public static function ajustarStockAtomico(int $varianteId, int $delta): self
    {
        return DB::transaction(function () use ($varianteId, $delta): self {
            /** @var self $variante */
            $variante = self::query()->lockForUpdate()->findOrFail($varianteId);

            $nuevoStock = $variante->stock + $delta;
            if ($nuevoStock < 0) {
                throw new InvalidArgumentException('No hay stock suficiente para la variante seleccionada.');
            }

            $variante->stock = $nuevoStock;
            $variante->save();

            return $variante;
        });
    }

    /**
     * Descuenta stock para una venta de forma atómica (una o varias variantes).
     *
     * @param  array<int, int>  $cantidadesPorVariante  [variante_id => cantidad]
     */
    public static function descontarPorVenta(array $cantidadesPorVariante): void
    {
        DB::transaction(function () use ($cantidadesPorVariante): void {
            foreach ($cantidadesPorVariante as $varianteId => $cantidad) {
                self::ajustarStockAtomico((int) $varianteId, -abs((int) $cantidad));
            }
        });
    }
}
