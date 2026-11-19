# Plan Maestro — Ojotas Capioví

<!-- plan-schema: 1 -->

Plan maestro de desarrollo para el sistema de gestión interna de Ojotas Capioví, iniciado el 2026-11-19 bajo arquitectura **MVC de Laravel**, Livewire y Bootstrap 5 nativo.

## Estructura de Fases y Tareas

El desarrollo sigue la metodología de **vistas antes de funcionalidad** y se descompone en cuatro fases trazables:

| Módulo / Fase | Archivo | Tareas | Descripción |
| :--- | :--- | :--- | :--- |
| **Fase A. Preparación** | [00-preparacion.md](00-preparacion.md) | `INI-001` a `INI-003` | Entorno, Laravel, Livewire, adaptación MVC del kit y registro de decisiones. |
| **Fase B. Vistas** | [01-vistas.md](01-vistas.md) | `VIS-001` a `VIS-011` | Layout, navegación, pantallas con datos simulados y revisión visual en celulares. |
| **Fase C. Funcionalidad** | [02-funcionalidad.md](02-funcionalidad.md) | `FUN-001` a `FUN-011` | Implementación de modelos, controladores, transacciones y lógica de negocio. |
| **Fase D. Validación** | [03-validacion.md](03-validacion.md) | `VAL-001` a `VAL-005` | Pruebas de integración, concurrencia, permisos, usabilidad y entrega final. |

## Reglas del Plan y Trazabilidad

- Cada tarea cumple rigurosamente con los 14 campos del esquema estándar versión 1.
- Trazabilidad directa: `Requisito (RF/RNF) → Tarea (INI/VIS/FUN/VAL) → Prueba → Evidencia`.
- La finalización de una vista (`VIS-xxx`) **no** autoriza a marcar como finalizado el requisito funcional de negocio (`RF-xxx`).

## Comandos Útiles

```sh
# Validar el plan
python -B .agents/skills/manage-project-roadmap/scripts/roadmap.py check

# Ver contexto de una tarea
python -B .agents/skills/manage-project-roadmap/scripts/roadmap.py task INI-001 --format context

# Reporte resumido de tareas
python -B .agents/skills/manage-project-roadmap/scripts/roadmap.py report --summary

# Abrir el visor web interactivo
python -B ver-plan.py
```
