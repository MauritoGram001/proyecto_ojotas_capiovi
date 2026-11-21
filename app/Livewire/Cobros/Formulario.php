<?php

namespace App\Livewire\Cobros;

use Livewire\Component;

class Formulario extends Component
{
    public string $clienteId = '';
    public string $clienteNombre = '';
    public array $boletas = [];

    public float $montoEfectivo = 0.0;
    public float $montoTransferencia = 0.0;
    public float $montoCheque = 0.0;
    public string $numeroCheque = '';

    public array $boletasSeleccionadas = [];
    public array $montosImputados = [];

    public function mount(string $clienteId, string $clienteNombre, array $boletas): void
    {
        $this->clienteId = $clienteId;
        $this->clienteNombre = $clienteNombre;
        $this->boletas = $boletas;

        foreach ($boletas as $boleta) {
            $this->montosImputados[(string) $boleta['id']] = (float) $boleta['saldo'];
        }
    }

    public function registrarCobro(): void
    {
        $totalPago = $this->totalPago();
        $totalImputado = $this->totalImputado();

        if ($totalPago <= 0) {
            session()->flash('warning', 'Ingresá un monto en al menos un medio de pago.');

            return;
        }

        if (count($this->boletasSeleccionadas) === 0) {
            session()->flash('warning', 'Seleccioná al menos una boleta para imputar el cobro.');

            return;
        }

        if (abs($totalPago - $totalImputado) > 0.01) {
            session()->flash('warning', 'El total del pago debe coincidir con el total imputado a las boletas.');

            return;
        }

        session()->flash('success', '¡Cobro simulado registrado e imputado a las boletas seleccionadas!');

        $this->montoEfectivo = 0.0;
        $this->montoTransferencia = 0.0;
        $this->montoCheque = 0.0;
        $this->numeroCheque = '';
        $this->boletasSeleccionadas = [];

        foreach ($this->boletas as $boleta) {
            $this->montosImputados[(string) $boleta['id']] = (float) $boleta['saldo'];
        }
    }

    public function totalPago(): float
    {
        return (float) $this->montoEfectivo + (float) $this->montoTransferencia + (float) $this->montoCheque;
    }

    public function totalImputado(): float
    {
        $total = 0.0;
        foreach ($this->boletasSeleccionadas as $id) {
            $total += (float) ($this->montosImputados[(string) $id] ?? 0);
        }

        return $total;
    }

    public function render()
    {
        return view('livewire.cobros.formulario', [
            'totalPago' => $this->totalPago(),
            'totalImputado' => $this->totalImputado(),
        ]);
    }
}
