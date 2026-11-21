<?php

namespace Database\Seeders;

use App\Models\Boleta;
use App\Models\Cliente;
use App\Models\CuentaCorriente;
use Illuminate\Database\Seeder;

class CuentaCorrienteSeeder extends Seeder
{
    public function run(): void
    {
        $clientes = Cliente::all();

        foreach ($clientes as $cliente) {
            CuentaCorriente::updateOrCreate(
                ['cliente_id' => $cliente->id],
                ['saldo' => 0]
            );
        }

        $this->crearBoletas(1, [
            ['numero' => 'B-0101', 'fecha' => '2026-11-18', 'monto' => 12500.00, 'concepto' => 'Venta de ojotas'],
            ['numero' => 'B-0102', 'fecha' => '2026-11-19', 'monto' => 6000.00, 'concepto' => 'Reposición de calzado'],
        ]);

        $this->crearBoletas(3, [
            ['numero' => 'B-0103', 'fecha' => '2026-11-15', 'monto' => 94500.00, 'concepto' => 'Pedido mayorista'],
        ]);

        $this->crearBoletas(4, [
            ['numero' => 'B-0104', 'fecha' => '2026-11-17', 'monto' => 32000.00, 'concepto' => 'Pedido mayorista'],
        ]);
    }

    /**
     * @param  array<int, array{numero:string,fecha:string,monto:float,concepto:string}>  $boletas
     */
    private function crearBoletas(int $clienteId, array $boletas): void
    {
        $total = 0.0;

        foreach ($boletas as $data) {
            $boleta = Boleta::updateOrCreate(
                ['numero' => $data['numero']],
                [
                    'cliente_id' => $clienteId,
                    'venta_id' => null,
                    'concepto' => $data['concepto'],
                    'monto' => $data['monto'],
                    'saldo' => $data['monto'],
                    'estado' => 'Pendiente',
                    'fecha' => $data['fecha'],
                ]
            );
            $total += (float) $boleta->saldo;
        }

        CuentaCorriente::updateOrCreate(
            ['cliente_id' => $clienteId],
            ['saldo' => round($total, 2)]
        );
    }
}
