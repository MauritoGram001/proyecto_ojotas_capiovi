<?php

namespace App\Livewire\Proveedores;

use App\Models\Proveedor;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Index extends Component
{
    public string $buscar = '';
    public string $filtroRubro = '';

    public bool $mostrarFormulario = false;
    public ?int $proveedorId = null;
    public string $formRazonSocial = '';
    public string $formCuit = '';
    public string $formContacto = '';
    public string $formTelefono = '';
    public string $formEmail = '';
    public string $formDireccion = '';
    public string $formRubro = 'Calzado';

    /** @return array<string, list<string|object>> */
    public function rules(): array
    {
        return [
            'formRazonSocial' => ['required', 'string', 'max:255'],
            'formCuit' => [
                'nullable', 'string', 'max:20', 'regex:/^\d{2}-\d{8}-\d$/',
                Rule::unique('proveedores', 'cuit')->ignore($this->proveedorId),
            ],
            'formContacto' => ['nullable', 'string', 'max:255'],
            'formTelefono' => ['nullable', 'string', 'max:50'],
            'formEmail' => ['nullable', 'email', 'max:255'],
            'formDireccion' => ['nullable', 'string', 'max:255'],
            'formRubro' => ['required', 'in:'.implode(',', Proveedor::RUBROS)],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'formRazonSocial.required' => 'Por favor ingresá la razón social del proveedor.',
            'formCuit.regex' => 'El CUIT debe tener el formato 30-12345678-9.',
            'formCuit.unique' => 'Ya existe un proveedor con ese CUIT.',
            'formRubro.in' => 'Seleccioná un rubro válido.',
        ];
    }

    public function abrirFormulario(?int $id = null): void
    {
        $this->reset(['formRazonSocial', 'formCuit', 'formContacto', 'formTelefono', 'formEmail', 'formDireccion']);
        $this->resetErrorBag();
        $this->proveedorId = null;
        $this->formRubro = 'Calzado';

        if ($id !== null) {
            $proveedor = Proveedor::findOrFail($id);
            $this->proveedorId = $proveedor->id;
            $this->formRazonSocial = $proveedor->razon_social;
            $this->formCuit = $proveedor->cuit ?? '';
            $this->formContacto = $proveedor->contacto ?? '';
            $this->formTelefono = $proveedor->telefono ?? '';
            $this->formEmail = $proveedor->email ?? '';
            $this->formDireccion = $proveedor->direccion ?? '';
            $this->formRubro = $proveedor->rubro;
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
            'razon_social' => trim($this->formRazonSocial),
            'cuit' => trim($this->formCuit) ?: null,
            'contacto' => trim($this->formContacto) ?: null,
            'telefono' => trim($this->formTelefono) ?: null,
            'email' => trim($this->formEmail) ?: null,
            'direccion' => trim($this->formDireccion) ?: null,
            'rubro' => $this->formRubro,
        ];

        if ($this->proveedorId !== null) {
            Proveedor::findOrFail($this->proveedorId)->update($datos);
            session()->flash('success', 'Proveedor actualizado correctamente.');
        } else {
            Proveedor::create($datos);
            session()->flash('success', 'Proveedor guardado correctamente.');
        }

        $this->mostrarFormulario = false;
    }

    public function eliminar(int $id): void
    {
        Proveedor::findOrFail($id)->delete();

        if ($this->proveedorId === $id) {
            $this->cerrarFormulario();
        }

        session()->flash('success', 'Proveedor eliminado correctamente.');
    }

    public function render()
    {
        $proveedores = Proveedor::query()
            ->when($this->buscar !== '', function ($query): void {
                $buscar = '%'.$this->buscar.'%';
                $query->where(function ($query) use ($buscar): void {
                    $query->where('razon_social', 'like', $buscar)
                        ->orWhere('telefono', 'like', $buscar);
                });
            })
            ->when($this->filtroRubro !== '', fn ($query) => $query->where('rubro', $this->filtroRubro))
            ->orderBy('razon_social')
            ->get();

        return view('livewire.proveedores.index', [
            'proveedoresFiltrados' => $proveedores,
        ])->layout('layouts.app', ['title' => 'Proveedores']);
    }
}
