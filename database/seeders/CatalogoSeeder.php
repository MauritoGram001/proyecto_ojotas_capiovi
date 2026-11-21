<?php

namespace Database\Seeders;

use App\Models\Producto;
use App\Models\Variante;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatalogoSeeder extends Seeder
{
    public function run(): void
    {
        $productos = [
            [
                'id' => 1,
                'modelo' => 'Ojotas Clásicas Capioví',
                'categoria' => 'Calzado',
                'precio_minorista' => 6500.00,
                'precio_mayorista' => 4500.00,
                'variantes' => [
                    ['id' => 101, 'talle' => '37/38', 'color' => 'Negro', 'stock' => 5],
                    ['id' => 102, 'talle' => '39/40', 'color' => 'Azul', 'stock' => 4],
                    ['id' => 103, 'talle' => '41/42', 'color' => 'Negro', 'stock' => 9],
                ],
            ],
            [
                'id' => 2,
                'modelo' => 'Sandalias Verano Confort',
                'categoria' => 'Calzado',
                'precio_minorista' => 9200.00,
                'precio_mayorista' => 6800.00,
                'variantes' => [
                    ['id' => 201, 'talle' => '35/36', 'color' => 'Blanco', 'stock' => 15],
                    ['id' => 202, 'talle' => '37/38', 'color' => 'Beige', 'stock' => 20],
                    ['id' => 203, 'talle' => '39/40', 'color' => 'Negro', 'stock' => 10],
                ],
            ],
            [
                'id' => 3,
                'modelo' => 'Ojotas Playeras Estampadas',
                'categoria' => 'Calzado',
                'precio_minorista' => 7800.00,
                'precio_mayorista' => 5400.00,
                'variantes' => [
                    ['id' => 301, 'talle' => '37/38', 'color' => 'Flores Azul', 'stock' => 3],
                    ['id' => 302, 'talle' => '39/40', 'color' => 'Hojas Verde', 'stock' => 5],
                ],
            ],
            [
                'id' => 4,
                'modelo' => 'Remera Básica Algodón Capioví',
                'categoria' => 'Textil',
                'precio_minorista' => 12500.00,
                'precio_mayorista' => 8900.00,
                'variantes' => [
                    ['id' => 401, 'talle' => 'S', 'color' => 'Blanco', 'stock' => 12],
                    ['id' => 402, 'talle' => 'M', 'color' => 'Gris', 'stock' => 14],
                    ['id' => 403, 'talle' => 'L', 'color' => 'Negro', 'stock' => 6],
                ],
            ],
            [
                'id' => 5,
                'modelo' => 'Bermuda Playera Secado Rápido',
                'categoria' => 'Textil',
                'precio_minorista' => 16000.00,
                'precio_mayorista' => 11500.00,
                'variantes' => [
                    ['id' => 501, 'talle' => 'M', 'color' => 'Azul Marino', 'stock' => 4],
                    ['id' => 502, 'talle' => 'L', 'color' => 'Negro', 'stock' => 3],
                ],
            ],
        ];

        foreach ($productos as $data) {
            $variantes = $data['variantes'];
            unset($data['variantes']);

            $producto = Producto::updateOrCreate(['id' => $data['id']], $data);

            foreach ($variantes as $variante) {
                Variante::updateOrCreate(
                    ['id' => $variante['id']],
                    $variante + ['producto_id' => $producto->id],
                );
            }
        }

        // Ajustar secuencias en SQLite para que nuevos registros no colisionen con IDs fijos.
        if (DB::getDriverName() === 'sqlite') {
            $maxProducto = (int) Producto::max('id');
            $maxVariante = (int) Variante::max('id');
            DB::statement("INSERT OR REPLACE INTO sqlite_sequence(name, seq) VALUES ('productos', {$maxProducto})");
            DB::statement("INSERT OR REPLACE INTO sqlite_sequence(name, seq) VALUES ('variantes', {$maxVariante})");
        }
    }
}
