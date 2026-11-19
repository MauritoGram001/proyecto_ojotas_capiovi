# DEC-2026-11-19-02: Interfaz visual simple basada en Bootstrap 5 nativo

- **Estado:** APROBADO
- **Fecha:** 2026-11-19
- **Responsable:** Usuario / Antigravity

## Contexto

El sistema será utilizado por personal sin conocimientos técnicos (empleados de local, repartidores en recorridos mayoristas y dueños). La simplicidad es un criterio de aceptación crítico (RNF 01, RNF 02, RNF 05, RNF 06). Se evaluó el uso de `jeroennoten/laravel-adminlte` versus Bootstrap 5 nativo limpio.

## Decisión

Se adopta **Bootstrap 5 nativo** directamente:
- Sin plantillas administrativas pesadas como AdminLTE.
- Diseño minimalista, centrado en formularios breves, tablas con pocas columnas y acciones claras.
- Optimización completa para navegación y operaciones táctiles desde teléfonos celulares (RNF 06).
- Cero paneles con gráficos superfluos o widgets decorativos que sobrecarguen la pantalla.

## Consecuencias

- Menor peso de assets, carga inmediata y experiencia de usuario fluida y libre de distracciones.
- Maquetación directa y limpia compatible con Livewire 4.
