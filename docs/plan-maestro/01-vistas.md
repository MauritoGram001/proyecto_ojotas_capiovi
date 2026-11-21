# Fase B. Vistas y Experiencia de Usuario

<!-- plan-schema: 1 -->

Construcción de la interfaz de usuario con Bootstrap 5 y Livewire, priorizando maquetación limpia, adaptación móvil (RNF 06) y datos ficticios simulados antes de la implementación funcional (DEC-2026-11-19-04).

## Tareas

### [HECHO] VIS-001 — Layout y navegación

- **Capa:** SOLICITADO
- **Fuente:** DEC-2026-11-19-02, RNF 01, RNF 02, RNF 05, RNF 06
- **Contexto propietario:** Interfaz y experiencia de usuario
- **Dependencias:** INI-002, INI-003
- **Alcance:** Diseñar e implementar el layout principal en Blade con Bootstrap 5 nativo, barra de navegación superior/lateral simplificada, menú adaptable a dispositivos móviles (offcanvas/hamburguesa) y estructura base para alertas y estados del sistema.
- **Fuera de alcance:** Implementación de menús multinivel complejos, temas oscuros dinámicos o componentes no esenciales.
- **Invariantes:** Interfaz sencilla, legible, libre de sobrecarga visual y con foco en usabilidad móvil (RNF 06).
- **Contratos y archivos previstos:** resources/views/layouts/app.blade.php, resources/views/components/navbar.blade.php
- **Criterios de aceptación:** Layout responsivo verificado en resoluciones móviles y de escritorio; navegación rápida sin recargas pesadas; menú con accesos directos a las áreas principales sin submenús profundos.
- **Responsable:** mauricio-chat
- **Fecha de inicio:** 2026-11-19
- **Última actualización:** 2026-11-19
- **Próximo paso:** Tarea completada. Proceder con VIS-002 (Productos, precios y stock).
- **Evidencia actual:** Layout principal implementado en resources/views/layouts/app.blade.php con Bootstrap 5.3.3 y Bootstrap Icons locales; barra de navegación responsiva con offcanvas en resources/views/components/navbar.blade.php; pantalla de inicio con accesos rápidos táctiles en resources/views/inicio.blade.php; rutas configuradas en routes/web.php; prueba automatizada tests/Feature/LayoutNavigationTest con 2 tests y 17 aserciones aprobadas.

### [HECHO] VIS-002 — Productos, precios y stock

- **Capa:** SOLICITADO
- **Fuente:** RF 02, RF 04, RF 05, RF 13, RF 18, DEC-2026-11-19-05
- **Contexto propietario:** Catálogo y existencias
- **Dependencias:** VIS-001
- **Alcance:** Construir las vistas y componentes Livewire con datos simulados para catálogo de productos, grilla de variantes (talle y color), precios diferenciados para mayorista y minorista, visualización de existencias y distintivos visuales para bajo stock (< 10 unidades).
- **Fuera de alcance:** Modificación real de existencias en base de datos.
- **Invariantes:** Mostrar únicamente columnas esenciales en la tabla y llevar detalles al expandir o modal; indicar claramente la condición de bajo stock.
- **Contratos y archivos previstos:** resources/views/livewire/productos/index.blade.php, resources/views/livewire/productos/formulario.blade.php
- **Criterios de aceptación:** Vistas interactivas con datos ficticios que permitan filtrar por modelo, talle y color; visualización clara de precios minorista y mayorista; alertas de bajo stock visibles cuando el stock simulado sea menor a 10.
- **Responsable:** mauricio-chat
- **Fecha de inicio:** 2026-11-19
- **Última actualización:** 2026-11-19
- **Próximo paso:** Tarea completada. Proceder con VIS-003 (Clientes y proveedores).
- **Evidencia actual:** Componente Livewire App\Livewire\Productos\Index implementado en app/Livewire/Productos/Index.php y resources/views/livewire/productos/index.blade.php; catálogo interactivo con calzado y textil, precios mayorista/minorista, grilla modal de variantes (talle y color) y distintivo de alerta de bajo stock (<10); prueba automatizada tests/Feature/ProductosViewTest con 4 tests y 14 aserciones aprobadas.

