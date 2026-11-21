<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Cliente extends Model
{
    public const TIPO_MINORISTA = 'Minorista';
    public const TIPO_MAYORISTA = 'Mayorista';

    /** @var list<string> */
    public const TIPOS = [self::TIPO_MINORISTA, self::TIPO_MAYORISTA];

    protected $fillable = [
        'nombre',
        'tipo',
        'telefono',
        'email',
        'direccion',
        'cuit',
    ];

    public function ventas(): HasMany
    {
        return $this->hasMany(Venta::class);
    }

    public function boletas(): HasMany
    {
        return $this->hasMany(Boleta::class);
    }

    public function cobros(): HasMany
    {
        return $this->hasMany(Cobro::class);
    }

    public function cuentaCorriente(): HasOne
    {
        return $this->hasOne(CuentaCorriente::class);
    }

    public function boletasPendientes(): HasMany
    {
        return $this->hasMany(Boleta::class)->where('saldo', '>', 0)->orderBy('fecha')->orderBy('id');
    }

    public function saldoCuentaCorriente(): float
    {
        $cuenta = $this->relationLoaded('cuentaCorriente')
            ? $this->cuentaCorriente
            : $this->cuentaCorriente()->first();

        if ($cuenta) {
            return round((float) $cuenta->saldo, 2);
        }

        return round((float) $this->boletas()->where('saldo', '>', 0)->sum('saldo'), 2);
    }

    public static function obtenerOCrearCuentaCorriente(int $clienteId): CuentaCorriente
    {
        return CuentaCorriente::query()->firstOrCreate(
            ['cliente_id' => $clienteId],
            ['saldo' => 0]
        );
    }
}
