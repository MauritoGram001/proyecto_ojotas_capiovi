<?php

namespace App\Livewire\CuentasCorrientes;

use App\Models\Boleta;
use App\Models\Cliente;
use App\Models\Cobro;
use Livewire\Attributes\On;
use Livewire\Component;

class Index extends Component
{
    public string $tab = 'clientes';
    public string $clienteId = '';
    public bool $mostrarCobro = false;

    /** @var array<int, array<string, mixed>> Datos simulados de deudas con proveedores (fuera de alcance FUN-005). */
    public array $deudasProveedores = [
        ['proveedor' => 'Fábrica de Calzados Misiones S.R.L.', 'concepto' => 'Pedido #0042 - 120 pares', 'vencimiento' => '25/11/2026', 'monto' => 125000.0],
        ['proveedor' => 'Insumos Capioví', 'concepto' => 'Insumos de empaque', 'vencimiento' => '28/11/2026', 'monto' => 34000.0],
    ];

    public function mount(): void
    {
        $primero = Cliente::query()->orderBy('id')->value('id');
        $this->clienteId = $primero ? (string) $primero : '';
    }

    public function abrirCobro(): void
    {
        $this->mostrarCobro = true;
    }

    public function cerrarCobro(): void
    {
        $this->mostrarCobro = false;
    }

    #[On('cobro-registrado')]
    public function refrescarTrasCobro(): void
    {
        $this->mostrarCobro = false;
    }

    public function render()
    {
        $clientes = Cliente::query()
            ->with('cuentaCorriente')
            ->orderBy('nombre')
            ->get();

        $clienteSeleccionado = $this->clienteId !== ''
            ? $clientes->firstWhere('id', (int) $this->clienteId)
            : null;

        if (! $clienteSeleccionado && $clientes->isNotEmpty()) {
            $clienteSeleccionado = $clientes->first();
            $this->clienteId = (string) $clienteSeleccionado->id;
        }

        $boletasPendientes = [];
        $movimientosCliente = [];

        if ($clienteSeleccionado) {
            $cid = (int) $clienteSeleccionado->id;

            $boletasPendientes = Boleta::query()
                ->where('cliente_id', $cid)
                ->where('saldo', '>', 0)
                ->orderBy('fecha')
                ->orderBy('id')
                ->get()
                ->map(fn (Boleta $b) => [
                    'id' => $b->id,
                    'numero' => $b->numero,
                    'cliente_id' => $b->cliente_id,
                    'fecha' => $b->fecha ? $b->fecha->format('d/m/Y') : '',
                    'monto' => (float) $b->monto,
                    'saldo' => (float) $b->saldo,
                    'concepto' => $b->concepto,
                ])
                ->all();

            $boletasMov = Boleta::query()
                ->where('cliente_id', $cid)
                ->orderByDesc('fecha')
                ->orderByDesc('id')
                ->get();

            $cobrosMov = Cobro::query()
                ->where('cliente_id', $cid)
                ->orderByDesc('fecha')
                ->orderByDesc('id')
                ->get();

            $movimientos = [];

            foreach ($boletasMov as $boleta) {
                $movimientos[] = [
                    'fecha' => $boleta->fecha ? $boleta->fecha->format('d/m/Y') : '',
                    'fecha_ord' => $boleta->fecha ? $boleta->fecha->format('Y-m-d') : '',
                    'id_ord' => $boleta->id,
                    'tipo' => 'Boleta',
                    'detalle' => $boleta->concepto ?: ('Boleta '.$boleta->numero),
                    'monto' => (float) $boleta->monto,
                    'estado' => $boleta->estado,
                ];
            }

            foreach ($cobrosMov as $cobro) {
                $movimientos[] = [
                    'fecha' => $cobro->fecha ? $cobro->fecha->format('d/m/Y') : '',
                    'fecha_ord' => $cobro->fecha ? $cobro->fecha->format('Y-m-d') : '',
                    'id_ord' => $cobro->id + 1000000,
                    'tipo' => 'Cobro',
                    'detalle' => $this->detalleCobro($cobro),
                    'monto' => -(float) $cobro->total,
                    'estado' => 'Aplicado',
                ];
            }

            usort($movimientos, function ($a, $b) {
                $cmp = strcmp($b['fecha_ord'], $a['fecha_ord']);
                if ($cmp !== 0) {
                    return $cmp;
                }

                return $b['id_ord'] <=> $a['id_ord'];
            });

            $movimientosCliente = $movimientos;
        }

        return view('livewire.cuentas-corrientes.index', [
            'clientes' => $clientes,
            'clienteSeleccionado' => $clienteSeleccionado,
            'boletasPendientes' => $boletasPendientes,
            'movimientosCliente' => $movimientosCliente,
        ]);
    }

    private function detalleCobro(Cobro $cobro): string
    {
        $medios = [];
        if ((float) $cobro->monto_efectivo > 0) {
            $medios[] = 'Efectivo';
        }
        if ((float) $cobro->monto_transferencia > 0) {
            $medios[] = 'Transferencia';
        }
        if ((float) $cobro->monto_cheque > 0) {
            $medios[] = 'Cheque'.($cobro->numero_cheque ? ' '.$cobro->numero_cheque : '');
        }

        return $medios ? 'Pago ('.implode(' + ', $medios).')' : 'Pago registrado';
    }
}
