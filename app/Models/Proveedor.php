<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $table = 'proveedores';

    /** @var list<string> */
    public const RUBROS = ['Calzado', 'Textil', 'Insumos', 'Logística'];

    protected $fillable = [
        'razon_social',
        'cuit',
        'contacto',
        'telefono',
        'email',
        'direccion',
        'rubro',
    ];
}