### [HECHO] VIS-003 — Clientes y proveedores

- **Capa:** SOLICITADO
- **Fuente:** RF 01, RF 07, RNF 05
- **Contexto propietario:** Directorio comercial
- **Dependencias:** VIS-001
- **Alcance:** Diseñar las pantallas de listado y formulario de clientes (minoristas y mayoristas) y proveedores con datos de contacto. Contemplar campo CUIT obligatorio condicional únicamente para mayoristas y simulación de selector para venta rápida sin identificar comprador.
- **Fuera de alcance:** Validación real contra padrón fiscal de AFIP o sincronizaciones externas.
- **Invariantes:** Formularios compactos; campos adicionales solo cuando correspondan al tipo de cliente; etiquetas claras.
- **Contratos y archivos previstos:** resources/views/livewire/clientes/index.blade.php, resources/views/livewire/proveedores/index.blade.php
- **Criterios de aceptación:** Formulario de cliente que active el campo CUIT al seleccionar "Mayorista" y lo oculte en "Minorista"; listado claro con búsqueda por nombre o teléfono; vista de proveedores con datos esenciales de contacto.
- **Responsable:** opencode-big-pickle-local
- **Fecha de inicio:** 2026-11-20
- **Última actualización:** 2026-11-20
- **Próximo paso:** Tarea completada. Proceder con VIS-004 (Ventas y comprobantes).
- **Evidencia actual:** Componentes Livewire App\Livewire\Clientes\Index y App\Livewire\Proveedores\Index implementados en app/Livewire/Clientes/Index.php y app/Livewire/Proveedores/Index.php con vistas resources/views/livewire/clientes/index.blade.php y resources/views/livewire/proveedores/index.blade.php; listados con datos ficticios, búsqueda por nombre o teléfono, alta simulada, campo CUIT condicional activo solo para mayoristas, selector de venta rápida con Consumidor Final y datos esenciales de contacto de proveedores; rutas /clientes y /proveedores actualizadas en routes/web.php; pruebas tests/Feature/ClientesViewTest y tests/Feature/ProveedoresViewTest con 9 tests aprobados (suite completa: 17 tests, 63 aserciones).

### [HECHO] VIS-004 — Ventas y comprobantes

- **Capa:** SOLICITADO
- **Fuente:** RF 03, RF 06, RF 18, RF 19, DEC-2026-11-19-05
- **Contexto propietario:** Mostrador y ventas
- **Dependencias:** VIS-001, VIS-002, VIS-003
- **Alcance:** Diseñar la pantalla de venta (POS simplificado para mostrador y celular), selector de cliente o comprador no identificado, carga de líneas de producto por variante, aplicación interactiva de precios mayorista/minorista, campo para cambio manual de precio/descuento y previsualización de comprobante interno no fiscal con botón de descarga simulada en PDF.
- **Fuera de alcance:** Envío por correo/WhatsApp o impresión térmica física.
- **Invariantes:** Comprobantes no fiscales de carácter estrictamente interno; cambio manual de precio presentado con advertencia visual de que afecta solo la operación en curso.
- **Contratos y archivos previstos:** resources/views/livewire/ventas/crear.blade.php, resources/views/pdf/comprobante-interno.blade.php
- **Criterios de aceptación:** Interfaz ágil utilizable desde teléfonos móviles; cálculo dinámico de totales con descuentos; simulación del diseño de comprobante PDF interno con modelo, talle, color y precio cobrado.
- **Responsable:** opencode-big-pickle-local
- **Fecha de inicio:** 2026-11-20
- **Última actualización:** 2026-11-20
- **Próximo paso:** Tarea completada. Proceder con VIS-005 (Cuentas y pagos).
- **Evidencia actual:** Componente Livewire App\Livewire\Ventas\Crear implementado en app/Livewire/Ventas/Crear.php y resources/views/livewire/ventas/crear.blade.php; POS adaptable a celular con selector de comprador (incluye Consumidor Final), modo de precios minorista/mayorista con recálculo de líneas no editadas, carga de variantes modelo/talle/color, edición de precio cobrado con advertencia de alcance solo a la operación en curso, descuentos y totales dinámicos; previsualización del diseño de comprobante interno no fiscal mediante resources/views/pdf/comprobante-interno.blade.php y botón de descarga PDF simulada; ruta /ventas/crear actualizada; pruebas tests/Feature/VentasViewTest con 8 tests aprobados (suite completa: 25 tests, 92 aserciones).

