<?php

namespace Tests\Feature;

use Tests\TestCase;

class VisualReviewTest extends TestCase
{
    /** @var array<string, string> */
    private array $rutas = [
        'inicio' => '/',
        'productos' => '/productos',
        'clientes' => '/clientes',
        'proveedores' => '/proveedores',
        'pedidos de proveedores' => '/proveedores/pedidos',
        'ventas' => '/ventas/crear',
        'postventa' => '/ventas/postventa',
        'cuentas corrientes' => '/cuentas-corrientes',
        'pedidos mayoristas' => '/pedidos-mayoristas',
        'informes' => '/informes',
        'usuarios' => '/usuarios',
    ];

    public function test_todas_las_vistas_son_adaptables_y_tienen_navegacion_accesible(): void
    {
        foreach ($this->rutas as $nombre => $ruta) {
            $response = $this->get($ruta);

            $response->assertStatus(200, "La vista {$nombre} no responde correctamente");
            $response->assertSee('name="viewport"', false);
            $response->assertSee('aria-label="Abrir menú"', false);
            $response->assertSee('id="mainNavbarOffcanvas"', false);
        }
    }

    public function test_los_controles_de_filtros_y_formularios_tienen_etiqueta_accesible(): void
    {
        $controles = [
            '/productos' => ['Filtrar productos por categoría', 'Filtrar productos por nivel de stock'],
            '/clientes' => ['Comprador para venta rápida', 'Filtrar clientes por tipo'],
            '/proveedores' => ['Filtrar proveedores por rubro'],
            '/proveedores/pedidos' => ['Filtrar pedidos de proveedores por estado'],
            '/ventas/crear' => ['Cliente o comprador no identificado', 'Variante: modelo, talle y color'],
            '/pedidos-mayoristas' => ['Filtrar pedidos mayoristas por estado'],
            '/informes' => ['Fecha desde', 'Fecha hasta', 'Vista del informe'],
            '/usuarios' => ['Rol de ', 'Rol para vista previa de permisos'],
        ];

        foreach ($controles as $ruta => $etiquetas) {
            $response = $this->get($ruta);

            $response->assertStatus(200);
            foreach ($etiquetas as $etiqueta) {
                $response->assertSee('aria-label="'.$etiqueta, false);
            }
        }
    }

    public function test_los_mensajes_de_error_son_anunciados_por_lectores_de_pantalla(): void
    {
        $response = $this->withSession(['warning' => 'Revisá los datos ingresados'])
            ->get('/productos');

        $response->assertStatus(200);
        $response->assertSee('role="alert"', false);
        $response->assertSee('Revisá los datos ingresados');
    }

    public function test_el_menu_expandido_no_genera_scroll_horizontal_en_escritorio(): void
    {
        $layout = file_get_contents(resource_path('views/layouts/app.blade.php'));

        $this->assertStringContainsString('@media (min-width: 992px)', $layout);
        $this->assertStringContainsString('.navbar .offcanvas-body', $layout);
        $this->assertStringContainsString('flex-wrap: wrap;', $layout);
        $this->assertStringContainsString('max-width: 100%;', $layout);
    }
}
