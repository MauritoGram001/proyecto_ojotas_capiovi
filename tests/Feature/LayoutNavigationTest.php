<?php

namespace Tests\Feature;

use Tests\TestCase;

class LayoutNavigationTest extends TestCase
{
    public function test_pantalla_inicio_carga_con_layout_bootstrap5_y_navbar(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Ojotas Capioví');
        $response->assertSee('Modo Visual (Fase B)');
        $response->assertSee('Nueva Venta');
        $response->assertSee('Stock & Variantes', false);
        $response->assertSee('Cuentas & Cobros', false);
        $response->assertSee('bootstrap.min.css');
        $response->assertSee('bootstrap.bundle.min.js');
    }

    public function test_rutas_de_navegacion_principales_responden(): void
    {
        $rutas = [
            '/productos',
            '/clientes',
            '/ventas/crear',
            '/cuentas-corrientes',
            '/pedidos-mayoristas',
            '/proveedores',
            '/informes',
            '/usuarios',
            '/plan-visor',
        ];

        foreach ($rutas as $ruta) {
            $response = $this->get($ruta);
            $response->assertStatus(200);
        }
    }
}
