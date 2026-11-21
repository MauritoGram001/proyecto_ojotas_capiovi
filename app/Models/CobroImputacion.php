<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CobroImputacion extends Model
{
    protected $table = 'cobro_imputaciones';

    protected $fillable = [
        'cobro_id',
        'boleta_id',
        'monto',
    ];

    protected function casts(): array
    {
        return [
            'monto' => 'decimal:2',
        ];
    }

    public function cobro(): BelongsTo
    {
        return $this->belongsTo(Cobro::class);
    }

    public function boleta(): BelongsTo
    {
        return $this->belongsTo(Boleta::class);
    }
}