### [HECHO] VIS-005 — Cuentas y pagos

- **Capa:** SOLICITADO
- **Fuente:** RF 09, RF 14, RF 16, RF 20, RF 21, DEC-2026-11-19-05
- **Contexto propietario:** Cobranzas y finanzas
- **Dependencias:** VIS-001, VIS-003, VIS-004
- **Alcance:** Diseñar vistas de cuenta corriente de clientes y deudas con proveedores; advertencia no bloqueante de boleta pendiente en ventas; formulario de cobro combinado (efectivo, transferencia y cheque básico sin cartera bancaria) e imputación a múltiples boletas.
- **Fuera de alcance:** Pasarelas de pago online, cálculo de intereses automáticos o gestión de cheques rechazados.
- **Invariantes:** La advertencia de boleta pendiente no bloquea ni pide contraseña; muestra claramente que se puede continuar.
- **Contratos y archivos previstos:** resources/views/livewire/cuentas-corrientes/index.blade.php, resources/views/livewire/cobros/formulario.blade.php
- **Criterios de aceptación:** Pantalla de saldo y movimientos por cliente; modal/alerta informativa de deuda previa sin botones de bloqueo; formulario para desglosar pago en varios medios y aplicarlo a boletas seleccionadas.
- **Responsable:** opencode-big-pickle-local
- **Fecha de inicio:** 2026-11-20
- **Última actualización:** 2026-11-20
- **Próximo paso:** Tarea completada. Proceder con VIS-006 (Pedidos a proveedores y cambios).
- **Evidencia actual:** Componente Livewire App\Livewire\CuentasCorrientes\Index con vista resources/views/livewire/cuentas-corrientes/index.blade.php para saldo y movimientos por cliente y deudas con proveedores; componente anidado App\Livewire\Cobros\Formulario con vista resources/views/livewire/cobros/formulario.blade.php para cobro combinado (efectivo, transferencia y cheque básico sin cartera) con imputación a múltiples boletas y validación de coincidencia de montos; advertencia informativa no bloqueante de boleta pendiente agregada al POS sin contraseña ni botones de bloqueo; ruta /cuentas-corrientes actualizada; pruebas tests/Feature/CuentasCorrientesViewTest, tests/Feature/CobrosFormularioTest y test adicional en tests/Feature/VentasViewTest (9 tests nuevos aprobados, suite completa: 34 tests, 125 aserciones).

### [HECHO] VIS-006 — Pedidos a proveedores y cambios

