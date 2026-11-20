<?php

namespace App\Services;

use Carbon\Carbon;

/**
 * Datos ficticios de cuentas corrientes para la fase visual (VIS-005).
 *
 * Lo comparten la pantalla de cuentas corrientes, el formulario de cobro y el
 * aviso de deuda de la pantalla de ventas, para que muestren los mismos saldos.
 * Se reemplaza por consultas reales (modelos Eloquent) en la fase funcional.
 *
 * Convención: una boleta es una venta a cuenta (`total`); `pagado` es lo ya
 * cobrado de esa boleta. La suma de `pagado` coincide con la suma de `pagos`.
 */
class CuentasEjemplo
{
    private const CLIENTES = [
        1 => [
            'nombre' => 'María Fernández',
            'tipo' => 'Minorista',
            'boletas' => [
                ['id' => 'B-0020', 'fecha' => '2026-11-10', 'total' => 9200.0, 'pagado' => 4000.0],
            ],
            'pagos' => [
                ['fecha' => '2026-11-12', 'medio' => 'Efectivo', 'importe' => 4000.0],
            ],
        ],
        3 => [
            'nombre' => 'Almacén Don Rubén',
            'tipo' => 'Mayorista',
            'boletas' => [
                ['id' => 'B-0012', 'fecha' => '2026-10-28', 'total' => 85000.0, 'pagado' => 30000.0],
                ['id' => 'B-0019', 'fecha' => '2026-11-05', 'total' => 42000.0, 'pagado' => 0.0],
            ],
            'pagos' => [
                ['fecha' => '2026-11-02', 'medio' => 'Transferencia', 'importe' => 30000.0],
            ],
        ],
        4 => [
            'nombre' => 'Kiosco El Sol',
            'tipo' => 'Mayorista',
            'boletas' => [
                ['id' => 'B-0015', 'fecha' => '2026-11-02', 'total' => 56000.0, 'pagado' => 56000.0],
            ],
            'pagos' => [
                ['fecha' => '2026-11-10', 'medio' => 'Efectivo', 'importe' => 56000.0],
            ],
        ],
        5 => [
            'nombre' => 'Tienda Moda Selva',
            'tipo' => 'Mayorista',
            'boletas' => [
                ['id' => 'B-0016', 'fecha' => '2026-11-03', 'total' => 120000.0, 'pagado' => 50000.0],
                ['id' => 'B-0021', 'fecha' => '2026-11-12', 'total' => 38500.0, 'pagado' => 0.0],
            ],
            'pagos' => [
                ['fecha' => '2026-11-08', 'medio' => 'Cheque', 'importe' => 50000.0],
            ],
        ],
        7 => [
            'nombre' => 'Bazar Los Pinos',
            'tipo' => 'Mayorista',
            'boletas' => [
                ['id' => 'B-0018', 'fecha' => '2026-11-09', 'total' => 27000.0, 'pagado' => 0.0],
            ],
            'pagos' => [],
        ],
    ];

    // Compras a proveedores con saldo adeudado (coherentes con la pantalla de Proveedores).
    private const PROVEEDORES = [
        1 => [
            'nombre' => 'Calzados Río Paraná S.A.',
            'compras' => [
                ['id' => 'C-2031', 'fecha' => '2026-11-01', 'total' => 210000.0, 'pagado' => 100000.0],
                ['id' => 'C-2044', 'fecha' => '2026-11-14', 'total' => 95000.0, 'pagado' => 0.0],
            ],
        ],
        2 => [
            'nombre' => 'Textil Norte Argentino',
            'compras' => [
                ['id' => 'C-0877', 'fecha' => '2026-11-06', 'total' => 140000.0, 'pagado' => 140000.0],
            ],
        ],
        4 => [
            'nombre' => 'Hilados y Tejidos Misioneros',
            'compras' => [
                ['id' => 'C-1120', 'fecha' => '2026-11-09', 'total' => 64000.0, 'pagado' => 0.0],
            ],
        ],
    ];

    /** Cuentas de clientes indexadas por id de cliente, con saldo y boletas pendientes. */
    public static function cuentasClientes(): array
    {
        $cuentas = [];
        foreach (self::CLIENTES as $id => $c) {
            $pendientes = self::pendientes($c['boletas']);
            $cuentas[$id] = $c + [
                'cliente_id' => $id,
                'pendientes' => $pendientes,
                'saldo' => round(array_sum(array_column($pendientes, 'saldo')), 2),
            ];
        }

        return $cuentas;
    }

    public static function cuentaCliente(int $clienteId): ?array
    {
        return self::cuentasClientes()[$clienteId] ?? null;
    }

    public static function saldoCliente(int $clienteId): float
    {
        return self::cuentaCliente($clienteId)['saldo'] ?? 0.0;
    }

    /** Boletas con saldo mayor a cero, de la más antigua a la más nueva. */
    public static function boletasPendientes(int $clienteId): array
    {
        return self::cuentaCliente($clienteId)['pendientes'] ?? [];
    }

    /**
     * Movimientos de la cuenta (boletas al debe, pagos al haber) con saldo acumulado.
     */
    public static function movimientosCliente(int $clienteId): array
    {
        $cuenta = self::CLIENTES[$clienteId] ?? null;
        if ($cuenta === null) {
            return [];
        }

        $movimientos = [];
        foreach ($cuenta['boletas'] as $b) {
            $movimientos[] = [
                'fecha' => $b['fecha'],
                'detalle' => 'Boleta ' . $b['id'],
                'debe' => $b['total'],
                'haber' => 0.0,
            ];
        }
        foreach ($cuenta['pagos'] as $p) {
            $movimientos[] = [
                'fecha' => $p['fecha'],
                'detalle' => 'Cobro en ' . mb_strtolower($p['medio']),
                'debe' => 0.0,
                'haber' => $p['importe'],
            ];
        }

        usort($movimientos, fn (array $a, array $b) => strcmp($a['fecha'], $b['fecha']));

        $acumulado = 0.0;
        foreach ($movimientos as $i => $m) {
            $acumulado = round($acumulado + $m['debe'] - $m['haber'], 2);
            $movimientos[$i]['saldo'] = $acumulado;
        }

        return $movimientos;
    }

    /** Deudas con proveedores indexadas por id de proveedor, con saldo y compras pendientes. */
    public static function deudasProveedores(): array
    {
        $deudas = [];
        foreach (self::PROVEEDORES as $id => $p) {
            $pendientes = self::pendientes($p['compras']);
            $deudas[$id] = $p + [
                'proveedor_id' => $id,
                'pendientes' => $pendientes,
                'saldo' => round(array_sum(array_column($pendientes, 'saldo')), 2),
            ];
        }

        return $deudas;
    }

    public static function fecha(string $iso): string
    {
        return Carbon::parse($iso)->format('d/m/Y');
    }

    /** Comprobantes con saldo pendiente (total − pagado), ordenados por fecha. */
    private static function pendientes(array $comprobantes): array
    {
        $pendientes = [];
        foreach ($comprobantes as $c) {
            $saldo = round($c['total'] - $c['pagado'], 2);
            if ($saldo > 0) {
                $pendientes[] = $c + ['saldo' => $saldo];
            }
        }
        usort($pendientes, fn (array $a, array $b) => strcmp($a['fecha'], $b['fecha']));

        return $pendientes;
    }
}
