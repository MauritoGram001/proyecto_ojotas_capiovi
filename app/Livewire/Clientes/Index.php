<?php

namespace App\Livewire\Clientes;

use Illuminate\Support\Str;
use Livewire\Component;

class Index extends Component
{
    public const CONSUMIDOR_FINAL = 'consumidor-final';

    public string $buscar = '';
    public string $filtroTipo = '';

    // Estado del formulario simulado
    public bool $mostrarFormulario = false;
    public string $formNombre = '';
    public string $formTipo = 'Minorista';
    public string $formCuit = '';
    public string $formTelefono = '';
    public string $formEmail = '';
    public string $formLocalidad = '';

    // Selector de cliente para venta rápida (vista previa del flujo de VIS-004)
    public string $clienteVenta = self::CONSUMIDOR_FINAL;

    // Mensaje de confirmación de la simulación, visible dentro del componente
    public string $mensajeExito = '';

    // Clientes con datos ficticios para la maqueta (CUIT y teléfonos inventados)
    public array $clientes = [
        [
            'id' => 1,
            'nombre' => 'María Fernández',
            'tipo' => 'Minorista',
            'cuit' => '',
            'telefono' => '3743-421587',
            'email' => 'maria.fernandez@ejemplo.com',
            'localidad' => 'Capioví',
        ],
        [
            'id' => 2,
            'nombre' => 'Lucas Benítez',
            'tipo' => 'Minorista',
            'cuit' => '',
            'telefono' => '3743-556201',
            'email' => '',
            'localidad' => 'Puerto Rico',
        ],
        [
            'id' => 3,
            'nombre' => 'Almacén Don Rubén',
            'tipo' => 'Mayorista',
            'cuit' => '30-71234567-8',
            'telefono' => '3751-482310',
            'email' => 'compras@donruben.ejemplo.com',
            'localidad' => 'Montecarlo',
        ],
        [
            'id' => 4,
            'nombre' => 'Kiosco El Sol',
            'tipo' => 'Mayorista',
            'cuit' => '20-28456123-4',
            'telefono' => '3751-455902',
            'email' => '',
            'localidad' => 'Eldorado',
        ],
        [
            'id' => 5,
            'nombre' => 'Tienda Moda Selva',
            'tipo' => 'Mayorista',
            'cuit' => '30-70987654-3',
            'telefono' => '3755-407788',
            'email' => 'ventas@modaselva.ejemplo.com',
            'localidad' => 'Oberá',
        ],
        [
            'id' => 6,
            'nombre' => 'Carolina Acosta',
            'tipo' => 'Minorista',
            'cuit' => '',
            'telefono' => '3743-498120',
            'email' => 'caro.acosta@ejemplo.com',
            'localidad' => 'Puerto Rico',
        ],
        [
            'id' => 7,
            'nombre' => 'Bazar Los Pinos',
            'tipo' => 'Mayorista',
            'cuit' => '33-69812345-9',
            'telefono' => '3743-460033',
            'email' => '',
            'localidad' => 'Capioví',
        ],
    ];

    protected function rules(): array
    {
        return [
            'formNombre' => ['required', 'string', 'max:120'],
            'formTipo' => ['required', 'in:Minorista,Mayorista'],
            // El CUIT solo es obligatorio para clientes mayoristas.
            'formCuit' => [$this->formTipo === 'Mayorista' ? 'required' : 'nullable', 'string', 'max:20'],
            'formTelefono' => ['nullable', 'string', 'max:30'],
            'formEmail' => ['nullable', 'email', 'max:120'],
            'formLocalidad' => ['nullable', 'string', 'max:80'],
        ];
    }

    protected function messages(): array
    {
        return [
            'formNombre.required' => 'Ingresá el nombre del cliente.',
            'formCuit.required' => 'El CUIT es obligatorio para clientes mayoristas.',
            'formEmail.email' => 'Ingresá un email válido.',
        ];
    }

    public function updatedFormTipo(): void
    {
        // Al pasar a Minorista el CUIT deja de aplicar: se limpia junto con su error.
        if ($this->formTipo !== 'Mayorista') {
            $this->formCuit = '';
            $this->resetErrorBag('formCuit');
        }
    }

    public function abrirFormulario(): void
    {
        $this->reset(['formNombre', 'formCuit', 'formTelefono', 'formEmail', 'formLocalidad', 'mensajeExito']);
        $this->formTipo = 'Minorista';
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

        $nuevoId = collect($this->clientes)->max('id') + 1;
        $this->clientes[] = [
            'id' => $nuevoId,
            'nombre' => trim($this->formNombre),
            'tipo' => $this->formTipo,
            'cuit' => $this->formTipo === 'Mayorista' ? trim($this->formCuit) : '',
            'telefono' => trim($this->formTelefono),
            'email' => trim($this->formEmail),
            'localidad' => trim($this->formLocalidad),
        ];

        $this->mostrarFormulario = false;
        $this->mensajeExito = 'Cliente simulado agregado a la maqueta. No se guarda en la base de datos.';
    }

    public function limpiarFiltros(): void
    {
        $this->buscar = '';
        $this->filtroTipo = '';
    }

    /**
     * Cliente elegido en el selector de venta rápida, o null si es consumidor final.
     */
    public function clienteVentaSeleccionado(): ?array
    {
        foreach ($this->clientes as $c) {
            if ((string) $c['id'] === $this->clienteVenta) {
                return $c;
            }
        }

        return null;
    }

    private function normalizar(string $texto): string
    {
        return Str::lower(Str::ascii($texto));
    }

    private function coincide(array $c): bool
    {
        if ($this->filtroTipo !== '' && $c['tipo'] !== $this->filtroTipo) {
            return false;
        }

        $q = trim($this->buscar);
        if ($q === '') {
            return true;
        }

        // Búsqueda por nombre, sin distinguir mayúsculas ni tildes.
        if (str_contains($this->normalizar($c['nombre']), $this->normalizar($q))) {
            return true;
        }

        // Búsqueda por teléfono, ignorando guiones y espacios.
        $digitosBuscados = preg_replace('/\D+/', '', $q);

        return $digitosBuscados !== ''
            && str_contains(preg_replace('/\D+/', '', $c['telefono']), $digitosBuscados);
    }

    public function render()
    {
        $filtrados = array_filter($this->clientes, fn (array $c) => $this->coincide($c));

        return view('livewire.clientes.index', [
            'clientesFiltrados' => $filtrados,
            'clienteVentaActual' => $this->clienteVentaSeleccionado(),
        ])->layout('layouts.app', ['title' => 'Clientes']);
    }
}
