<?php

namespace App\Livewire\Clientes;

use App\Models\Cliente;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Index extends Component
{
    public string $buscar = '';
    public string $filtroTipo = '';

    // Selección para venta rápida; "final" representa al consumidor sin identificar.
    public string $compradorRapido = '';
    public string $compradorConfirmado = '';

    public bool $mostrarFormulario = false;
    public ?int $clienteId = null;
    public string $formNombre = '';
    public string $formTelefono = '';
    public string $formEmail = '';
    public string $formDireccion = '';
    public string $formTipo = Cliente::TIPO_MINORISTA;
    public string $formCuit = '';

    /** @return array<string, list<string|object>> */
    public function rules(): array
    {
        $reglasCuit = ['string', 'max:20', 'regex:/^\d{2}-\d{8}-\d$/'];

        if ($this->formTipo === Cliente::TIPO_MAYORISTA) {
            array_unshift($reglasCuit, 'required');
        } else {
            array_unshift($reglasCuit, 'nullable');
        }

        $reglasCuit[] = Rule::unique('clientes', 'cuit')->ignore($this->clienteId);

        return [
            'formNombre' => ['required', 'string', 'max:255'],
            'formTipo' => ['required', 'in:'.implode(',', Cliente::TIPOS)],
            'formTelefono' => ['nullable', 'string', 'max:50'],
            'formEmail' => ['nullable', 'email', 'max:255'],
            'formDireccion' => ['nullable', 'string', 'max:255'],
            'formCuit' => $reglasCuit,
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'formNombre.required' => 'Por favor ingresá el nombre o razón social del cliente.',
            'formTipo.in' => 'Seleccioná un tipo de cliente válido.',
            'formCuit.required' => 'El CUIT es obligatorio para clientes mayoristas.',
            'formCuit.regex' => 'El CUIT debe tener el formato 30-12345678-9.',
            'formCuit.unique' => 'Ya existe un cliente con ese CUIT.',
        ];
    }

    public function abrirFormulario(?int $id = null): void
    {
        $this->reset(['formNombre', 'formTelefono', 'formEmail', 'formDireccion', 'formCuit']);
        $this->resetErrorBag();
        $this->clienteId = null;
        $this->formTipo = Cliente::TIPO_MINORISTA;

        if ($id !== null) {
            $cliente = Cliente::findOrFail($id);
            $this->clienteId = $cliente->id;
            $this->formNombre = $cliente->nombre;
            $this->formTipo = $cliente->tipo;
            $this->formTelefono = $cliente->telefono ?? '';
            $this->formEmail = $cliente->email ?? '';
            $this->formDireccion = $cliente->direccion ?? '';
            $this->formCuit = $cliente->cuit ?? '';
        }

        $this->mostrarFormulario = true;
    }

    public function cerrarFormulario(): void
    {
        $this->mostrarFormulario = false;
    }

    public function guardar(): void
    {
        $this->validate();

        $datos = [
            'nombre' => trim($this->formNombre),
            'tipo' => $this->formTipo,
            'telefono' => trim($this->formTelefono) ?: null,
            'email' => trim($this->formEmail) ?: null,
            'direccion' => trim($this->formDireccion) ?: null,
            'cuit' => $this->formTipo === Cliente::TIPO_MAYORISTA ? trim($this->formCuit) : null,
        ];

        if ($this->clienteId !== null) {
            Cliente::findOrFail($this->clienteId)->update($datos);
            session()->flash('success', 'Cliente actualizado correctamente.');
        } else {
            Cliente::create($datos);
            session()->flash('success', 'Cliente guardado correctamente.');
        }

        $this->mostrarFormulario = false;
    }

    public function eliminar(int $id): void
    {
        Cliente::findOrFail($id)->delete();

        if ($this->clienteId === $id) {
            $this->cerrarFormulario();
        }

        session()->flash('success', 'Cliente eliminado correctamente.');
    }

    public function confirmarCompradorRapido(): void
    {
        if ($this->compradorRapido === '') {
            session()->flash('warning', 'Elegí un comprador o seleccioná Consumidor Final.');

            return;
        }

        if ($this->compradorRapido === 'final') {
            $this->compradorConfirmado = 'Consumidor Final (sin identificar)';
            session()->flash('success', 'Venta rápida sin identificar comprador.');

            return;
        }

        $cliente = Cliente::find((int) $this->compradorRapido);

        if (! $cliente) {
            session()->flash('warning', 'El comprador seleccionado ya no existe.');

            return;
        }

        $this->compradorConfirmado = $cliente->nombre;
        session()->flash('success', 'Comprador seleccionado: '.$cliente->nombre);
    }

    public function render()
    {
        $clientes = Cliente::query()
            ->when($this->buscar !== '', function ($query): void {
                $buscar = '%'.$this->buscar.'%';
                $query->where(function ($query) use ($buscar): void {
                    $query->where('nombre', 'like', $buscar)
                        ->orWhere('telefono', 'like', $buscar);
                });
            })
            ->when($this->filtroTipo !== '', fn ($query) => $query->where('tipo', $this->filtroTipo))
            ->orderBy('nombre')
            ->get();

        return view('livewire.clientes.index', [
            'clientesFiltrados' => $clientes,
        ])->layout('layouts.app', ['title' => 'Clientes']);
    }
}
