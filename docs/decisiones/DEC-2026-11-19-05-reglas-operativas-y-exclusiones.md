# DEC-2026-11-19-05: Reglas operativas confirmadas y exclusiones expresas

- **Estado:** APROBADO
- **Fecha:** 2026-11-19
- **Responsable:** Usuario / Antigravity

## Contexto

El prompt maestro y las aclaraciones del usuario fijaron un conjunto de reglas operativas concretas para la primera versión del sistema, así como exclusiones de complejidad no requerida.

## Reglas Operativas Confirmadas

1. **RF 13 (Umbral de bajo stock):** Se fija una alerta visual cuando las existencias de un producto o variante sean menores a 10 unidades.
2. **RF 19 (Cambios manuales de precio y descuentos):** Cualquier usuario autenticado puede aplicar descuentos o modificar el precio unitario en una venta. Este cambio afecta **únicamente a la venta en curso**; el precio de catálogo no se altera. El comprobante y la venta conservan el precio efectivamente cobrado.
3. **RF 22 (Devoluciones y defectos):** Las devoluciones se gestionan como cambio de producto; la mercadería defectuosa devuelta ingresa al estado "Stock defectuoso" (sin reingresar al stock vendible) para su posterior reclamo o reemplazo con proveedores (RF 12).
4. **RF 09 (Boletas pendientes):** Se emite una advertencia visual si un cliente tiene deuda acumulada, pero el sistema permite expresamente continuar con la venta sin bloquear ni exigir autorizaciones adicionales.
5. **RF 10 (Roles y permisos):**
   - Administrador: gestión operativa total.
   - Dueño: caja, cuentas corrientes, informes, usuarios y permisos.
   - Empleado: ventas minoristas y consulta de disponibilidad.
6. **RF 20 (Medios de pago):** Efectivo, transferencia y cheque. Para cheques se registra únicamente el medio y el importe, sin administración de vencimientos ni cartera bancaria.
7. **RNF 08 (Respaldos):** Volcado diario automatizado de la base de datos MySQL, retención de 30 días y copia en almacenamiento secundario/nube.

## Exclusiones Expresas

- No hay control ni inventario separado por vehículos de reparto (sin depósitos móviles).
- Los pedidos de clientes mayoristas no reservan ni descuentan stock (RF 17); el stock se descuenta únicamente al confirmar la venta.
- Sin facturación fiscal ni conexión con servicios externos (AFIP); los comprobantes son estrictamente internos y no fiscales (RF 06).
- Sin funcionamiento fuera de línea (sin modo offline); el sistema requiere conexión activa (RNF 07).
