<?php

namespace App\Livewire\Cobros;

use App\Models\Boleta;
use App\Models\Cliente;
use App\Models\Cobro;
use App\Models\CobroImputacion;
use App\Models\CuentaCorriente;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Formulario extends Component
{
    public int $clienteId;
    public string $clienteNombre = '';

    public float $montoEfectivo = 0.0;
    public float $montoTransferencia = 0.0;
    public float $montoCheque = 0.0;
    public string $numeroCheque = '';

    /** @var array<int|string> */
    public array $boletasSeleccionadas = [];

    /** @var array<string, float|int|string> */
    public array $montosImputados = [];

    public function mount(int $clienteId): void
    {
        $this->clienteId = $clienteId;
        $cliente = Cliente::find($clienteId);
        $this->clienteNombre = $cliente?->nombre ?? '';

        $this->sincronizarMontosSugeridos();
    }

    public function updatedBoletasSeleccionadas(): void
    {
        $this->sincronizarMontosSugeridos();
    }

    private function sincronizarMontosSugeridos(): void
    {
        $pendientes = $this->boletasPendientes();
        $restante = $this->totalPago();

        foreach ($pendientes as $boleta) {
            $id = (string) $boleta->id;
            if (! in_array($id, array_map('strval', $this->boletasSeleccionadas), true)) {
                continue;
            }

            if (! array_key_exists($id, $this->montosImputados) || (float) $this->montosImputados[$id] <= 0) {
                $sugerido = min((float) $boleta->saldo, $restante);
                $this->montosImputados[$id] = round(max(0.0, $sugerido), 2);
            }
            $restante = round($restante - (float) ($this->montosImputados[$id] ?? 0), 2);
        }
    }

    public function registrarCobro(): void
    {
        $this->resetErrorBag();

        $totalPago = $this->totalPago();
        $seleccionadas = array_values(array_unique(array_map('strval', $this->boletasSeleccionadas)));

        if ($totalPago <= 0.0) {
            throw ValidationException::withMessages(['pago' => 'Ingresá un monto de pago mayor a cero.']);
        }

        if (count($seleccionadas) === 0) {
            throw ValidationException::withMessages(['boletas' => 'Seleccioná al menos una boleta para imputar el cobro.']);
        }

        $imputaciones = [];
        foreach ($seleccionadas as $id) {
            $monto = round((float) ($this->montosImputados[$id] ?? 0), 2);
            if ($monto <= 0.0) {
                throw ValidationException::withMessages(['boletas' => 'Cada boleta seleccionada debe tener un importe imputado mayor a cero.']);
            }
            $imputaciones[$id] = $monto;
        }

        $totalImputado = round(array_sum($imputaciones), 2);
        if (abs($totalImputado - $totalPago) > 0.009) {
            throw ValidationException::withMessages(['boletas' => 'La suma imputada debe coincidir con el total cobrado.']);
        }

        DB::transaction(function () use ($imputaciones, $totalPago): void {
            $ids = array_map('intval', array_keys($imputaciones));
            sort($ids);

            $boletas = Boleta::query()
                ->where('cliente_id', $this->clienteId)
                ->whereIn('id', $ids)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            if ($boletas->count() !== count($ids)) {
                throw ValidationException::withMessages(['boletas' => 'Alguna boleta seleccionada no es válida para este cliente.']);
            }

            foreach ($ids as $id) {
                $monto = $imputaciones[(string) $id];
                $boleta = $boletas->get($id);
                if (! $boleta) {
                    throw ValidationException::withMessages(['boletas' => 'Boleta no encontrada.']);
                }
                $saldo = round((float) $boleta->saldo, 2);
                if ($monto - $saldo > 0.009) {
                    throw ValidationException::withMessages(['boletas' => 'El importe imputado no puede superar el saldo de la boleta '.$boleta->numero.'.']);
                }
            }

            $cobro = Cobro::query()->create([
                'cliente_id' => $this->clienteId,
                'user_id' => auth()->id(),
                'fecha' => now()->toDateString(),
                'monto_efectivo' => round($this->montoEfectivo, 2),
                'monto_transferencia' => round($this->montoTransferencia, 2),
                'monto_cheque' => round($this->montoCheque, 2),
                'numero_cheque' => $this->numeroCheque !== '' ? $this->numeroCheque : null,
                'total' => round($totalPago, 2),
            ]);

            $saldoReducido = 0.0;
            foreach ($ids as $id) {
                $monto = $imputaciones[(string) $id];
                /** @var Boleta $boleta */
                $boleta = $boletas->get($id);

                CobroImputacion::query()->create([
                    'cobro_id' => $cobro->id,
                    'boleta_id' => $boleta->id,
                    'monto' => $monto,
                ]);

                $boleta->saldo = round((float) $boleta->saldo - $monto, 2);
                $boleta->actualizarEstado();
                $boleta->save();

                $saldoReducido = round($saldoReducido + $monto, 2);
            }

            $cuenta = CuentaCorriente::query()
                ->where('cliente_id', $this->clienteId)
                ->lockForUpdate()
                ->first();

            if ($cuenta) {
                $cuenta->saldo = round(max(0.0, (float) $cuenta->saldo - $saldoReducido), 2);
                $cuenta->save();
            } else {
                $pendiente = (float) Boleta::query()
                    ->where('cliente_id', $this->clienteId)
                    ->where('saldo', '>', 0)
                    ->sum('saldo');
                CuentaCorriente::query()->create([
                    'cliente_id' => $this->clienteId,
                    'saldo' => round($pendiente, 2),
                ]);
            }
        });

        $this->montoEfectivo = 0.0;
        $this->montoTransferencia = 0.0;
        $this->montoCheque = 0.0;
        $this->numeroCheque = '';
        $this->boletasSeleccionadas = [];
        $this->montosImputados = [];

        $this->dispatch('cobro-registrado');
    }

    public function totalPago(): float
    {
        return round(((float) $this->montoEfectivo) + ((float) $this->montoTransferencia) + ((float) $this->montoCheque), 2);
    }

    public function totalImputado(): float
    {
        $total = 0.0;
        foreach ($this->boletasSeleccionadas as $boletaId) {
            $total += (float) ($this->montosImputados[(string) $boletaId] ?? 0);
        }

        return round($total, 2);
    }

    public function diferencia(): float
    {
        return round($this->totalPago() - $this->totalImputado(), 2);
    }

    /**
     * @return \Illuminate\Support\Collection<int, Boleta>
     */
    public function boletasPendientes()
    {
        return Boleta::query()
            ->where('cliente_id', $this->clienteId)
            ->where('saldo', '>', 0)
            ->orderBy('fecha')
            ->orderBy('id')
            ->get();
    }

    public function render()
    {
        return view('livewire.cobros.formulario', [
            'boletas' => $this->boletasPendientes(),
        ]);
    }
}
