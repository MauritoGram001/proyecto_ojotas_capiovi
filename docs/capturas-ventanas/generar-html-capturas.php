<?php

declare(strict_types=1);

$items = [
    ['file' => '01-login.png', 'title' => '1. Login', 'ruta' => '/login', 'desc' => 'Acceso público con correo y contraseña y opción “mantener sesión iniciada”.'],
    ['file' => '02-inicio-dashboard.png', 'title' => '2. Inicio (Dashboard)', 'ruta' => '/', 'desc' => 'Panel principal con accesos rápidos a los módulos y acciones de gestión.'],
    ['file' => '03-productos-existencias.png', 'title' => '3. Productos y Existencias', 'ruta' => '/productos', 'desc' => 'Catálogo con variantes (talle/color), precios minorista/mayorista, stock y alerta automática de bajo stock (< 10); permite alta de productos.'],
    ['file' => '04-clientes.png', 'title' => '4. Clientes', 'ruta' => '/clientes', 'desc' => 'Directorio minorista/mayorista con alta, edición, baja y búsqueda; CUIT obligatorio solo para mayoristas y opción de consumidor final/venta rápida.'],
    ['file' => '05-nueva-venta-pos.png', 'title' => '5. Nueva Venta (POS)', 'ruta' => '/ventas/crear', 'desc' => 'Punto de venta: selección de cliente/comprador y modo de precios, agregado de variantes, totales, descuentos/cambios de precio y registro con comprobante interno no fiscal (PDF).'],
    ['file' => '06-anulaciones-devoluciones.png', 'title' => '6. Anulaciones y Devoluciones', 'ruta' => '/ventas/postventa', 'desc' => 'Postventa: anulación de ventas con justificación y confirmación, y devoluciones por cambio vinculadas a la venta original (derivación a stock defectuoso si corresponde).'],
    ['file' => '07-cuentas-corrientes.png', 'title' => '7. Cuentas Corrientes', 'ruta' => '/cuentas-corrientes', 'desc' => 'Control de saldos y movimientos por cliente, boletas/deudas pendientes y acceso a registro de cobros e imputaciones.'],
    ['file' => '08-pedidos-mayoristas.png', 'title' => '8. Pedidos Mayoristas', 'ruta' => '/pedidos-mayoristas', 'desc' => 'Seguimiento de pedidos de clientes mayoristas por estado (pendiente/preparado/entregado/cancelado); no reservan stock hasta confirmar la venta.'],
    ['file' => '09-informes.png', 'title' => '9. Informes', 'ruta' => '/informes', 'desc' => 'Reportes consolidados de ventas por período (detalle/diario/mensual) e historial por cliente identificado, con totales y promedios.'],
    ['file' => '10-usuarios-roles.png', 'title' => '10. Usuarios y Roles', 'ruta' => '/usuarios', 'desc' => 'Administración de usuarios y roles (Administrador, Dueño, Empleado de local) con previsualización de permisos.'],
    ['file' => '11-proveedores.png', 'title' => '11. Proveedores', 'ruta' => '/proveedores', 'desc' => 'Directorio de proveedores con alta, edición, baja, búsqueda y filtro por rubro (CUIT opcional).'],
    ['file' => '12-pedidos-proveedores.png', 'title' => '12. Pedidos a Proveedores', 'ruta' => '/proveedores/pedidos', 'desc' => 'Seguimiento de pedidos/recepciones a proveedores y circuito de mercadería defectuosa/cambios.'],
];

$dir = __DIR__;
$fecha = date('d/m/Y H:i');

$sections = '';
$total = count($items);
foreach ($items as $i => $item) {
    $path = $dir . '/' . $item['file'];
    if (!is_file($path)) {
        throw new RuntimeException('No se encontró la captura: ' . $path);
    }
    $data = base64_encode((string) file_get_contents($path));
    $src = 'data:image/png;base64,' . $data;
    $title = htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8');
    $ruta = htmlspecialchars($item['ruta'], ENT_QUOTES, 'UTF-8');
    $desc = htmlspecialchars($item['desc'], ENT_QUOTES, 'UTF-8');
    $pageBreak = ($i < $total - 1) ? 'page-break-after: always;' : '';
    $sections .= <<<HTML
    <section class="ventana" style="{$pageBreak}">
      <h2>{$title}</h2>
      <p class="ruta"><strong>Ruta:</strong> <code>{$ruta}</code></p>
      <p class="desc">{$desc}</p>
      <div class="imgwrap"><img src="{$src}" alt="{$title}"></div>
    </section>
HTML;
}

$html = <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Ojotas Capioví — Capturas de ventanas</title>
<style>
  @page { margin: 18mm 14mm 18mm 14mm; }
  body { font-family: "Segoe UI", Helvetica, Arial, sans-serif; color: #222; font-size: 11pt; line-height: 1.4; margin: 0; }
  .cover h1 { font-size: 22pt; margin: 0 0 6px 0; color: #1f3b57; }
  .cover p { margin: 2px 0; color: #444; }
  .cover { margin-bottom: 18px; padding-bottom: 10px; border-bottom: 2px solid #1f3b57; }
  .nota { font-size: 9pt; color: #555; background: #f6f8fa; padding: 8px 10px; border-left: 3px solid #1f3b57; margin-top: 10px; }
  h2 { font-size: 14pt; margin: 0 0 4px 0; color: #1f3b57; }
  .ruta { margin: 0 0 4px 0; font-size: 9pt; color: #333; }
  .ruta code { background: #eef2f6; padding: 1px 4px; font-size: 9pt; }
  .desc { margin: 0 0 8px 0; }
  .imgwrap { text-align: center; border: 1px solid #d0d7de; padding: 4px; background: #fff; }
  .imgwrap img { width: 100%; height: auto; }
  .ventana { margin-bottom: 8px; }
</style>
</head>
<body>
  <div class="cover">
    <h1>Ojotas Capioví — Capturas de ventanas</h1>
    <p><strong>Sistema:</strong> Gestión interna (Laravel + Livewire + Bootstrap 5)</p>
    <p><strong>Generado:</strong> {$fecha}</p>
    <p><strong>URL local de visualización:</strong> http://127.0.0.1:8001/ (servidor local, no remoto)</p>
    <div class="nota"><strong>Nota:</strong> El proyecto corre en local (localhost). La URL http://127.0.0.1:8000/ solo funciona en la PC donde se ejecute el servidor. Capturas tomadas con usuario administrador de prueba.</div>
  </div>
  {$sections}
</body>
</html>
HTML;

$out = __DIR__ . '/ventanas-ojotas-capiovi.html';
file_put_contents($out, $html);
echo $out;
