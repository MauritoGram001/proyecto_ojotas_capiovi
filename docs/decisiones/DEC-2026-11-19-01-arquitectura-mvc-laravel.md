# DEC-2026-11-19-01: Adopción de Arquitectura MVC Convencional de Laravel

- **Estado:** APROBADO
- **Fecha:** 2026-11-19
- **Responsable:** Usuario / Antigravity

## Contexto

El sistema de gestión interna de Ojotas Capioví requiere una arquitectura ágil, mantenible y alineada con los estándares de la comunidad de Laravel y Livewire.

## Decisión

Se adopta el patrón **Modelo-Vista-Controlador (MVC)** convencional de Laravel:
- Organización estándar bajo `app/Models`, `app/Http/Controllers`, `app/Livewire`, `resources/views`, etc.
- Se rechaza explícitamente el uso de Arquitectura Hexagonal y Domain-Driven Design (DDD).
- No se estructurará el código por bounded contexts (`src/<Contexto>/...`) ni se crearán capas de abstracción innecesarias (puertos, adaptadores, agregados, interfaces redundantes).
- La lógica compartida se extraerá a `app/Services` únicamente cuando exista una necesidad concreta y comprobada.

## Consecuencias

- Desarrollo más directo, menor boilerplate y máxima compatibilidad con las herramientas del ecosistema Laravel (Eloquent, Livewire, Artisan).
- Las plantillas y skills del Kit-AI se adaptan para eliminar referencias a arquitectura hexagonal.