- **Capa:** SOLICITADO
- **Fuente:** RF 11, RF 12, DEC-2026-11-19-05
- **Contexto propietario:** Compras y abastecimiento
- **Dependencias:** VIS-001, VIS-002, VIS-003
- **Alcance:** Diseñar pantallas de seguimiento de pedidos a proveedores con estados visuales (pendiente, recibido) y sección de mercadería defectuosa o enviada a cambio con indicación de su estado.
- **Fuera de alcance:** Integración electrónica B2B con proveedores.
- **Invariantes:** Claridad en el estado del pedido y coherencia con el circuito de cambio de mercadería fallada.
- **Contratos y archivos previstos:** resources/views/livewire/proveedores/pedidos.blade.php, resources/views/livewire/proveedores/cambios.blade.php
- **Criterios de aceptación:** Vistas interactivas con listado de pedidos y formulario de registro de mercadería defectuosa; estados visibles mediante insignias de texto claras.
- **Responsable:** opencode-big-pickle-local
- **Fecha de inicio:** 2026-11-20
- **Última actualización:** 2026-11-20
- **Próximo paso:** Tarea completada. Proceder con VIS-007 (Pedidos mayoristas).
- **Evidencia actual:** Componente Livewire App\Livewire\Proveedores\Pedidos con vistas resources/views/livewire/proveedores/pedidos.blade.php y resources/views/livewire/proveedores/cambios.blade.php; listado de pedidos con filtro por estado e insignias claras Pendiente/Recibido, acción simulada de marcado como recibido, registro de mercadería defectuosa con circuito Detectada → Enviada a cambio → Recibida en reemplazo y avance manual de estado; ruta /proveedores/pedidos y enlace desde la vista de proveedores; pruebas tests/Feature/PedidosCambiosViewTest con 7 tests aprobados (suite completa: 41 tests, 147 aserciones).

### [HECHO] VIS-007 — Pedidos mayoristas

- **Capa:** SOLICITADO
- **Fuente:** RF 17
- **Contexto propietario:** Ventas mayoristas
- **Dependencias:** VIS-001, VIS-002, VIS-003
- **Alcance:** Diseñar la vista de pedidos de clientes mayoristas con estados (pendiente, preparado, entregado, cancelado) e indicador visual explícito que aclare que el pedido no reserva stock.
- **Fuera de alcance:** Control de carga por vehículo o depósitos móviles en camionetas.
- **Invariantes:** Los pedidos no descuentan stock; la pantalla debe mostrar esta regla para evitar malentendidos operativos.
- **Contratos y archivos previstos:** resources/views/livewire/pedidos-mayoristas/index.blade.php, resources/views/livewire/pedidos-mayoristas/detalle.blade.php
- **Criterios de aceptación:** Pantalla de gestión de pedidos mayoristas con cambio de estado visual; nota aclaratoria de no reserva de stock; botón para simular conversión a venta confirmada.
- **Responsable:** opencode-big-pickle-local
- **Fecha de inicio:** 2026-11-20
- **Última actualización:** 2026-11-20
- **Próximo paso:** Tarea completada. Proceder con VIS-008 (Informes e historial).
- **Evidencia actual:** Componente Livewire App\Livewire\PedidosMayoristas\Index con vistas resources/views/livewire/pedidos-mayoristas/index.blade.php y resources/views/livewire/pedidos-mayoristas/detalle.blade.php; listado con estados Pendiente/Preparado/Entregado/Cancelado mediante insignias, filtro por estado, aviso explícito y reiterado en el detalle de que los pedidos NO reservan stock, detalle de variantes con totales y botón de conversión simulada a venta confirmada; ruta /pedidos-mayoristas actualizada; pruebas tests/Feature/PedidosMayoristasViewTest con 6 tests aprobados (suite completa: 47 tests, 167 aserciones).

### [HECHO] VIS-008 — Informes e historial

