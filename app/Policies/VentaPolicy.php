<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Venta;

class VentaPolicy
{
    /**
     * Cualquier usuario autenticado puede consultar y registrar ventas.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Venta $venta): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    /**
     * RF 19 / DEC-2026-11-19-05: todo usuario autenticado puede aplicar
     * descuentos en la venta en curso, sin importar su rol.
     */
    public function aplicarDescuento(User $user): bool
    {
        return true;
    }

    /**
     * RF 19 / DEC-2026-11-19-05: todo usuario autenticado puede modificar el
     * precio unitario de la venta en curso.
     */
    public function cambiarPrecio(User $user): bool
    {
        return true;
    }

    /**
     * Las correcciones posteriores a la venta quedan reservadas a quienes
     * administran la operación o la caja.
     */
    public function anular(User $user, Venta $venta): bool
    {
        return $user->esAdministrador() || $user->esDueno();
    }
}
