<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Boleta extends Model
{
    protected $fillable = [
        'numero',
        'cliente_id',
        'venta_id',
        'concepto',
        'monto',
        'saldo',
        'estado',
        'fecha',
    ];

    protected function casts(): array
    {
        return [
            'monto' => 'decimal:2',
            'saldo' => 'decimal:2',
            'fecha' => 'date',
        ];
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class);
    }

    public function imputaciones(): HasMany
    {
        return $this->hasMany(CobroImputacion::class);
    }

    public function actualizarEstado(): void
    {
        $saldo = round((float) $this->saldo, 2);
        $monto = round((float) $this->monto, 2);

        if ($saldo <= 0.0) {
            $this->estado = 'Pagada';
            $this->saldo = 0;
        } elseif ($saldo < $monto) {
            $this->estado = 'Parcial';
        } else {
            $this->estado = 'Pendiente';
        }
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

        return 'B-'.str_pad((string) $siguiente, 4, '0', STR_PAD_LEFT);
    }
}
