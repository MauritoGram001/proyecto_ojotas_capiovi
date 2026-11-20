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

### [PENDIENTE] VIS-003 — Clientes y proveedores

- **Capa:** SOLICITADO
- **Fuente:** RF 01, RF 07, RNF 05
- **Contexto propietario:** Directorio comercial
- **Dependencias:** VIS-001
- **Alcance:** Diseñar las pantallas de listado y formulario de clientes (minoristas y mayoristas) y proveedores con datos de contacto. Contemplar campo CUIT obligatorio condicional únicamente para mayoristas y simulación de selector para venta rápida sin identificar comprador.
- **Fuera de alcance:** Validación real contra padrón fiscal de AFIP o sincronizaciones externas.
- **Invariantes:** Formularios compactos; campos adicionales solo cuando correspondan al tipo de cliente; etiquetas claras.
- **Contratos y archivos previstos:** resources/views/livewire/clientes/index.blade.php, resources/views/livewire/proveedores/index.blade.php
- **Criterios de aceptación:** Formulario de cliente que active el campo CUIT al seleccionar "Mayorista" y lo oculte en "Minorista"; listado claro con búsqueda por nombre o teléfono; vista de proveedores con datos esenciales de contacto.
- **Responsable:** sin asignar
- **Fecha de inicio:** pendiente
- **Última actualización:** 2026-11-19
- **Próximo paso:** Completar VIS-001 y ejecutar claim VIS-003.
- **Evidencia actual:** Tarea pendiente de autorización.

### [PENDIENTE] VIS-004 — Ventas y comprobantes

- **Capa:** SOLICITADO
- **Fuente:** RF 03, RF 06, RF 18, RF 19, DEC-2026-11-19-05
- **Contexto propietario:** Mostrador y ventas
- **Dependencias:** VIS-001, VIS-002, VIS-003
- **Alcance:** Diseñar la pantalla de venta (POS simplificado para mostrador y celular), selector de cliente o comprador no identificado, carga de líneas de producto por variante, aplicación interactiva de precios mayorista/minorista, campo para cambio manual de precio/descuento y previsualización de comprobante interno no fiscal con botón de descarga simulada en PDF.
- **Fuera de alcance:** Envío por correo/WhatsApp o impresión térmica física.
- **Invariantes:** Comprobantes no fiscales de carácter estrictamente interno; cambio manual de precio presentado con advertencia visual de que afecta solo la operación en curso.
- **Contratos y archivos previstos:** resources/views/livewire/ventas/crear.blade.php, resources/views/pdf/comprobante-interno.blade.php
- **Criterios de aceptación:** Interfaz ágil utilizable desde teléfonos móviles; cálculo dinámico de totales con descuentos; simulación del diseño de comprobante PDF interno con modelo, talle, color y precio cobrado.
- **Responsable:** sin asignar
- **Fecha de inicio:** pendiente
- **Última actualización:** 2026-11-19
- **Próximo paso:** Completar dependencias visuales y ejecutar claim VIS-004.
- **Evidencia actual:** Tarea pendiente de autorización.

### [PENDIENTE] VIS-005 — Cuentas y pagos

- **Capa:** SOLICITADO
- **Fuente:** RF 09, RF 14, RF 16, RF 20, RF 21, DEC-2026-11-19-05
- **Contexto propietario:** Cobranzas y finanzas
- **Dependencias:** VIS-001, VIS-003, VIS-004
- **Alcance:** Diseñar vistas de cuenta corriente de clientes y deudas con proveedores; advertencia no bloqueante de boleta pendiente en ventas; formulario de cobro combinado (efectivo, transferencia y cheque básico sin cartera bancaria) e imputación a múltiples boletas.
- **Fuera de alcance:** Pasarelas de pago online, cálculo de intereses automáticos o gestión de cheques rechazados.
- **Invariantes:** La advertencia de boleta pendiente no bloquea ni pide contraseña; muestra claramente que se puede continuar.
- **Contratos y archivos previstos:** resources/views/livewire/cuentas-corrientes/index.blade.php, resources/views/livewire/cobros/formulario.blade.php
- **Criterios de aceptación:** Pantalla de saldo y movimientos por cliente; modal/alerta informativa de deuda previa sin botones de bloqueo; formulario para desglosar pago en varios medios y aplicarlo a boletas seleccionadas.
- **Responsable:** sin asignar
- **Fecha de inicio:** pendiente
- **Última actualización:** 2026-11-19
- **Próximo paso:** Completar dependencias y ejecutar claim VIS-005.
- **Evidencia actual:** Tarea pendiente de autorización.

