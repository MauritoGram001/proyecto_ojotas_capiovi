<?php

namespace App\Livewire\Cobros;

use App\Services\CuentasEjemplo;
use Livewire\Component;

/**
 * Cobro combinado (efectivo, transferencia y cheque básico) con imputación a
 * varias boletas (VIS-005). Fase visual: no guarda nada ni modifica saldos.
 */
class Formulario extends Component
{
    public string $clienteId = '';

    // Ids de las boletas elegidas para imputar el cobro
    public array $boletasSeleccionadas = [];

    // Importes por medio de pago (texto: el input numérico puede quedar vacío)
    public string $efectivo = '';
    public string $transferencia = '';
    public string $cheque = '';

    public string $mensajeExito = '';

    public function mount(?string $cliente = null): void
    {
        // Permite entrar desde la cuenta de un cliente con la cuenta ya elegida.
        if ($cliente !== null && ctype_digit($cliente) && CuentasEjemplo::saldoCliente((int) $cliente) > 0) {
            $this->clienteId = $cliente;
        }
    }

    public function updatedClienteId(): void
    {
        $this->reset(['boletasSeleccionadas', 'efectivo', 'transferencia', 'cheque', 'mensajeExito']);
        $this->resetErrorBag();
    }

    public function seleccionarTodas(): void
    {
        $this->boletasSeleccionadas = array_column($this->pendientes(), 'id');
    }

    public function limpiarSeleccion(): void
    {
        $this->boletasSeleccionadas = [];
    }

    public function confirmarCobroSimulado(): void
    {
        $this->mensajeExito = '';
        $this->resetErrorBag();

        $this->validate([
            'efectivo' => ['nullable', 'numeric', 'min:0'],
            'transferencia' => ['nullable', 'numeric', 'min:0'],
            'cheque' => ['nullable', 'numeric', 'min:0'],
        ], [
            'efectivo.numeric' => 'Ingresá un importe válido.',
            'efectivo.min' => 'El importe no puede ser negativo.',
            'transferencia.numeric' => 'Ingresá un importe válido.',
            'transferencia.min' => 'El importe no puede ser negativo.',
            'cheque.numeric' => 'Ingresá un importe válido.',
            'cheque.min' => 'El importe no puede ser negativo.',
        ]);

        if ($this->clienteId === '') {
            $this->addError('clienteId', 'Elegí el cliente que paga.');
            return;
        }

        $imputacion = $this->imputacion();

        if ($imputacion['total'] <= 0) {
            $this->addError('importes', 'Ingresá al menos un importe en efectivo, transferencia o cheque.');
            return;
        }
        if (count($imputacion['filas']) === 0) {
            $this->addError('boletasSeleccionadas', 'Elegí al menos una boleta para imputar el cobro.');
            return;
        }

        $this->mensajeExito = 'Cobro simulado de ' . \App\Livewire\Ventas\Crear::dinero($imputacion['total'])
            . ' imputado a ' . count($imputacion['filas']) . ' boleta(s). No se guardó ni se modificaron saldos.';
        $this->reset(['boletasSeleccionadas', 'efectivo', 'transferencia', 'cheque']);
    }

    private function importe(string $texto): float
    {
        $texto = str_replace(',', '.', trim($texto));

        return (is_numeric($texto) && (float) $texto > 0) ? round((float) $texto, 2) : 0.0;
    }

    private function pendientes(): array
    {
        return $this->clienteId === '' ? [] : CuentasEjemplo::boletasPendientes((int) $this->clienteId);
    }

    /**
     * Reparte el total cobrado entre las boletas elegidas, de la más antigua a la más nueva.
     */
    private function imputacion(): array
    {
        $porMedio = [
            'Efectivo' => $this->importe($this->efectivo),
            'Transferencia' => $this->importe($this->transferencia),
            'Cheque' => $this->importe($this->cheque),
        ];
        $total = round(array_sum($porMedio), 2);

        $restante = $total;
        $filas = [];
        $totalSeleccionado = 0.0;

        foreach ($this->pendientes() as $b) {
            if (! in_array($b['id'], $this->boletasSeleccionadas, true)) {
                continue;
            }

            $aplicado = round(min($b['saldo'], $restante), 2);
            $restante = round($restante - $aplicado, 2);
            $totalSeleccionado += $b['saldo'];

            $filas[] = [
                'id' => $b['id'],
                'fecha' => $b['fecha'],
                'saldo' => $b['saldo'],
                'aplicado' => $aplicado,
                'queda' => round($b['saldo'] - $aplicado, 2),
            ];
        }

        return [
            'por_medio' => $porMedio,
            'total' => $total,
            'filas' => $filas,
            'total_seleccionado' => round($totalSeleccionado, 2),
            'sobrante' => $restante,
        ];
    }

    public function render()
    {
        $clientesConDeuda = array_filter(
            CuentasEjemplo::cuentasClientes(),
            fn (array $c) => $c['saldo'] > 0
        );

        return view('livewire.cobros.formulario', [
            'clientesConDeuda' => $clientesConDeuda,
            'pendientes' => $this->pendientes(),
            'imputacion' => $this->imputacion(),
        ])->layout('layouts.app', ['title' => 'Registrar Cobro']);
    }
}
