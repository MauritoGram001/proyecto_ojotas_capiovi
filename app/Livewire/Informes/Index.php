<?php

namespace App\Livewire\Informes;

use Livewire\Component;

class Index extends Component
{
    public string $tab = 'ventas';

    // Filtros del informe de ventas por período
    public string $desde = '2026-11-01';
    public string $hasta = '2026-11-30';
    public string $vista = 'detalle'; // detalle | diario | mensual

    // Historial por cliente identificado
    public string $clienteSeleccionado = 'María González';

    /**
     * Ventas ficticias de la fase visual. Se guarda una fecha ISO para filtrar
     * y se muestra en formato argentino en la vista.
     */
    public array $ventas = [
        ['numero' => 'INT-0001', 'fecha' => '2026-11-18', 'cliente' => 'María González', 'modo' => 'Minorista', 'items' => 2, 'total' => 11700.0],
        ['numero' => 'INT-0002', 'fecha' => '2026-11-18', 'cliente' => 'Consumidor Final (sin identificar)', 'modo' => 'Minorista', 'items' => 1, 'total' => 6500.0],
        ['numero' => 'INT-0003', 'fecha' => '2026-11-19', 'cliente' => 'Carlos Ruiz', 'modo' => 'Minorista', 'items' => 3, 'total' => 21400.0],
        ['numero' => 'INT-0004', 'fecha' => '2026-11-19', 'cliente' => 'Distribuidora Norte S.R.L.', 'modo' => 'Mayorista', 'items' => 4, 'total' => 45600.0],
        ['numero' => 'INT-0005', 'fecha' => '2026-11-20', 'cliente' => 'Comercial El Sol S.A.', 'modo' => 'Mayorista', 'items' => 2, 'total' => 26800.0],
        ['numero' => 'INT-0006', 'fecha' => '2026-11-20', 'cliente' => 'María González', 'modo' => 'Minorista', 'items' => 1, 'total' => 9200.0],
        ['numero' => 'INT-0007', 'fecha' => '2026-11-20', 'cliente' => 'Consumidor Final (sin identificar)', 'modo' => 'Minorista', 'items' => 2, 'total' => 14300.0],
    ];

    public array $clientesIdentificados = [
        'María González',
        'Carlos Ruiz',
        'Distribuidora Norte S.R.L.',
        'Comercial El Sol S.A.',
    ];

    public function aplicarPreset(string $preset): void
    {
        switch ($preset) {
            case 'hoy':
                $this->desde = '2026-11-20';
                $this->hasta = '2026-11-20';
                break;
            case 'semana':
                $this->desde = '2026-11-16';
                $this->hasta = '2026-11-22';
                break;
            case 'mes':
                $this->desde = '2026-11-01';
                $this->hasta = '2026-11-30';
                break;
        }
    }

    public function render()
    {
        $desde = $this->desde !== '' ? $this->desde : '0000-00-00';
        $hasta = $this->hasta !== '' ? $this->hasta : '9999-99-99';

        $ventasFiltradas = array_values(array_filter($this->ventas, function ($venta) use ($desde, $hasta) {
            return $venta['fecha'] >= $desde && $venta['fecha'] <= $hasta;
        }));

        // Totales consolidados directos
        $totalGeneral = 0.0;
        foreach ($ventasFiltradas as $venta) {
            $totalGeneral += (float) $venta['total'];
        }
        $cantidadVentas = count($ventasFiltradas);
        $ticketPromedio = $cantidadVentas > 0 ? $totalGeneral / $cantidadVentas : 0.0;

        // Agrupación para informes diarios y mensuales simplificados
        $agrupado = [];
        foreach ($ventasFiltradas as $venta) {
            $clave = $this->vista === 'mensual'
                ? substr($venta['fecha'], 0, 7)
                : $venta['fecha'];

            if (! isset($agrupado[$clave])) {
                $agrupado[$clave] = ['clave' => $clave, 'cantidad' => 0, 'total' => 0.0];
            }
            $agrupado[$clave]['cantidad'] += 1;
            $agrupado[$clave]['total'] += (float) $venta['total'];
        }
        ksort($agrupado);
        $agrupado = array_values($agrupado);

        // Historial del cliente identificado seleccionado
        $historial = array_values(array_filter($this->ventas, function ($venta) {
            return $venta['cliente'] === $this->clienteSeleccionado;
        }));
        $totalCliente = 0.0;
        foreach ($historial as $venta) {
            $totalCliente += (float) $venta['total'];
        }
        $cantidadCompras = count($historial);
        $promedioCliente = $cantidadCompras > 0 ? $totalCliente / $cantidadCompras : 0.0;
        $ultimaCompra = $cantidadCompras > 0 ? $historial[$cantidadCompras - 1]['fecha'] : null;

        $view = $this->tab === 'cliente'
            ? 'livewire.informes.historial-cliente'
            : 'livewire.informes.ventas-periodo';

        return view($view, [
            'ventasFiltradas' => $ventasFiltradas,
            'totales' => [
                'total' => $totalGeneral,
                'cantidad' => $cantidadVentas,
                'promedio' => $ticketPromedio,
            ],
            'agrupado' => $agrupado,
            'historial' => $historial,
            'totalesCliente' => [
                'total' => $totalCliente,
                'cantidad' => $cantidadCompras,
                'promedio' => $promedioCliente,
                'ultimaCompra' => $ultimaCompra,
            ],
        ])->layout('layouts.app', ['title' => 'Informes e Historial']);
    }
}
