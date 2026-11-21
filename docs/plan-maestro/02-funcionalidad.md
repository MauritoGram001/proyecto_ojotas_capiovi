# Fase C. Implementación Funcional

<!-- plan-schema: 1 -->

Desarrollo de la lógica de negocio, persistencia en base de datos MySQL, modelos de Eloquent, migraciones, controladores, componentes Livewire funcionales y generación de comprobantes internos PDF.

## Tareas

### [HECHO] FUN-001 — Autenticación y autorización

- **Capa:** SOLICITADO
- **Fuente:** RF 10, RNF 04, DEC-2026-11-19-05
- **Contexto propietario:** Seguridad y control de acceso
- **Dependencias:** VIS-011
- **Alcance:** Implementar autenticación de usuarios mediante credenciales en servidor, asignación de roles (Administrador, Dueño, Empleado de local) mediante Laravel Policies y Middleware, garantizando que todo usuario autenticado pueda aplicar descuentos y cambios manuales de precio en ventas.
- **Fuera de alcance:** Proveedores externos OAuth o autenticación de dos factores (2FA).
- **Invariantes:** Validación de permisos en servidor (ocultar un botón no sustituye verificar permisos); autorización de cambio de precio abierta a todo usuario logueado.
- **Contratos y archivos previstos:** app/Models/User.php, app/Policies/VentaPolicy.php, database/migrations/*create_users_table.php
- **Criterios de aceptación:** Acceso restringido por usuario y contraseña; roles con permisos verificables en pruebas de servidor; autorización confirmada para cualquier usuario de aplicar descuentos y cambios de precio.
- **Responsable:** opencode-big-pickle-local
- **Fecha de inicio:** 2026-11-20
- **Última actualización:** 2026-11-20
- **Próximo paso:** Tarea completada. Continuar con FUN-002 (Clientes y proveedores).
- **Evidencia actual:** Autenticación por correo/contraseña con `Auth::attempt`, validación de credenciales, regeneración de sesión y límite de intentos en `/login`; cierre de sesión en `/logout`. `App\Models\User` incorpora los roles Dueño, Administrador y Empleado de local con helpers `tieneRol`, `esAdministrador` y `esDueno`; la columna `role` quedó en la migración de usuarios y `DatabaseSeeder` crea tres cuentas locales (contraseña `capiovi2026`). Middleware `EnsureUserHasRole` registrado como alias `role` en `bootstrap/app.php`, aplicado a rutas de caja/informes/usuarios (Administrador o Dueño) y compras/proveedores (solo Administrador); el resto de la gestión exige `auth`. `App\Policies\VentaPolicy` registrada en `AppServiceProvider`: todo usuario autenticado puede crear ventas, aplicar descuentos y cambiar precios (RF 19), mientras que anular queda para Administrador/Dueño; `App\Livewire\Ventas\Crear` verifica `Gate::authorize` en el hook `updated` para `precio_unitario` y `descuento`. La navbar reemplazó el selector de rol simulado por la sesión real con opción de cerrar sesión. Pruebas `tests/Feature/AuthenticationTest.php` con 9 tests (login, credenciales inválidas, logout, rutas protegidas, middleware de roles, política y POS por rol); suite completa: 84 tests, 395 aserciones. Verificación en navegador: login con administrador OK y empleado recibe 403 en `/usuarios` pero accede a `/ventas/crear`. Sin OAuth ni 2FA.

### [HECHO] FUN-002 — Clientes y proveedores

- **Capa:** SOLICITADO
- **Fuente:** RF 01, RF 07, DEC-2026-11-19-05
- **Contexto propietario:** Directorio comercial
- **Dependencias:** FUN-001
- **Alcance:** Implementar modelos Eloquent, migraciones y CRUD funcional para Clientes (minoristas y mayoristas) y Proveedores. Validación en servidor de CUIT obligatorio únicamente para clientes mayoristas y soporte para operaciones de venta a consumidor final sin identificar comprador.
- **Fuera de alcance:** Portales externos o autogestión para clientes o proveedores.
- **Invariantes:** CUIT obligatorio solo para mayoristas; permitir ventas minoristas sin cliente asociado; operaciones en cuenta corriente obligatoriamente vinculadas a un cliente.
- **Contratos y archivos previstos:** app/Models/Cliente.php, app/Models/Proveedor.php, database/migrations/*create_clientes_table.php
- **Criterios de aceptación:** Registros guardados en MySQL con validaciones comprobadas; CUIT validado para mayoristas y opcional para minoristas; consultas de clientes ágiles con índices en nombre y CUIT.
- **Responsable:** opencode-big-pickle-local
- **Fecha de inicio:** 2026-11-20
- **Última actualización:** 2026-11-20
- **Próximo paso:** Tarea completada. Continuar con FUN-003 (Productos, precios y stock).
- **Evidencia actual:** Modelos Eloquent `App\Models\Cliente` (tipos Minorista/Mayorista) y `App\Models\Proveedor` (tabla `proveedores`, rubros Calzado/Textil/Insumos/Logística) con campos de contacto y CUIT. Migraciones `2026_11_20_100000_create_clientes_table` (índices en `nombre` y `tipo`, `cuit` único y nullable) y `2026_11_20_100001_create_proveedores_table` (índices en `razon_social` y `rubro`, `cuit` único y nullable). `DatabaseSeeder` idempotente y `DirectorioSeeder` con datos iniciales del directorio; `tests/TestCase` siembra `DirectorioSeeder` para el entorno de pruebas. CRUD funcional en Livewire: `App\Livewire\Clientes\Index` y `App\Livewire\Proveedores\Index` con persistencia, búsqueda por nombre/razón social o teléfono, filtros, edición y baja; vistas actualizadas con acciones, mensajes de error y formularios reales. Validación en servidor: nombre/razón social requeridos, CUIT obligatorio solo para mayoristas (formato `NN-NNNNNNNN-N` y único, opcional para minoristas/proveedores), y el CUIT se limpia al pasar un cliente a minorista. Venta rápida admite consumidor final sin identificar y selección de clientes existentes. `App\Livewire\Ventas\Crear` consume clientes desde la base para el selector y modo de precio. Pruebas `tests/Feature/ClientesViewTest` y `ProveedoresViewTest` actualizadas para alta/edición/baja, persistencia, búsquedas, formato/unicidad de CUIT y regla mayorista; `VentasViewTest` y el resto de la suite compatibles. Suite completa: `89 passed (437 assertions)`. Verificación en navegador local (con usuario administrador): alta de cliente y proveedor persiste tras recargar y el nuevo cliente aparece en el POS. Sin portales ni autogestión externa.

### [HECHO] FUN-003 — Productos, precios y stock

- **Capa:** SOLICITADO
- **Fuente:** RF 02, RF 04, RF 05, RF 13, RF 18, DEC-2026-11-19-05
- **Contexto propietario:** Catálogo y existencias
- **Dependencias:** FUN-001
- **Alcance:** Implementar modelos para Productos y Variantes (talle y color), esquema de precios diferenciados (mayorista y minorista), lógica de consulta de stock y actualización atómica ante ventas o ingresos, con alertas automáticas cuando la existencia sea inferior a 10 unidades.
- **Fuera de alcance:** Control de stock por depósitos móviles en vehículos.
- **Invariantes:** Actualización atómica de stock mediante transacciones seguras para evitar movimientos duplicados o inconsistencias concurrentes.
- **Contratos y archivos previstos:** app/Models/Producto.php, app/Models/Variante.php, database/migrations/*create_productos_table.php
- **Criterios de aceptación:** Productos y variantes persistidos con integridad referencial; consultas de stock por modelo/talle/color; alerta activa comprobada cuando el stock es menor a 10; prevención de stock duplicado en transacciones concurrentes.
- **Responsable:** opencode-big-pickle-local
- **Fecha de inicio:** 2026-11-20
- **Última actualización:** 2026-11-20
- **Próximo paso:** Tarea completada. Continuar con FUN-004 (Ventas y comprobantes).
- **Evidencia actual:** Modelos Eloquent `App\Models\Producto` (precios minorista/mayorista, relación con variantes y consulta de existencias por modelo/talle/color) y `App\Models\Variante` (talle, color, stock, relación `producto_id` con FK y borrado en cascada). Métodos de actualización atómica con transacción y bloqueo (`Variante::ajustarStockAtomico`, `descontarPorVenta`, `Producto::registrarIngreso`) que impiden stock negativo e inconsistencias/duplicaciones en egresos concurrentes o parciales. Migraciones `2026_11_20_110000_create_productos_table` y `2026_11_20_110001_create_variantes_table` con índices (modelo/categoría, talle/color y compuesto producto-talle-color) e integridad referencial. Catálogo inicial persistido en `database/seeders/CatalogoSeeder` (5 productos, 13 variantes con casos de stock < 10) integrado a `DatabaseSeeder` y a `tests/TestCase`. `App\Livewire\Productos\Index` y la vista `resources/views/livewire/productos/index.blade.php` pasan de datos ficticios a datos reales (listado, búsqueda/filtros, detalle con variantes, alerta automática “Bajo stock (< 10)!” por variante/total y alta de producto con variante inicial persistida). `App\Livewire\Ventas\Crear` consume variantes reales del catálogo (con precios diferenciados por modo minorista/mayorista) y la vista del POS actualizada para mostrar existencias consultadas del catálogo. Pruebas en `tests/Feature/ProductosViewTest.php` cubren persistencia e integridad, consulta de stock por modelo/talle/color, alerta de bajo stock (< 10), alta con variante y atomicidad (ingreso/egreso, rechazo sin descuento parcial ante insuficiencia); `VentasViewTest` y suite completa compatibles: `93 passed (455 assertions)`. Migraciones aplicadas y catálogo sembrado en la BD local. Verificación en navegador con usuario administrador: `/productos` lista variantes/precios/alertas desde BD y `/ventas/crear` ofrece variantes reales del catálogo. Sin control de stock por depósitos móviles.

### [HECHO] FUN-004 — Ventas y comprobantes

- **Capa:** SOLICITADO
- **Fuente:** RF 03, RF 06, RF 19, DEC-2026-11-19-05
- **Contexto propietario:** Mostrador y facturación interna
- **Dependencias:** FUN-002, FUN-003
- **Alcance:** Implementar el proceso transaccional de registro de ventas minoristas y mayoristas, cálculo de precios según tipo de venta, registro del precio y descuento efectivamente cobrado (sin alterar el catálogo), descuento inmediato de stock y generación de comprobantes internos no fiscales descargables en formato PDF mediante DomPDF.
- **Fuera de alcance:** Facturación electrónica (AFIP) o integración de impresoras fiscales.
- **Invariantes:** Comprobantes internos no fiscales; el precio modificado manualmente se guarda en el ítem de la venta y no altera el catálogo general; consistencia transaccional de stock ante ventas simultáneas.
- **Contratos y archivos previstos:** app/Models/Venta.php, app/Models/VentaItem.php, app/Http/Controllers/ComprobanteController.php, resources/views/pdf/comprobante.blade.php
- **Criterios de aceptación:** Venta registrada con descuento inmediato de existencias; generación y descarga de comprobante PDF desde PC y celular; persistencia del precio cobrado real; soporte de venta sin cliente o con cliente identificado.
- **Responsable:** opencode-big-pickle-local
- **Fecha de inicio:** 2026-11-20
- **Última actualización:** 2026-11-20
- **Próximo paso:** Tarea completada. Continuar con FUN-005 (Cuenta corriente, boletas y cobros).
- **Evidencia actual:** Registro transaccional de ventas minorista/mayorista implementado en `app/Livewire/Ventas/Crear.php` (confirmación con `DB::transaction`, bloqueo `lockForUpdate` de variantes en orden estable, descuento inmediato de stock, validación de stock insuficiente sin registrar ni descontar, soporte de comprador identificado o “Consumidor Final”). Persistencia en `app/Models/Venta.php` y `app/Models/VentaItem.php` con precio de lista, precio unitario y descuento efectivamente cobrados por ítem (sin alterar `productos`), totales y marca de precio modificado; migraciones `2026_11_20_120000_create_ventas_table.php` y `2026_11_20_120001_create_venta_items_table.php`. Comprobante interno no fiscal descargable en PDF (DomPDF) vía `GET /ventas/{venta}/comprobante` (`app/Http/Controllers/ComprobanteController.php`, `routes/web.php`, plantilla `resources/views/pdf/comprobante.blade.php`); vista POS `resources/views/livewire/ventas/crear.blade.php` actualizada para registrar, mostrar estado y descargar comprobante. `tests/Feature/VentasViewTest.php`: 14 passed (66 assertions) cubriendo carga del POS, precios minorista/mayorista, cambio manual no bloqueante, totales, registro con/sin cliente, descuento inmediato de stock, persistencia de precio/descuento sin alterar catálogo, rechazo por stock insuficiente sin efectos y descarga de PDF. Suite completa: `php artisan test` → 98 passed (489 assertions). `roadmap.py check` válido. Cambios locales sin commit ni push.

### [EN_CURSO] FUN-005 — Cuenta corriente, boletas y cobros

- **Capa:** SOLICITADO
- **Fuente:** RF 09, RF 14, RF 20, RF 21, DEC-2026-11-19-05
- **Contexto propietario:** Cuentas corrientes y cobranzas
- **Dependencias:** FUN-004
- **Alcance:** Implementar el control de cuenta corriente por cliente, boletas pendientes de cobro, advertencia no bloqueante al vender a un cliente con deuda, registro de pagos parciales y combinados (efectivo, transferencia y cheque básico sin cartera) e imputación simultánea de un mismo cobro a varias boletas calculando saldos remanentes.
- **Fuera de alcance:** Seguimiento de vencimiento o compensación bancaria de cheques.
- **Invariantes:** La advertencia de boleta pendiente no bloquea la operación ni exige permisos adicionales; la suma de importes aplicados a boletas debe coincidir con el total cobrado.
- **Contratos y archivos previstos:** app/Models/Boleta.php, app/Models/Cobro.php, app/Models/CobroImputacion.php, database/migrations/*create_cuentas_corrientes_table.php
- **Criterios de aceptación:** Saldo de cliente actualizado en tiempo real; venta permitida tras advertencia de deuda previa; cobro multimedio registrado e imputado a múltiples boletas con saldos consistentes comprobados en pruebas.
- **Responsable:** opencode-big-pickle-local
- **Fecha de inicio:** 2026-11-20
- **Última actualización:** 2026-11-20
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
