<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    public const TIPO_MINORISTA = 'Minorista';
    public const TIPO_MAYORISTA = 'Mayorista';

    /** @var list<string> */
    public const TIPOS = [self::TIPO_MINORISTA, self::TIPO_MAYORISTA];

    protected $fillable = [
        'nombre',
        'tipo',
        'telefono',
        'email',
        'direccion',
        'cuit',
    ];
}
