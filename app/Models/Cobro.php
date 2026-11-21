<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cobro extends Model
{
    protected $fillable = [
        'cliente_id',
        'user_id',
        'fecha',
        'monto_efectivo',
        'monto_transferencia',
        'monto_cheque',
        'numero_cheque',
        'total',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'monto_efectivo' => 'decimal:2',
            'monto_transferencia' => 'decimal:2',
            'monto_cheque' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function imputaciones(): HasMany
    {
        return $this->hasMany(CobroImputacion::class);
    }
}
