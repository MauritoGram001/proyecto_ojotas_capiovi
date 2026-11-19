# Fase D. Validación Integral y Entrega

<!-- plan-schema: 1 -->

Pruebas integrales de circuito, concurrencia, permisos de servidor, verificación de RNF en celulares y entrega formal de la primera versión del sistema.

## Tareas

### [PENDIENTE] VAL-001 — Flujos completos

- **Capa:** SOLICITADO
- **Fuente:** RF 01 a RF 22, RNF 07
- **Contexto propietario:** Calidad y pruebas de integración
- **Dependencias:** FUN-004, FUN-005, FUN-006, FUN-007, FUN-008, FUN-009, FUN-010
- **Alcance:** Ejecución de pruebas integrales de punta a punta (Feature tests y pruebas manuales) sobre todos los circuitos de negocio: venta minorista sin cliente, venta mayorista con cuenta corriente, pedidos a proveedores con recepción, cambios por defectos, cobranzas múltiples y devoluciones por cambio.
- **Fuera de alcance:** Pruebas de estrés masivo fuera del ámbito operativo de la empresa.
- **Invariantes:** Los flujos deben ejecutarse sin excepciones no controladas y manteniendo la coherencia de datos entre módulos.
- **Contratos y archivos previstos:** tests/Feature/FlujoCompletoTest.php, docs/evidencia/VAL-001.md
- **Criterios de aceptación:** Todos los circuitos de negocio ejecutados con éxito; comprobantes PDF emitidos correctamente; saldos de cuenta corriente y existencias finales cuadradas exactamente.
- **Responsable:** sin asignar
- **Fecha de inicio:** pendiente
- **Última actualización:** 2026-11-19
- **Próximo paso:** Completar la fase funcional y ejecutar claim VAL-001.
- **Evidencia actual:** Tarea pendiente de ejecución.

### [PENDIENTE] VAL-002 — Simultaneidad e integridad

- **Capa:** SOLICITADO
- **Fuente:** RNF 07, DEC-2026-11-19-03, DEC-2026-11-19-05
- **Contexto propietario:** Concurrencia e integridad transaccional
- **Dependencias:** VAL-001
- **Alcance:** Probar operaciones simultáneas desde múltiples clientes (celulares en simultáneo con terminal de escritorio), simulando ventas concurrentes sobre la misma variante con stock límite, cobranzas paralelas sobre las mismas boletas y reintentos ante desconexión momentánea de internet.
- **Fuera de alcance:** Pruebas de tolerancia a fallos de hardware en el servidor.
- **Invariantes:** Bloqueos pesimistas activos; ningún movimiento de stock o cobro puede registrarse duplicado ante reintentos o transacciones concurrentes.
- **Contratos y archivos previstos:** tests/Feature/ConcurrenciaIntegridadTest.php
- **Criterios de aceptación:** Comprobación de que no se genera stock negativo involuntario ante compras concurrentes del último par; confirmación de que un cobro simultáneo sobre una boleta no duplica el saldo cancelado.
- **Responsable:** sin asignar
- **Fecha de inicio:** pendiente
- **Última actualización:** 2026-11-19
- **Próximo paso:** Completar VAL-001 y ejecutar claim VAL-002.
- **Evidencia actual:** Tarea pendiente de ejecución.

### [PENDIENTE] VAL-003 — Permisos y validaciones

- **Capa:** SOLICITADO
- **Fuente:** RF 10, RNF 04, DEC-2026-11-19-05
- **Contexto propietario:** Auditoría de seguridad
- **Dependencias:** FUN-001, VAL-001
- **Alcance:** Verificar que las restricciones por rol se apliquen estrictamente en el backend (rutas, endpoints y componentes Livewire) y comprobar que cualquier usuario autenticado pueda realizar descuentos y cambios manuales de precio según lo expresamente aprobado.
- **Fuera de alcance:** Auditorías de penetración de red externa.
- **Invariantes:** La seguridad reside en las validaciones de servidor y Policies de Laravel, no en ocultar elementos en la vista.
- **Contratos y archivos previstos:** tests/Feature/SeguridadRolesTest.php
- **Criterios de aceptación:** Empleados bloqueados en servidor al intentar acceder a informes o administración de usuarios (HTTP 403); empleados, administradores y dueños habilitados para aplicar descuentos en ventas.
- **Responsable:** sin asignar
- **Fecha de inicio:** pendiente
- **Última actualización:** 2026-11-19
- **Próximo paso:** Completar dependencias y ejecutar claim VAL-003.
- **Evidencia actual:** Tarea pendiente de ejecución.

### [PENDIENTE] VAL-004 — Usabilidad, celulares y rendimiento

- **Capa:** SOLICITADO
- **Fuente:** RNF 01, RNF 02, RNF 03, RNF 05, RNF 06
- **Contexto propietario:** Experiencia de usuario y desempeño
- **Dependencias:** VAL-001
- **Alcance:** Validar la operatividad en teléfonos móviles reales (recorridos mayoristas), facilidad de carga táctil, tiempos de respuesta de consultas comunes (menores a pocos segundos) y ausencia de sobrecarga de datos en pantallas.
- **Fuera de alcance:** Optimización extrema para miles de peticiones por segundo.
- **Invariantes:** Interfaz clara, comprensible por usuarios sin conocimientos técnicos y sin elementos decorativos pesados.
- **Contratos y archivos previstos:** docs/evidencia/VAL-004-usabilidad.md
- **Criterios de aceptación:** Todas las pantallas operables desde celulares con fluidez; formularios concisos; consultas estándar resueltas en menos de 2 segundos.
- **Responsable:** sin asignar
- **Fecha de inicio:** pendiente
- **Última actualización:** 2026-11-19
- **Próximo paso:** Completar dependencias y ejecutar claim VAL-004.
- **Evidencia actual:** Tarea pendiente de ejecución.

### [PENDIENTE] VAL-005 — Entrega de la primera versión

- **Capa:** SOLICITADO
- **Fuente:** Solicitud inicial (2026-11-19), FTE-01, FTE-02
- **Contexto propietario:** Cierre y entrega
- **Dependencias:** VAL-001, VAL-002, VAL-003, VAL-004, FUN-011
- **Alcance:** Consolidar la entrega de la primera versión completa del sistema de gestión de Ojotas Capioví, comprobando cobertura total de RF 01 a RF 22 y RNF 01 a RNF 08, documentación técnica, manual breve de operación y verificación final del plan maestro.
- **Fuera de alcance:** Nuevas propuestas de funcionalidades no contempladas en el alcance aprobado.
- **Invariantes:** Todos los requisitos de la especificación consolidada deben estar cubiertos y verificados con evidencia duradera.
- **Contratos y archivos previstos:** docs/entrega-v1.md, plan maestro en estado final
- **Criterios de aceptación:** 100% de requisitos confirmados implementados y probados; plan maestro validado sin errores; procedimiento de respaldo y restauración comprobado.
- **Responsable:** sin asignar
- **Fecha de inicio:** pendiente
- **Última actualización:** 2026-11-19
- **Próximo paso:** Concluir todas las tareas previas para el cierre de la versión 1.
- **Evidencia actual:** Tarea pendiente de ejecución.