- **Capa:** SOLICITADO
- **Fuente:** RF 08, RF 15
- **Contexto propietario:** Gestión e informes
- **Dependencias:** VIS-001, VIS-004, VIS-005
- **Alcance:** Diseñar pantallas de consulta de ventas por día y período, historial de compras por cliente identificado e informes diarios y mensuales simplificados para dueño y contador (carácter interno).
- **Fuera de alcance:** Dashboards saturados de gráficos o exportaciones fiscales a sistemas externos.
- **Invariantes:** Diseños limpios con tablas ordenadas, filtros por fecha y subtotales directos, sin saturación visual.
- **Contratos y archivos previstos:** resources/views/livewire/informes/ventas-periodo.blade.php, resources/views/livewire/informes/historial-cliente.blade.php
- **Criterios de aceptación:** Pantallas de reporte con filtros de fechas que presenten totales consolidados claros, sin elementos decorativos pesados.
- **Responsable:** opencode-big-pickle-local
- **Fecha de inicio:** 2026-11-20
- **Última actualización:** 2026-11-20
- **Próximo paso:** Tarea completada. Proceder con VIS-009 (Usuarios y permisos).
- **Evidencia actual:** Componente Livewire App\Livewire\Informes\Index con vistas resources/views/livewire/informes/ventas-periodo.blade.php y resources/views/livewire/informes/historial-cliente.blade.php; consulta de ventas por día y período con filtros de fecha (desde/hasta y presets Hoy/Semana/Mes), totales consolidados (total del período, cantidad de ventas y ticket promedio) y subtotales directos en vistas detalle, resumen diario y resumen mensual; historial de compras por cliente identificado (excluye Consumidor Final) con total acumulado, cantidad, promedio y última compra; diseño limpio con tablas ordenadas y sin gráficos decorativos ni exportaciones fiscales; ruta /informes actualizada; pruebas tests/Feature/InformesViewTest con 6 tests aprobados (suite completa: 53 tests, 197 aserciones).

### [HECHO] VIS-009 — Usuarios y permisos

- **Capa:** SOLICITADO
- **Fuente:** RF 10, DEC-2026-11-19-05
- **Contexto propietario:** Seguridad y administración
- **Dependencias:** VIS-001
- **Alcance:** Diseñar la vista de administración de usuarios y asignación de roles (Administrador, Dueño, Empleado de local), reflejando las diferencias visuales en la interfaz según el rol simulado.
- **Fuera de alcance:** Mecanismos complejos de 2FA o proveedores de identidad OAuth.
- **Invariantes:** Mostrar que todos los roles autenticados tienen acceso a la acción de descuento o cambio de precio en ventas.
- **Contratos y archivos previstos:** resources/views/livewire/usuarios/index.blade.php, resources/views/livewire/usuarios/formulario.blade.php
- **Criterios de aceptación:** Pantalla de usuarios clara con selección de rol; previsualización de permisos asociados a cada rol.
- **Responsable:** opencode-big-pickle-local
- **Fecha de inicio:** 2026-11-20
- **Última actualización:** 2026-11-20
- **Próximo paso:** Tarea completada. Proceder con VIS-010 (Anulaciones y devoluciones).
- **Evidencia actual:** Componente Livewire App\Livewire\Usuarios\Index con vistas resources/views/livewire/usuarios/index.blade.php y resources/views/livewire/usuarios/formulario.blade.php; listado de usuarios con cambio de rol (Dueño, Administrador, Empleado de local), activación/desactivación y alta/edición simulada con validación de nombre y correo; previsualización de permisos por rol con módulos accesibles, restricciones y vista previa del menú (acceso/bloqueo); invariante de descuento y cambio de precio en ventas mostrada explícitamente como disponible para todos los roles autenticados, sin 2FA ni OAuth; ruta /usuarios actualizada; pruebas tests/Feature/UsuariosViewTest con 9 tests aprobados (suite completa: 62 tests, 229 aserciones).

### [HECHO] VIS-010 — Anulaciones y devoluciones

