<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Producto extends Model
{
    protected $fillable = [
        'modelo',
        'categoria',
        'precio_minorista',
        'precio_mayorista',
    ];

    protected function casts(): array
    {
        return [
            'precio_minorista' => 'decimal:2',
            'precio_mayorista' => 'decimal:2',
        ];
    }

    public function variantes(): HasMany
    {
        return $this->hasMany(Variante::class);
    }

    /**
     * Consulta existencias por modelo, talle y/o color con integridad referencial.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, Variante>
     */
    public static function consultarStock(?string $modelo = null, ?string $talle = null, ?string $color = null)
    {
        $query = Variante::query()->with('producto');

        if ($modelo !== null && $modelo !== '') {
            $query->whereHas('producto', fn ($q) => $q->where('modelo', 'like', '%'.$modelo.'%'));
        }

        if ($talle !== null && $talle !== '') {
            $query->where('talle', $talle);
        }

        if ($color !== null && $color !== '') {
            $query->where('color', $color);
        }

        return $query->orderBy('producto_id')->orderBy('talle')->get();
    }

    /**
     * Registra un ingreso de mercadería de forma atómica.
     *
     * @param  array<int, int>  $cantidadesPorVariante  [variante_id => cantidad]
     */
    public static function registrarIngreso(array $cantidadesPorVariante): void
    {
        DB::transaction(function () use ($cantidadesPorVariante): void {
            foreach ($cantidadesPorVariante as $varianteId => $cantidad) {
                Variante::ajustarStockAtomico((int) $varianteId, (int) $cantidad);
            }
        });
    }
}
