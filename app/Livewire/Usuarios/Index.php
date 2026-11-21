<?php

namespace App\Livewire\Usuarios;

use Livewire\Component;

class Index extends Component
{
    public array $usuarios = [
        ['id' => 1, 'nombre' => 'Mauricio Gramajo', 'email' => 'mauricio@ojotascapiovi.com.ar', 'rol' => 'Dueño', 'estado' => 'Activo', 'ultimoAcceso' => '20/11/2026 09:35'],
        ['id' => 2, 'nombre' => 'Ana Paiva', 'email' => 'ana@ojotascapiovi.com.ar', 'rol' => 'Administrador', 'estado' => 'Activo', 'ultimoAcceso' => '20/11/2026 10:12'],
        ['id' => 3, 'nombre' => 'Jorge Benítez', 'email' => 'jorge@ojotascapiovi.com.ar', 'rol' => 'Empleado de local', 'estado' => 'Activo', 'ultimoAcceso' => '20/11/2026 11:48'],
        ['id' => 4, 'nombre' => 'Lucía Ferreyra', 'email' => 'lucia@ojotascapiovi.com.ar', 'rol' => 'Empleado de local', 'estado' => 'Inactivo', 'ultimoAcceso' => '18/11/2026 16:20'],
    ];

    public array $roles = [
        'Dueño' => [
            'descripcion' => 'Acceso total a la gestión del negocio.',
            'modulos' => [
                'Ventas (POS y comprobantes)',
                'Productos y existencias',
                'Clientes y proveedores',
                'Cuentas y cobros',
                'Pedidos mayoristas',
                'Informes e historial',
                'Usuarios y permisos',
            ],
            'restricciones' => [],
        ],
        'Administrador' => [
            'descripcion' => 'Gestión operativa diaria del negocio.',
            'modulos' => [
                'Ventas (POS y comprobantes)',
                'Productos y existencias',
                'Clientes y proveedores',
                'Cuentas y cobros',
                'Pedidos mayoristas',
                'Informes e historial',
            ],
            'restricciones' => ['No puede modificar el rol del usuario Dueño'],
        ],
        'Empleado de local' => [
            'descripcion' => 'Operación de mostrador y consultas básicas.',
            'modulos' => [
                'Ventas (POS y comprobantes)',
                'Productos y existencias (consulta)',
                'Clientes (consulta)',
                'Cuentas y cobros (consulta)',
            ],
            'restricciones' => [
                'Sin acceso a informes e historial',
                'Sin acceso a administración de usuarios',
            ],
        ],
    ];

    // Invariante VIS-009: todos los roles autenticados pueden aplicar descuentos
    // o cambiar el precio cobrado en una venta.
    public array $permisoCompartido = [
        'titulo' => 'Descuentos y cambio de precio en ventas',
        'detalle' => 'Disponible para Dueño, Administrador y Empleado de local. No requiere autorización adicional.',
    ];

    public string $rolVistaPrevia = 'Dueño';

    public bool $mostrarFormulario = false;
    public ?int $usuarioEditando = null;
    public string $formNombre = '';
    public string $formEmail = '';
    public string $formRol = 'Empleado de local';

    public array $modulosNav = [
        'Inicio',
        'Ventas',
        'Productos & Stock',
        'Clientes',
        'Cuentas & Cobros',
        'Pedidos Mayoristas',
        'Proveedores & Compras',
        'Informes',
        'Usuarios',
    ];

    public function abrirFormulario(?int $usuarioId = null): void
    {
        $this->usuarioEditando = $usuarioId;

        if ($usuarioId !== null) {
            foreach ($this->usuarios as $usuario) {
                if ($usuario['id'] === $usuarioId) {
                    $this->formNombre = $usuario['nombre'];
                    $this->formEmail = $usuario['email'];
                    $this->formRol = $usuario['rol'];
                    break;
                }
            }
        } else {
            $this->formNombre = '';
            $this->formEmail = '';
            $this->formRol = 'Empleado de local';
        }

        $this->mostrarFormulario = true;
    }

    public function cerrarFormulario(): void
    {
        $this->mostrarFormulario = false;
        $this->usuarioEditando = null;
    }

    public function guardarUsuario(): void
    {
        if (trim($this->formNombre) === '' || trim($this->formEmail) === '') {
            session()->flash('warning', 'Completá el nombre y el correo del usuario.');

            return;
        }

        if (! isset($this->roles[$this->formRol])) {
            session()->flash('warning', 'Seleccioná un rol válido.');

            return;
        }

        if ($this->usuarioEditando !== null) {
            foreach ($this->usuarios as $index => $usuario) {
                if ($usuario['id'] === $this->usuarioEditando) {
                    $this->usuarios[$index]['nombre'] = trim($this->formNombre);
                    $this->usuarios[$index]['email'] = trim($this->formEmail);
                    $this->usuarios[$index]['rol'] = $this->formRol;
                    session()->flash('success', 'Usuario actualizado (simulación).');
                    break;
                }
            }
        } else {
            $nuevoId = count($this->usuarios) + 1;
            $this->usuarios[] = [
                'id' => $nuevoId,
                'nombre' => trim($this->formNombre),
                'email' => trim($this->formEmail),
                'rol' => $this->formRol,
                'estado' => 'Activo',
                'ultimoAcceso' => '—',
            ];
            session()->flash('success', 'Usuario creado con rol ' . $this->formRol . ' (simulación).');
        }

        $this->cerrarFormulario();
    }

    public function cambiarRolUsuario(int $usuarioId, string $rol): void
    {
        if (! isset($this->roles[$rol])) {
            return;
        }

        foreach ($this->usuarios as $index => $usuario) {
            if ($usuario['id'] === $usuarioId) {
                $this->usuarios[$index]['rol'] = $rol;
                session()->flash('success', 'Rol actualizado a ' . $rol . ' (simulación).');

                return;
            }
        }
    }

    public function toggleEstado(int $usuarioId): void
    {
        foreach ($this->usuarios as $index => $usuario) {
            if ($usuario['id'] === $usuarioId) {
                $this->usuarios[$index]['estado'] = $usuario['estado'] === 'Activo' ? 'Inactivo' : 'Activo';

                return;
            }
        }
    }

    public function render()
    {
        $rol = $this->roles[$this->rolVistaPrevia] ?? $this->roles['Dueño'];

        $navAcceso = [];
        foreach ($this->modulosNav as $modulo) {
            $navAcceso[$modulo] = $this->moduloHabilitado($modulo, $rol);
        }

        return view('livewire.usuarios.index', [
            'permisosRol' => $rol,
            'navAcceso' => $navAcceso,
        ])->layout('layouts.app', ['title' => 'Usuarios y Permisos']);
    }

    private function moduloHabilitado(string $modulo, array $rol): bool
    {
        $mapa = [
            'Inicio' => null,
            'Ventas' => 'Ventas',
            'Productos & Stock' => 'Productos',
            'Clientes' => 'Clientes',
            'Cuentas & Cobros' => 'Cuentas',
            'Pedidos Mayoristas' => 'Pedidos',
            'Proveedores & Compras' => 'proveedores',
            'Informes' => 'Informes',
            'Usuarios' => 'Usuarios',
        ];

        $prefijo = $mapa[$modulo] ?? null;
        if ($prefijo === null) {
            return true;
        }

        foreach ($rol['modulos'] as $moduloPermitido) {
            if (str_contains($moduloPermitido, $prefijo)) {
                return true;
            }
        }

        return false;
    }
}