- **Capa:** SOLICITADO
- **Fuente:** RF 22, DEC-2026-11-19-05
- **Contexto propietario:** Postventa y correcciones
- **Dependencias:** VIS-001, VIS-002, VIS-004
- **Alcance:** Diseñar pantallas para registrar anulación de ventas y devoluciones por cambio, con opción para indicar falla de fabricación e ingreso simulado a "Stock defectuoso".
- **Fuera de alcance:** Procesos de garantía externa o peritajes técnicos.
- **Invariantes:** Confirmación obligatoria previa a anular; visualización del vínculo entre la venta original y la devolución.
- **Contratos y archivos previstos:** resources/views/livewire/ventas/devolucion.blade.php, resources/views/livewire/ventas/anular-modal.blade.php
- **Criterios de aceptación:** Flujo interactivo de anulación con modal de confirmación justificado; formulario de cambio de producto con selección del artículo devuelto y del nuevo entregado.
- **Responsable:** opencode-big-pickle-local
- **Fecha de inicio:** 2026-11-20
- **Última actualización:** 2026-11-20
- **Próximo paso:** Tarea completada. Proceder con VIS-011 (Revisión visual integral).
- **Evidencia actual:** Componente Livewire App\Livewire\Ventas\Postventa con vistas resources/views/livewire/ventas/devolucion.blade.php y resources/views/livewire/ventas/anular-modal.blade.php; listado de ventas con estados Confirmada/Anulada/Devuelta y flujo de anulación con modal que exige justificación y confirmación explícita (botón deshabilitado hasta completar ambos), vinculando el registro ANU-xxx con la venta original; formulario de devolución por cambio con selección de venta original, artículo devuelto y nuevo entregado, más opción de falla de fabricación que genera ingreso simulado a Stock defectuoso (SD-xxx) con origen en la devolución (DEV-xxx); tablas muestran explícitamente el vínculo venta original → devolución; ruta /ventas/postventa y enlace desde el POS; fuera de alcance garantía externa y peritajes; pruebas tests/Feature/PostventaViewTest con 9 tests aprobados (suite completa: 71 tests, 260 aserciones).

### [HECHO] VIS-011 — Revisión visual integral

- **Capa:** SOLICITADO
- **Fuente:** RNF 01, RNF 02, RNF 05, RNF 06
- **Contexto propietario:** Auditoría visual y usabilidad
- **Dependencias:** VIS-001, VIS-002, VIS-003, VIS-004, VIS-005, VIS-006, VIS-007, VIS-008, VIS-009, VIS-010
- **Alcance:** Inspección completa de todas las vistas en dispositivos móviles y de escritorio; verificación de estados vacíos, mensajes de error accesibles, formularios concisos y eliminación de redundancias visuales o componentes superfluos.
- **Fuera de alcance:** Pruebas de base de datos o lógica funcional de negocio.
- **Invariantes:** Simplicidad estricta como criterio de aceptación; navegación fluida y sin sobrecarga cognitiva.
- **Contratos y archivos previstos:** Documento de revisión visual y ajustes en resources/views
- **Criterios de aceptación:** Aprobación explícita de la navegación completa con datos simulados en pantallas de teléfono y PC antes de iniciar la implementación funcional en la Fase C.
- **Responsable:** opencode-big-pickle-local
- **Fecha de inicio:** 2026-11-20
- **Última actualización:** 2026-11-20
- **Próximo paso:** Tarea completada. Iniciar FUN-001 (Autenticación y autorización) según la metodología vistas-antes-de-funcionalidad.
- **Evidencia actual:** Revisión documentada en docs/plan-maestro/01-revision-visual.md. Inspección en navegador de 11 vistas × 2 viewports (390 px celular y 1200 px escritorio) sin desbordamiento horizontal tras ajustar el menú expandido de Bootstrap en resources/views/layouts/app.blade.php (@media min-width 992px con flex-wrap y max-width 100%); 18 controles sin etiqueta programática corregidos con aria-label en productos, clientes, proveedores, pedidos de proveedores, ventas, pedidos mayoristas, informes y usuarios; estados vacíos y alertas con role="alert" verificados; navegación completa con datos ficticios aprobada en ambos dispositivos. Pruebas tests/Feature/VisualReviewTest.php con 4 tests nuevos (suite completa: 75 tests, 333 aserciones).
