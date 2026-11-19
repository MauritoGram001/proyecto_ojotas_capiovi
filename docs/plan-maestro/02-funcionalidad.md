# Fase C. Implementación Funcional

<!-- plan-schema: 1 -->

Desarrollo de la lógica de negocio, persistencia en base de datos MySQL, modelos de Eloquent, migraciones, controladores, componentes Livewire funcionales y generación de comprobantes internos PDF.

## Tareas

### [PENDIENTE] FUN-001 — Autenticación y autorización

- **Capa:** SOLICITADO
- **Fuente:** RF 10, RNF 04, DEC-2026-11-19-05
- **Contexto propietario:** Seguridad y control de acceso
- **Dependencias:** VIS-011
- **Alcance:** Implementar autenticación de usuarios mediante credenciales en servidor, asignación de roles (Administrador, Dueño, Empleado de local) mediante Laravel Policies y Middleware, garantizando que todo usuario autenticado pueda aplicar descuentos y cambios manuales de precio en ventas.
- **Fuera de alcance:** Proveedores externos OAuth o autenticación de dos factores (2FA).
- **Invariantes:** Validación de permisos en servidor (ocultar un botón no sustituye verificar permisos); autorización de cambio de precio abierta a todo usuario logueado.
- **Contratos y archivos previstos:** app/Models/User.php, app/Policies/VentaPolicy.php, database/migrations/*create_users_table.php
- **Criterios de aceptación:** Acceso restringido por usuario y contraseña; roles con permisos verificables en pruebas de servidor; autorización confirmada para cualquier usuario de aplicar descuentos y cambios de precio.
- **Responsable:** sin asignar
- **Fecha de inicio:** pendiente
- **Última actualización:** 2026-11-19
- **Próximo paso:** Reclamar con roadmap.py claim FUN-001 tras la aprobación de la revisión visual.
- **Evidencia actual:** Tarea pendiente de ejecución.

### [PENDIENTE] FUN-002 — Clientes y proveedores

- **Capa:** SOLICITADO
- **Fuente:** RF 01, RF 07, DEC-2026-11-19-05
- **Contexto propietario:** Directorio comercial
- **Dependencias:** FUN-001
- **Alcance:** Implementar modelos Eloquent, migraciones y CRUD funcional para Clientes (minoristas y mayoristas) y Proveedores. Validación en servidor de CUIT obligatorio únicamente para clientes mayoristas y soporte para operaciones de venta a consumidor final sin identificar comprador.
- **Fuera de alcance:** Portales externos o autogestión para clientes o proveedores.
- **Invariantes:** CUIT obligatorio solo para mayoristas; permitir ventas minoristas sin cliente asociado; operaciones en cuenta corriente obligatoriamente vinculadas a un cliente.
- **Contratos y archivos previstos:** app/Models/Cliente.php, app/Models/Proveedor.php, database/migrations/*create_clientes_table.php
- **Criterios de aceptación:** Registros guardados en MySQL con validaciones comprobadas; CUIT validado para mayoristas y opcional para minoristas; consultas de clientes ágiles con índices en nombre y CUIT.
- **Responsable:** sin asignar
- **Fecha de inicio:** pendiente
- **Última actualización:** 2026-11-19
- **Próximo paso:** Completar FUN-001 y ejecutar claim FUN-002.
- **Evidencia actual:** Tarea pendiente de ejecución.

### [PENDIENTE] FUN-003 — Productos, precios y stock

- **Capa:** SOLICITADO
- **Fuente:** RF 02, RF 04, RF 05, RF 13, RF 18, DEC-2026-11-19-05
- **Contexto propietario:** Catálogo y existencias
- **Dependencias:** FUN-001
- **Alcance:** Implementar modelos para Productos y Variantes (talle y color), esquema de precios diferenciados (mayorista y minorista), lógica de consulta de stock y actualización atómica ante ventas o ingresos, con alertas automáticas cuando la existencia sea inferior a 10 unidades.
- **Fuera de alcance:** Control de stock por depósitos móviles en vehículos.
- **Invariantes:** Actualización atómica de stock mediante transacciones seguras para evitar movimientos duplicados o inconsistencias concurrentes.
- **Contratos y archivos previstos:** app/Models/Producto.php, app/Models/Variante.php, database/migrations/*create_productos_table.php
- **Criterios de aceptación:** Productos y variantes persistidos con integridad referencial; consultas de stock por modelo/talle/color; alerta activa comprobada cuando el stock es menor a 10; prevención de stock duplicado en transacciones concurrentes.
- **Responsable:** sin asignar
- **Fecha de inicio:** pendiente
- **Última actualización:** 2026-11-19
- **Próximo paso:** Completar FUN-001 y ejecutar claim FUN-003.
- **Evidencia actual:** Tarea pendiente de ejecución.

### [PENDIENTE] FUN-004 — Ventas y comprobantes

- **Capa:** SOLICITADO
- **Fuente:** RF 03, RF 06, RF 19, DEC-2026-11-19-05
- **Contexto propietario:** Mostrador y facturación interna
- **Dependencias:** FUN-002, FUN-003
- **Alcance:** Implementar el proceso transaccional de registro de ventas minoristas y mayoristas, cálculo de precios según tipo de venta, registro del precio y descuento efectivamente cobrado (sin alterar el catálogo), descuento inmediato de stock y generación de comprobantes internos no fiscales descargables en formato PDF mediante DomPDF.
- **Fuera de alcance:** Facturación electrónica (AFIP) o integración de impresoras fiscales.
- **Invariantes:** Comprobantes internos no fiscales; el precio modificado manualmente se guarda en el ítem de la venta y no altera el catálogo general; consistencia transaccional de stock ante ventas simultáneas.
- **Contratos y archivos previstos:** app/Models/Venta.php, app/Models/VentaItem.php, app/Http/Controllers/ComprobanteController.php, resources/views/pdf/comprobante.blade.php
- **Criterios de aceptación:** Venta registrada con descuento inmediato de existencias; generación y descarga de comprobante PDF desde PC y celular; persistencia del precio cobrado real; soporte de venta sin cliente o con cliente identificado.
- **Responsable:** sin asignar
- **Fecha de inicio:** pendiente
- **Última actualización:** 2026-11-19
- **Próximo paso:** Completar dependencias y ejecutar claim FUN-004.
- **Evidencia actual:** Tarea pendiente de ejecución.

### [PENDIENTE] FUN-005 — Cuenta corriente, boletas y cobros

- **Capa:** SOLICITADO
- **Fuente:** RF 09, RF 14, RF 20, RF 21, DEC-2026-11-19-05
- **Contexto propietario:** Cuentas corrientes y cobranzas
- **Dependencias:** FUN-004
- **Alcance:** Implementar el control de cuenta corriente por cliente, boletas pendientes de cobro, advertencia no bloqueante al vender a un cliente con deuda, registro de pagos parciales y combinados (efectivo, transferencia y cheque básico sin cartera) e imputación simultánea de un mismo cobro a varias boletas calculando saldos remanentes.
- **Fuera de alcance:** Seguimiento de vencimiento o compensación bancaria de cheques.
- **Invariantes:** La advertencia de boleta pendiente no bloquea la operación ni exige permisos adicionales; la suma de importes aplicados a boletas debe coincidir con el total cobrado.
- **Contratos y archivos previstos:** app/Models/Boleta.php, app/Models/Cobro.php, app/Models/CobroImputacion.php, database/migrations/*create_cuentas_corrientes_table.php
- **Criterios de aceptación:** Saldo de cliente actualizado en tiempo real; venta permitida tras advertencia de deuda previa; cobro multimedio registrado e imputado a múltiples boletas con saldos consistentes comprobados en pruebas.
- **Responsable:** sin asignar
- **Fecha de inicio:** pendiente
- **Última actualización:** 2026-11-19
- **Próximo paso:** Completar dependencias y ejecutar claim FUN-005.
- **Evidencia actual:** Tarea pendiente de ejecución.

### [PENDIENTE] FUN-006 — Compras y deudas a proveedores

- **Capa:** SOLICITADO
- **Fuente:** RF 11, RF 16, DEC-2026-11-19-05
- **Contexto propietario:** Abastecimiento y pagos
- **Dependencias:** FUN-002, FUN-003
- **Alcance:** Implementar registro de pedidos a proveedores, seguimiento de entrega, ingreso de mercadería con incremento automático del stock y control de boletas pendientes de pago a proveedores con registro de pagos efectuados.
- **Fuera de alcance:** Enlaces EDI automáticos con proveedores.
- **Invariantes:** El ingreso de mercadería debe ser atómico y no duplicar existencias ante reintentos; el estado de deuda con el proveedor se actualiza congruentemente con cada boleta y pago.
- **Contratos y archivos previstos:** app/Models/Compra.php, app/Models/CompraItem.php, app/Models/PagoProveedor.php, database/migrations/*create_compras_table.php
- **Criterios de aceptación:** Recepción de compras que incrementa stock de variantes correspondientes; registro de deuda con proveedor y pagos aplicados con saldos exactos comprobados.
- **Responsable:** sin asignar
- **Fecha de inicio:** pendiente
- **Última actualización:** 2026-11-19
- **Próximo paso:** Completar dependencias y ejecutar claim FUN-006.
- **Evidencia actual:** Tarea pendiente de ejecución.

### [PENDIENTE] FUN-007 — Mercadería defectuosa y cambios

- **Capa:** SOLICITADO
- **Fuente:** RF 12, DEC-2026-11-19-05
- **Contexto propietario:** Control de calidad y garantías
- **Dependencias:** FUN-003, FUN-006
- **Alcance:** Implementar circuito de mercadería defectuosa: registro de productos fallados, aislamiento en estado "Stock defectuoso" (sin computar en stock vendible), seguimiento del envío a cambio al proveedor y actualización de stock regular al recibir el reemplazo o crédito correspondiente.
- **Fuera de alcance:** Reclamaciones jurídicas o peritajes técnicos formales.
- **Invariantes:** La mercadería defectuosa no puede estar disponible para venta regular; trazabilidad completa entre la unidad fallada y la resolución del proveedor.
- **Contratos y archivos previstos:** app/Models/DefectoProveedor.php, database/migrations/*create_defectos_table.php
- **Criterios de aceptación:** Stock defectuoso contabilizado separadamente del disponible; trazabilidad de cambios con proveedores verificada mediante pruebas funcionales.
- **Responsable:** sin asignar
- **Fecha de inicio:** pendiente
- **Última actualización:** 2026-11-19
- **Próximo paso:** Completar dependencias y ejecutar claim FUN-007.
- **Evidencia actual:** Tarea pendiente de ejecución.

### [PENDIENTE] FUN-008 — Pedidos mayoristas

- **Capa:** SOLICITADO
- **Fuente:** RF 17, DEC-2026-11-19-05
- **Contexto propietario:** Ventas mayoristas y logística
- **Dependencias:** FUN-004
- **Alcance:** Implementar el ciclo de pedidos de clientes mayoristas con transiciones de estado (pendiente → preparado → entregado / cancelado) y su vinculación con la venta final.
- **Fuera de alcance:** Monitoreo GPS de entregas o cálculo de stock por camión.
- **Invariantes:** Los pedidos no descuentan ni reservan stock en ninguna circunstancia; el movimiento de stock se efectúa exclusivamente al confirmar la venta asociada.
- **Contratos y archivos previstos:** app/Models/PedidoMayorista.php, database/migrations/*create_pedidos_mayoristas_table.php
- **Criterios de aceptación:** Pedidos creados y transitados entre estados sin impacto en existencias; conversión de pedido entregado a venta confirmada con descuento único de stock.
- **Responsable:** sin asignar
- **Fecha de inicio:** pendiente
- **Última actualización:** 2026-11-19
- **Próximo paso:** Completar dependencias y ejecutar claim FUN-008.
- **Evidencia actual:** Tarea pendiente de ejecución.

### [PENDIENTE] FUN-009 — Anulaciones y devoluciones

- **Capa:** SOLICITADO
- **Fuente:** RF 22, DEC-2026-11-19-05
- **Contexto propietario:** Correcciones y postventa
- **Dependencias:** FUN-003, FUN-004, FUN-005
- **Alcance:** Implementar lógica para anular ventas y registrar devoluciones de clientes con cambio de producto: restitución de stock al inventario vendible (para mercadería en buen estado) o derivación a "Stock defectuoso" (para mercadería fallada), junto con el reajuste automático de boletas y cuenta corriente cuando corresponda.
- **Fuera de alcance:** Devolución directa de dinero en efectivo para operaciones de cuenta corriente no saldadas.
- **Invariantes:** Conservar el registro original de la venta vinculándolo con la corrección; evitar que la repetición de una acción duplique la restitución de existencias o el crédito en saldo.
- **Contratos y archivos previstos:** app/Models/Devolucion.php, app/Services/DevolucionService.php, database/migrations/*create_devoluciones_table.php
- **Criterios de aceptación:** Anulación comprobada con reversión de stock y saldo; devolución por cambio probada con ingreso del producto devuelto a stock defectuoso y descuento del nuevo producto entregado sin duplicar movimientos.
- **Responsable:** sin asignar
- **Fecha de inicio:** pendiente
- **Última actualización:** 2026-11-19
- **Próximo paso:** Completar dependencias y ejecutar claim FUN-009.
- **Evidencia actual:** Tarea pendiente de ejecución.

### [PENDIENTE] FUN-010 — Informes e historial

- **Capa:** SOLICITADO
- **Fuente:** RF 08, RF 15
- **Contexto propietario:** Informes gerenciales y contables
- **Dependencias:** FUN-004, FUN-005, FUN-006
- **Alcance:** Implementar consultas optimizadas de ventas por día, período y cliente; historial de transacciones de cada cliente; reportes consolidados para el dueño (caja, cobros, pendientes) y resúmenes para el contador, manteniendo carácter de control interno.
- **Fuera de alcance:** Libros fiscales de IVA compras/ventas digitales (RG AFIP).
- **Invariantes:** Informes precisos basados en transacciones cerradas, generados de manera ágil sin bloquear la base de datos operativa.
- **Contratos y archivos previstos:** app/Services/InformeService.php, app/Livewire/Informes/ResumenPeriodo.php
- **Criterios de aceptación:** Consultas que resuelvan en pocos segundos con datos agregados correctos; reportes descargables o visualizables con filtros por fecha y cliente.
- **Responsable:** sin asignar
- **Fecha de inicio:** pendiente
- **Última actualización:** 2026-11-19
- **Próximo paso:** Completar dependencias y ejecutar claim FUN-010.
- **Evidencia actual:** Tarea pendiente de ejecución.

### [PENDIENTE] FUN-011 — Respaldos y restauración

- **Capa:** SOLICITADO
- **Fuente:** RNF 08, DEC-2026-11-19-05
- **Contexto propietario:** Operación y contingencia
- **Dependencias:** FUN-001
- **Alcance:** Implementar comando de respaldo automatizado de la base de datos MySQL (mysqldump programado en Laravel Scheduler) con retención de 30 días, compresión de volcados, almacenamiento secundario y procedimiento documentado de restauración comprobado.
- **Fuera de alcance:** Replicación en tiempo real multi-datacenter.
- **Invariantes:** Los respaldos deben ser consistentes y el procedimiento de recuperación debe haber sido probado efectivamente sobre una base de prueba.
- **Contratos y archivos previstos:** app/Console/Commands/DatabaseBackupCommand.php, docs/operacion/respaldos.md
- **Criterios de aceptación:** Comando artisan operativo para generar respaldo; purga automática de copias de más de 30 días; prueba de restauración ejecutada con éxito.
- **Responsable:** sin asignar
- **Fecha de inicio:** pendiente
- **Última actualización:** 2026-11-19
- **Próximo paso:** Completar FUN-001 y ejecutar claim FUN-011.
- **Evidencia actual:** Tarea pendiente de ejecución.
