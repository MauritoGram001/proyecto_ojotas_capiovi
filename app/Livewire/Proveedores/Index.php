<?php

namespace App\Livewire\Proveedores;

use Illuminate\Support\Str;
use Livewire\Component;

class Index extends Component
{
    public string $buscar = '';
    public string $filtroRubro = '';

    // Estado del formulario simulado
    public bool $mostrarFormulario = false;
    public string $formNombre = '';
    public string $formRubro = 'Calzado';
    public string $formContacto = '';
    public string $formTelefono = '';
    public string $formEmail = '';
    public string $formLocalidad = '';

    // Mensaje de confirmación de la simulación, visible dentro del componente
    public string $mensajeExito = '';

    // Proveedores con datos ficticios para la maqueta
    public array $proveedores = [
        [
            'id' => 1,
            'nombre' => 'Calzados Río Paraná S.A.',
            'rubro' => 'Calzado',
            'contacto' => 'Hernán Giménez',
            'telefono' => '376-4421900',
            'email' => 'pedidos@rioparana.ejemplo.com',
            'localidad' => 'Posadas',
        ],
        [
            'id' => 2,
            'nombre' => 'Textil Norte Argentino',
            'rubro' => 'Textil',
            'contacto' => 'Silvia Duarte',
            'telefono' => '362-4455011',
            'email' => 'ventas@textilnorte.ejemplo.com',
            'localidad' => 'Resistencia',
        ],
        [
            'id' => 3,
            'nombre' => 'Distribuidora Ojotas del Sur',
            'rubro' => 'Calzado',
            'contacto' => 'Pablo Ferreyra',
            'telefono' => '11-4890-2233',
            'email' => '',
            'localidad' => 'Buenos Aires',
        ],
        [
            'id' => 4,
            'nombre' => 'Hilados y Tejidos Misioneros',
            'rubro' => 'Textil',
            'contacto' => 'Marta Kovalski',
            'telefono' => '3755-408812',
            'email' => 'contacto@hilados.ejemplo.com',
            'localidad' => 'Oberá',
        ],
        [
            'id' => 5,
            'nombre' => 'Importadora Sandalias del Litoral',
            'rubro' => 'Calzado',
            'contacto' => 'Rodrigo Aquino',
            'telefono' => '376-4478855',
            'email' => '',
            'localidad' => 'Posadas',
        ],
    ];

    protected function rules(): array
    {
        return [
            'formNombre' => ['required', 'string', 'max:120'],
            'formRubro' => ['required', 'in:Calzado,Textil'],
            'formContacto' => ['nullable', 'string', 'max:80'],
            'formTelefono' => ['nullable', 'string', 'max:30'],
            'formEmail' => ['nullable', 'email', 'max:120'],
            'formLocalidad' => ['nullable', 'string', 'max:80'],
        ];
    }

    protected function messages(): array
    {
        return [
            'formNombre.required' => 'Ingresá el nombre del proveedor.',
            'formEmail.email' => 'Ingresá un email válido.',
        ];
    }

    public function abrirFormulario(): void
    {
        $this->reset(['formNombre', 'formContacto', 'formTelefono', 'formEmail', 'formLocalidad', 'mensajeExito']);
        $this->formRubro = 'Calzado';
        $this->resetErrorBag();
        $this->mostrarFormulario = true;
    }

    public function cerrarFormulario(): void
    {
        $this->mostrarFormulario = false;
        $this->resetErrorBag();
    }

    public function guardarSimulado(): void
    {
        $this->validate();

        $nuevoId = collect($this->proveedores)->max('id') + 1;
        $this->proveedores[] = [
            'id' => $nuevoId,
            'nombre' => trim($this->formNombre),
            'rubro' => $this->formRubro,
            'contacto' => trim($this->formContacto),
            'telefono' => trim($this->formTelefono),
            'email' => trim($this->formEmail),
            'localidad' => trim($this->formLocalidad),
        ];

        $this->mostrarFormulario = false;
        $this->mensajeExito = 'Proveedor simulado agregado a la maqueta. No se guarda en la base de datos.';
    }

    public function limpiarFiltros(): void
    {
        $this->buscar = '';
        $this->filtroRubro = '';
    }

    private function normalizar(string $texto): string
    {
        return Str::lower(Str::ascii($texto));
    }

    private function coincide(array $p): bool
    {
        if ($this->filtroRubro !== '' && $p['rubro'] !== $this->filtroRubro) {
            return false;
        }

        $q = trim($this->buscar);
        if ($q === '') {
            return true;
        }

        // Búsqueda por nombre del proveedor o de la persona de contacto, sin tildes.
        $buscado = $this->normalizar($q);
        if (str_contains($this->normalizar($p['nombre']), $buscado)
            || str_contains($this->normalizar($p['contacto']), $buscado)) {
            return true;
        }

        // Búsqueda por teléfono, ignorando guiones y espacios.
        $digitosBuscados = preg_replace('/\D+/', '', $q);

        return $digitosBuscados !== ''
            && str_contains(preg_replace('/\D+/', '', $p['telefono']), $digitosBuscados);
    }

    public function render()
    {
        $filtrados = array_filter($this->proveedores, fn (array $p) => $this->coincide($p));

        return view('livewire.proveedores.index', [
            'proveedoresFiltrados' => $filtrados,
        ])->layout('layouts.app', ['title' => 'Proveedores']);
    }
}