### [PENDIENTE] VIS-006 — Pedidos a proveedores y cambios

- **Capa:** SOLICITADO
- **Fuente:** RF 11, RF 12, DEC-2026-11-19-05
- **Contexto propietario:** Compras y abastecimiento
- **Dependencias:** VIS-001, VIS-002, VIS-003
- **Alcance:** Diseñar pantallas de seguimiento de pedidos a proveedores con estados visuales (pendiente, recibido) y sección de mercadería defectuosa o enviada a cambio con indicación de su estado.
- **Fuera de alcance:** Integración electrónica B2B con proveedores.
- **Invariantes:** Claridad en el estado del pedido y coherencia con el circuito de cambio de mercadería fallada.
- **Contratos y archivos previstos:** resources/views/livewire/proveedores/pedidos.blade.php, resources/views/livewire/proveedores/cambios.blade.php
- **Criterios de aceptación:** Vistas interactivas con listado de pedidos y formulario de registro de mercadería defectuosa; estados visibles mediante insignias de texto claras.
- **Responsable:** sin asignar
- **Fecha de inicio:** pendiente
- **Última actualización:** 2026-11-19
- **Próximo paso:** Completar VIS-001 y ejecutar claim VIS-006.
- **Evidencia actual:** Tarea pendiente de autorización.

### [PENDIENTE] VIS-007 — Pedidos mayoristas

- **Capa:** SOLICITADO
- **Fuente:** RF 17
- **Contexto propietario:** Ventas mayoristas
- **Dependencias:** VIS-001, VIS-002, VIS-003
- **Alcance:** Diseñar la vista de pedidos de clientes mayoristas con estados (pendiente, preparado, entregado, cancelado) e indicador visual explícito que aclare que el pedido no reserva stock.
- **Fuera de alcance:** Control de carga por vehículo o depósitos móviles en camionetas.
- **Invariantes:** Los pedidos no descuentan stock; la pantalla debe mostrar esta regla para evitar malentendidos operativos.
- **Contratos y archivos previstos:** resources/views/livewire/pedidos-mayoristas/index.blade.php, resources/views/livewire/pedidos-mayoristas/detalle.blade.php
- **Criterios de aceptación:** Pantalla de gestión de pedidos mayoristas con cambio de estado visual; nota aclaratoria de no reserva de stock; botón para simular conversión a venta confirmada.
- **Responsable:** sin asignar
- **Fecha de inicio:** pendiente
- **Última actualización:** 2026-11-19
- **Próximo paso:** Completar dependencias y ejecutar claim VIS-007.
- **Evidencia actual:** Tarea pendiente de autorización.

### [PENDIENTE] VIS-008 — Informes e historial

- **Capa:** SOLICITADO
- **Fuente:** RF 08, RF 15
- **Contexto propietario:** Gestión e informes
- **Dependencias:** VIS-001, VIS-004, VIS-005
- **Alcance:** Diseñar pantallas de consulta de ventas por día y período, historial de compras por cliente identificado e informes diarios y mensuales simplificados para dueño y contador (carácter interno).
- **Fuera de alcance:** Dashboards saturados de gráficos o exportaciones fiscales a sistemas externos.
- **Invariantes:** Diseños limpios con tablas ordenadas, filtros por fecha y subtotales directos, sin saturación visual.
- **Contratos y archivos previstos:** resources/views/livewire/informes/ventas-periodo.blade.php, resources/views/livewire/informes/historial-cliente.blade.php
- **Criterios de aceptación:** Pantallas de reporte con filtros de fechas que presenten totales consolidados claros, sin elementos decorativos pesados.
- **Responsable:** sin asignar
- **Fecha de inicio:** pendiente
- **Última actualización:** 2026-11-19
- **Próximo paso:** Completar dependencias y ejecutar claim VIS-008.
- **Evidencia actual:** Tarea pendiente de autorización.

