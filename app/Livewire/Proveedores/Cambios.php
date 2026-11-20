<?php

namespace App\Livewire\Proveedores;

use App\Services\AbastecimientoEjemplo as Datos;
use Livewire\Component;

/**
 * Mercadería defectuosa y enviada a cambio (VIS-006).
 *
 * Circuito: Stock defectuoso → Enviada a cambio → Reemplazo recibido.
 * Fase visual: no modifica el stock vendible ni guarda datos reales.
 */
class Cambios extends Component
{
    public string $filtroEstado = '';

    public array $registros = [];
    public string $mensajeExito = '';

    // Formulario de registro de mercadería defectuosa
    public bool $mostrarFormulario = false;
    public string $formVariante = '';
    public $formCantidad = 1;
    public string $formProveedor = '';
    public string $formMotivo = '';
    public string $formOrigen = Datos::ORIGEN_DEVOLUCION;

    public function mount(): void
    {
        $this->registros = Datos::cambios();
    }

    protected function rules(): array
    {
        return [
            'formVariante' => ['required'],
            'formCantidad' => ['required', 'integer', 'min:1', 'max:999'],
            'formProveedor' => ['required'],
            'formMotivo' => ['required', 'string', 'max:200'],
            'formOrigen' => ['required', 'in:' . Datos::ORIGEN_DEVOLUCION . ',' . Datos::ORIGEN_STOCK],
        ];
    }

    protected function messages(): array
    {
        return [
            'formVariante.required' => 'Elegí el producto defectuoso (modelo, talle y color).',
            'formCantidad.required' => 'Ingresá la cantidad.',
            'formCantidad.integer' => 'La cantidad debe ser un número entero.',
            'formCantidad.min' => 'La cantidad mínima es 1.',
            'formCantidad.max' => 'La cantidad máxima es 999.',
            'formProveedor.required' => 'Elegí el proveedor al que se le reclama.',
            'formMotivo.required' => 'Contá brevemente cuál es la falla.',
            'formMotivo.max' => 'El motivo no puede superar los 200 caracteres.',
        ];
    }

    public function abrirFormulario(): void
    {
        $this->reset(['formVariante', 'formProveedor', 'formMotivo', 'mensajeExito']);
        $this->formCantidad = 1;
        $this->formOrigen = Datos::ORIGEN_DEVOLUCION;
        $this->resetErrorBag();
        $this->mostrarFormulario = true;
    }

    public function cerrarFormulario(): void
    {
        $this->mostrarFormulario = false;
        $this->resetErrorBag();
    }

    public function registrarSimulado(): void
    {
        $this->validate();

        if (! isset(Datos::variantes()[(int) $this->formVariante])) {
            $this->addError('formVariante', 'El producto elegido no existe en el catálogo de ejemplo.');
            return;
        }
        if (! isset(Datos::proveedores()[(int) $this->formProveedor])) {
            $this->addError('formProveedor', 'El proveedor elegido no existe.');
            return;
        }

        $numero = (int) substr(collect($this->registros)->max('id') ?? 'D-0000', 2) + 1;
        $id = 'D-' . str_pad((string) $numero, 4, '0', STR_PAD_LEFT);

        $this->registros[] = [
            'id' => $id,
            'variante_id' => (int) $this->formVariante,
            'cantidad' => (int) $this->formCantidad,
            'proveedor_id' => (int) $this->formProveedor,
            'motivo' => trim($this->formMotivo),
            'origen' => $this->formOrigen,
            'fecha' => now()->toDateString(),
            'estado' => Datos::CAMBIO_DEFECTUOSO,
        ];

        $this->mostrarFormulario = false;
        $this->mensajeExito = 'Registro ' . $id . ' agregado como Stock defectuoso. Simulación: no se guarda ni se toca el stock.';
    }

    public function enviarACambio(string $id): void
    {
        $this->avanzar($id, Datos::CAMBIO_DEFECTUOSO, Datos::CAMBIO_ENVIADO, 'enviado a cambio al proveedor');
    }

    public function registrarReemplazo(string $id): void
    {
        $this->avanzar($id, Datos::CAMBIO_ENVIADO, Datos::CAMBIO_RECIBIDO, 'cerrado: llegó el reemplazo');
    }

    /**
     * Pasa un registro al siguiente estado solo si está en el estado esperado,
     * para respetar el orden del circuito.
     */
    private function avanzar(string $id, string $desde, string $hacia, string $descripcion): void
    {
        foreach ($this->registros as $i => $r) {
            if ($r['id'] === $id && $r['estado'] === $desde) {
                $this->registros[$i]['estado'] = $hacia;
                $this->mensajeExito = 'Registro ' . $id . ' ' . $descripcion . '. Simulación: el stock vendible no cambia.';
                return;
            }
        }
    }

    public function render()
    {
        $filtrados = array_values(array_filter(
            $this->registros,
            fn (array $r) => $this->filtroEstado === '' || $r['estado'] === $this->filtroEstado
        ));
        usort($filtrados, fn (array $a, array $b) => strcmp($b['fecha'], $a['fecha']));

        $porEstado = array_count_values(array_column($this->registros, 'estado'));
        $unidadesDefectuosas = array_sum(array_column(
            array_filter($this->registros, fn (array $r) => $r['estado'] === Datos::CAMBIO_DEFECTUOSO),
            'cantidad'
        ));

        return view('livewire.proveedores.cambios', [
            'registrosFiltrados' => $filtrados,
            'variantes' => Datos::variantes(),
            'proveedores' => Datos::proveedores(),
            'cantidadDefectuoso' => $porEstado[Datos::CAMBIO_DEFECTUOSO] ?? 0,
            'cantidadEnviado' => $porEstado[Datos::CAMBIO_ENVIADO] ?? 0,
            'cantidadRecibido' => $porEstado[Datos::CAMBIO_RECIBIDO] ?? 0,
            'unidadesDefectuosas' => $unidadesDefectuosas,
        ])->layout('layouts.app', ['title' => 'Cambios y Mercadería Defectuosa']);
    }
}
