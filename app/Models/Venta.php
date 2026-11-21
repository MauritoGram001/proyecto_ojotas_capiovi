<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Venta registrada (comprobante interno no fiscal).
 */
class Venta extends Model
{
    protected $table = 'ventas';

    protected $fillable = [
        'numero',
        'cliente_id',
        'nombre_comprador',
        'modo_precio',
        'subtotal',
        'descuento_total',
        'total',
        'precio_modificado_manual',
        'estado',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'descuento_total' => 'decimal:2',
            'total' => 'decimal:2',
            'precio_modificado_manual' => 'boolean',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(VentaItem::class);
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Datos listos para la vista/PDF del comprobante interno.
     *
     * @return array<string, mixed>
     */
    public function toComprobanteArray(): array
    {
        $this->loadMissing('items');

        return [
            'id' => $this->id,
            'numero' => $this->numero,
            'fecha' => $this->created_at ? $this->created_at->format('d/m/Y H:i') : now()->format('d/m/Y H:i'),
            'cliente' => $this->nombre_comprador,
            'modo' => $this->modo_precio,
            'items' => $this->items->map(fn (VentaItem $item) => [
                'modelo' => $item->modelo,
                'talle' => $item->talle,
                'color' => $item->color,
                'cantidad' => $item->cantidad,
                'precio_unitario' => (float) $item->precio_unitario,
                'descuento' => (float) $item->descuento_porcentaje,
                'subtotal' => (float) $item->subtotal,
            ])->all(),
            'subtotal' => (float) $this->subtotal,
            'descuento' => (float) $this->descuento_total,
            'total' => (float) $this->total,
        ];
    }

    public static function generarNumero(): string
    {
        $ultimo = self::query()->orderByDesc('id')->value('numero');
        $siguiente = 1;

        if (is_string($ultimo) && preg_match('/(\d+)$/', $ultimo, $m)) {
            $siguiente = ((int) $m[1]) + 1;
        } elseif ($ultimo !== null) {
            $siguiente = ((int) self::query()->max('id')) + 1;
        }

        return 'INT-'.str_pad((string) $siguiente, 4, '0', STR_PAD_LEFT);
    }
}
