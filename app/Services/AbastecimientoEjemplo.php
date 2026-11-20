<?php

namespace App\Services;

/**
 * Datos ficticios de abastecimiento para la fase visual (VIS-006): pedidos a
 * proveedores y mercadería defectuosa o enviada a cambio.
 *
 * Los proveedores, variantes y compras son coherentes con las pantallas de
 * Productos, Proveedores y Cuentas corrientes. Se reemplaza por modelos
 * Eloquent en la fase funcional.
 */
class AbastecimientoEjemplo
{
    public const PEDIDO_PENDIENTE = 'Pendiente';
    public const PEDIDO_RECIBIDO = 'Recibido';

    public const CAMBIO_DEFECTUOSO = 'Stock defectuoso';
    public const CAMBIO_ENVIADO = 'Enviada a cambio';
    public const CAMBIO_RECIBIDO = 'Reemplazo recibido';

    public const ORIGEN_DEVOLUCION = 'Devolución de cliente';
    public const ORIGEN_STOCK = 'Detectada en stock';

    /** Proveedores indexados por id. */
    public static function proveedores(): array
    {
        return [
            1 => 'Calzados Río Paraná S.A.',
            2 => 'Textil Norte Argentino',
            3 => 'Distribuidora Ojotas del Sur',
            4 => 'Hilados y Tejidos Misioneros',
            5 => 'Importadora Sandalias del Litoral',
        ];
    }

    /** Variantes (modelo, talle y color) indexadas por id, como en el catálogo de Productos. */
    public static function variantes(): array
    {
        return [
            101 => ['modelo' => 'Ojotas Clásicas Capioví', 'talle' => '37/38', 'color' => 'Negro'],
            102 => ['modelo' => 'Ojotas Clásicas Capioví', 'talle' => '39/40', 'color' => 'Azul'],
            103 => ['modelo' => 'Ojotas Clásicas Capioví', 'talle' => '41/42', 'color' => 'Negro'],
            201 => ['modelo' => 'Sandalias Verano Confort', 'talle' => '35/36', 'color' => 'Blanco'],
            202 => ['modelo' => 'Sandalias Verano Confort', 'talle' => '37/38', 'color' => 'Beige'],
            203 => ['modelo' => 'Sandalias Verano Confort', 'talle' => '39/40', 'color' => 'Negro'],
            301 => ['modelo' => 'Ojotas Playeras Estampadas', 'talle' => '37/38', 'color' => 'Flores Azul'],
            302 => ['modelo' => 'Ojotas Playeras Estampadas', 'talle' => '39/40', 'color' => 'Hojas Verde'],
            401 => ['modelo' => 'Remera Básica Algodón Capioví', 'talle' => 'S', 'color' => 'Blanco'],
            402 => ['modelo' => 'Remera Básica Algodón Capioví', 'talle' => 'M', 'color' => 'Gris'],
            403 => ['modelo' => 'Remera Básica Algodón Capioví', 'talle' => 'L', 'color' => 'Negro'],
            501 => ['modelo' => 'Bermuda Playera Secado Rápido', 'talle' => 'M', 'color' => 'Azul Marino'],
            502 => ['modelo' => 'Bermuda Playera Secado Rápido', 'talle' => 'L', 'color' => 'Negro'],
        ];
    }

    /** Pedidos a proveedores; `fecha_recepcion` solo existe en los ya recibidos. */
    public static function pedidos(): array
    {
        return [
            [
                'id' => 'P-0101', 'proveedor_id' => 1, 'estado' => self::PEDIDO_PENDIENTE,
                'fecha' => '2026-11-14', 'entrega_estimada' => '2026-11-24', 'fecha_recepcion' => null,
                'items' => [['variante_id' => 101, 'cantidad' => 30], ['variante_id' => 102, 'cantidad' => 20]],
            ],
            [
                'id' => 'P-0102', 'proveedor_id' => 2, 'estado' => self::PEDIDO_RECIBIDO,
                'fecha' => '2026-11-06', 'entrega_estimada' => '2026-11-13', 'fecha_recepcion' => '2026-11-12',
                'items' => [['variante_id' => 401, 'cantidad' => 40], ['variante_id' => 402, 'cantidad' => 40]],
            ],
            [
                'id' => 'P-0103', 'proveedor_id' => 4, 'estado' => self::PEDIDO_PENDIENTE,
                'fecha' => '2026-11-09', 'entrega_estimada' => '2026-11-20', 'fecha_recepcion' => null,
                'items' => [['variante_id' => 403, 'cantidad' => 25]],
            ],
            [
                'id' => 'P-0104', 'proveedor_id' => 1, 'estado' => self::PEDIDO_RECIBIDO,
                'fecha' => '2026-11-01', 'entrega_estimada' => '2026-11-08', 'fecha_recepcion' => '2026-11-07',
                'items' => [
                    ['variante_id' => 201, 'cantidad' => 24],
                    ['variante_id' => 202, 'cantidad' => 24],
                    ['variante_id' => 203, 'cantidad' => 12],
                ],
            ],
            [
                'id' => 'P-0105', 'proveedor_id' => 5, 'estado' => self::PEDIDO_PENDIENTE,
                'fecha' => '2026-11-17', 'entrega_estimada' => '2026-11-27', 'fecha_recepcion' => null,
                'items' => [['variante_id' => 301, 'cantidad' => 18]],
            ],
        ];
    }

    /** Mercadería defectuosa y su circuito de cambio con el proveedor. */
    public static function cambios(): array
    {
        return [
            [
                'id' => 'D-0001', 'variante_id' => 103, 'cantidad' => 2, 'proveedor_id' => 1,
                'motivo' => 'Suela despegada', 'origen' => self::ORIGEN_DEVOLUCION,
                'fecha' => '2026-11-11', 'estado' => self::CAMBIO_DEFECTUOSO,
            ],
            [
                'id' => 'D-0002', 'variante_id' => 402, 'cantidad' => 3, 'proveedor_id' => 2,
                'motivo' => 'Costura abierta', 'origen' => self::ORIGEN_STOCK,
                'fecha' => '2026-11-08', 'estado' => self::CAMBIO_ENVIADO,
            ],
            [
                'id' => 'D-0003', 'variante_id' => 202, 'cantidad' => 1, 'proveedor_id' => 1,
                'motivo' => 'Hebilla rota', 'origen' => self::ORIGEN_DEVOLUCION,
                'fecha' => '2026-11-05', 'estado' => self::CAMBIO_RECIBIDO,
            ],
        ];
    }

    public static function etiquetaVariante(int $varianteId): string
    {
        $v = self::variantes()[$varianteId] ?? null;

        return $v ? $v['modelo'] . ' — ' . $v['talle'] . ' · ' . $v['color'] : 'Producto desconocido';
    }
}
