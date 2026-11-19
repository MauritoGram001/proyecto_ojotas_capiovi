# DEC-2026-11-19-04: Prioridad de desarrollo: Vistas antes de implementación funcional

- **Estado:** APROBADO
- **Fecha:** 2026-11-19
- **Responsable:** Usuario / Antigravity

## Contexto

Para garantizar que la experiencia de usuario y los flujos visuales cumplan los criterios de simplicidad antes de invertir esfuerzo en reglas de negocio y migraciones complejas, se establece una metodología estricta de entrega por etapas.

## Decisión

El desarrollo autorizado se estructurará rigurosamente en dos etapas:
1. **Fase Visual (Fase B):** Construcción de layouts, navegación y todas las pantallas (VIS-001 a VIS-011) con datos ficticios claramente identificados, simulando flujos y verificando la usabilidad en computadoras y celulares. No se implementa persistencia ni reglas de negocio en esta fase.
2. **Fase Funcional (Fase C):** Implementación de modelos reales, lógica de negocio, validaciones, migraciones, transacciones y pruebas automatizadas (FUN-001 a FUN-011).

## Consecuencias

- Las tareas de vistas y de funcionalidad están formalmente separadas en el Plan Maestro.
- Una vista completada no equivale a un requisito funcional terminado.