### [PENDIENTE] VIS-009 — Usuarios y permisos

- **Capa:** SOLICITADO
- **Fuente:** RF 10, DEC-2026-11-19-05
- **Contexto propietario:** Seguridad y administración
- **Dependencias:** VIS-001
- **Alcance:** Diseñar la vista de administración de usuarios y asignación de roles (Administrador, Dueño, Empleado de local), reflejando las diferencias visuales en la interfaz según el rol simulado.
- **Fuera de alcance:** Mecanismos complejos de 2FA o proveedores de identidad OAuth.
- **Invariantes:** Mostrar que todos los roles autenticados tienen acceso a la acción de descuento o cambio de precio en ventas.
- **Contratos y archivos previstos:** resources/views/livewire/usuarios/index.blade.php, resources/views/livewire/usuarios/formulario.blade.php
- **Criterios de aceptación:** Pantalla de usuarios clara con selección de rol; previsualización de permisos asociados a cada rol.
- **Responsable:** sin asignar
- **Fecha de inicio:** pendiente
- **Última actualización:** 2026-11-19
- **Próximo paso:** Completar VIS-001 y ejecutar claim VIS-009.
- **Evidencia actual:** Tarea pendiente de autorización.

### [PENDIENTE] VIS-010 — Anulaciones y devoluciones

- **Capa:** SOLICITADO
- **Fuente:** RF 22, DEC-2026-11-19-05
- **Contexto propietario:** Postventa y correcciones
- **Dependencias:** VIS-001, VIS-002, VIS-004
- **Alcance:** Diseñar pantallas para registrar anulación de ventas y devoluciones por cambio, con opción para indicar falla de fabricación e ingreso simulado a "Stock defectuoso".
- **Fuera de alcance:** Procesos de garantía externa o peritajes técnicos.
- **Invariantes:** Confirmación obligatoria previa a anular; visualización del vínculo entre la venta original y la devolución.
- **Contratos y archivos previstos:** resources/views/livewire/ventas/devolucion.blade.php, resources/views/livewire/ventas/anular-modal.blade.php
- **Criterios de aceptación:** Flujo interactivo de anulación con modal de confirmación justificado; formulario de cambio de producto con selección del artículo devuelto y del nuevo entregado.
- **Responsable:** sin asignar
- **Fecha de inicio:** pendiente
- **Última actualización:** 2026-11-19
- **Próximo paso:** Completar dependencias y ejecutar claim VIS-010.
- **Evidencia actual:** Tarea pendiente de autorización.

### [PENDIENTE] VIS-011 — Revisión visual integral

- **Capa:** SOLICITADO
- **Fuente:** RNF 01, RNF 02, RNF 05, RNF 06
- **Contexto propietario:** Auditoría visual y usabilidad
- **Dependencias:** VIS-001, VIS-002, VIS-003, VIS-004, VIS-005, VIS-006, VIS-007, VIS-008, VIS-009, VIS-010
- **Alcance:** Inspección completa de todas las vistas en dispositivos móviles y de escritorio; verificación de estados vacíos, mensajes de error accesibles, formularios concisos y eliminación de redundancias visuales o componentes superfluos.
- **Fuera de alcance:** Pruebas de base de datos o lógica funcional de negocio.
- **Invariantes:** Simplicidad estricta como criterio de aceptación; navegación fluida y sin sobrecarga cognitiva.
- **Contratos y archivos previstos:** Documento de revisión visual y ajustes en resources/views
- **Criterios de aceptación:** Aprobación explícita de la navegación completa con datos simulados en pantallas de teléfono y PC antes de iniciar la implementación funcional en la Fase C.
- **Responsable:** sin asignar
- **Fecha de inicio:** pendiente
- **Última actualización:** 2026-11-19
- **Próximo paso:** Finalizar todas las vistas individuales VIS-001 a VIS-010 antes de ejecutar la revisión integral.
- **Evidencia actual:** Tarea pendiente de autorización.
