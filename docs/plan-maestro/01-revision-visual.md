# Revisión visual integral (VIS-011)

Fecha: 2026-11-20
Alcance: todas las vistas de la Fase B con datos ficticios, en viewport de celular (390 px) y escritorio (1200 px).

## Metodología

Se levantó el servidor local de Laravel y se inspeccionaron las 11 pantallas (`/`, `/productos`, `/clientes`, `/proveedores`, `/proveedores/pedidos`, `/ventas/crear`, `/ventas/postventa`, `/cuentas-corrientes`, `/pedidos-mayoristas`, `/informes`, `/usuarios`) dentro de iframes con ambos anchos de viewport. Para cada combinación se midió `document.documentElement.clientWidth` frente a `scrollWidth`, se relevó la lista de controles sin etiqueta programática (`label`, `aria-label`, `placeholder`, `title` o `aria-labelledby`) y se revisaron estados vacíos y mensajes de error.

## Hallazgos y ajustes

1. **Desbordamiento horizontal en escritorio (todas las vistas).** El menú expandido de Bootstrap (`navbar-expand-lg .offcanvas`) dejaba su contenido sin ajustar: `scrollWidth` superaba al ancho visible en ~55 px. Se ajustó `resources/views/layouts/app.blade.php` con `flex-wrap: wrap`, `row-gap` y `max-width: 100%` para `.navbar .offcanvas-body`, `.navbar .navbar-nav` y sus enlaces en `@media (min-width: 992px)`.
2. **18 controles sin etiqueta accesible** en 8 vistas (filtros de productos, clientes, proveedores, pedidos de proveedores, pedidos mayoristas, informes, selects del POS y selector/roles de usuarios). Se agregó `aria-label` descriptivo en cada control, sin incorporar elementos visuales nuevos.
3. **Estados vacíos.** Confirmados en las tablas y paneles: productos, clientes, proveedores, pedidos, ventas, cuentas, informes e historial muestran mensajes concretos y acción de limpiar filtros cuando corresponde.
4. **Mensajes de error.** Los avisos `success`, `warning` y `error` del layout se renderizan con `role="alert"` y botón de cierre con `aria-label="Cerrar"`; los formularios de la fase visual informan las validaciones simuladas por ese canal.
5. **Simplicidad y navegación.** Se mantuvo un solo nivel de filtros por pantalla, botones táctiles (`btn-touch`) en celular y tablas dentro de `.table-responsive`; no se agregaron componentes decorativos ni gráficos superfluos.

## Verificación posterior

- Las 22 combinaciones (11 vistas × 2 viewports) terminan sin desbordamiento horizontal (`scrollWidth <= clientWidth`).
- Las 22 combinaciones terminan sin controles sin etiqueta programática.
- Pruebas de regresión: `tests/Feature/VisualReviewTest.php` (4 tests) valida viewport, navegación accesible, etiquetas, mensajes `role="alert"` y la regla CSS que evita el scroll horizontal.
- Suite completa: ver evidencia registrada en `01-vistas.md` para VIS-011.

Fuera de alcance: lógica de negocio, persistencia y pruebas de base de datos (corresponden a la Fase C).
