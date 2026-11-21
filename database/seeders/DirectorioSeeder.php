<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\Proveedor;
use Illuminate\Database\Seeder;

class DirectorioSeeder extends Seeder
{
    public function run(): void
    {
        $clientes = [
            ['nombre' => 'María González', 'tipo' => Cliente::TIPO_MINORISTA, 'telefono' => '3765 412345', 'email' => 'maria.gonzalez@email.com', 'direccion' => 'Av. Libertad 1234, Capioví', 'cuit' => null],
            ['nombre' => 'Carlos Ruiz', 'tipo' => Cliente::TIPO_MINORISTA, 'telefono' => '3765 489012', 'email' => 'carlos.ruiz@email.com', 'direccion' => 'Calle Mitre 456, Capioví', 'cuit' => null],
            ['nombre' => 'Distribuidora Norte S.R.L.', 'tipo' => Cliente::TIPO_MAYORISTA, 'telefono' => '3765 501234', 'email' => 'ventas@disnorte.com.ar', 'direccion' => 'Ruta 12 Km 8, Puerto Iguazú', 'cuit' => '30-71234567-8'],
            ['nombre' => 'Comercial El Sol S.A.', 'tipo' => Cliente::TIPO_MAYORISTA, 'telefono' => '3765 557890', 'email' => 'compras@elsol.com.ar', 'direccion' => 'Av. Misiones 987, Posadas', 'cuit' => '30-70876543-2'],
            ['nombre' => 'Lucía Fernández', 'tipo' => Cliente::TIPO_MINORISTA, 'telefono' => '3764 223311', 'email' => 'lucia.fernandez@email.com', 'direccion' => 'Barrio Centro, Capioví', 'cuit' => null],
        ];

        foreach ($clientes as $cliente) {
            Cliente::updateOrCreate(['nombre' => $cliente['nombre']], $cliente);
        }

        $proveedores = [
            ['razon_social' => 'Fábrica de Calzados Misiones S.R.L.', 'cuit' => '30-71223344-5', 'contacto' => 'Ing. Roberto Acosta', 'telefono' => '3765 443322', 'email' => 'ventas@calzadosmisiones.com.ar', 'direccion' => 'Parque Industrial, Capioví', 'rubro' => 'Calzado'],
            ['razon_social' => 'Textiles del Sur S.A.', 'cuit' => '30-71556677-9', 'contacto' => 'Ana Paiva', 'telefono' => '3765 498877', 'email' => 'compras@textilesdelsur.com.ar', 'direccion' => 'Av. Industrial 2200, Posadas', 'rubro' => 'Textil'],
            ['razon_social' => 'Insumos Capioví', 'cuit' => '20-31998877-1', 'contacto' => 'Jorge Benítez', 'telefono' => '3765 511900', 'email' => 'insumoscapiovi@email.com', 'direccion' => 'Calle Comercio 118, Capioví', 'rubro' => 'Insumos'],
            ['razon_social' => 'Transportes Guaraní', 'cuit' => '30-70443322-6', 'contacto' => 'Silvia Ramos', 'telefono' => '3765 477123', 'email' => 'logistica@guarani.com.ar', 'direccion' => 'Ruta 12 Km 4, Puerto Iguazú', 'rubro' => 'Logística'],
        ];

        foreach ($proveedores as $proveedor) {
            Proveedor::updateOrCreate(['razon_social' => $proveedor['razon_social']], $proveedor);
        }
    }
}
