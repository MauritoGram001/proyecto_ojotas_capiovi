# Ojotas Capioví — Sistema de Gestión Interna

## 1. Descripción del Proyecto

Sistema integral de gestión comercial e interna para **Ojotas Capioví**, comercio dedicado a la venta de textiles y calzado tanto minorista (en local) como mayorista (recorridos, reposición y pedidos).

El sistema es de uso interno exclusivo (administradores, dueño y empleados). No contempla portales externos para clientes o proveedores.

## 2. Stack Tecnológico Confirmado

- **Framework:** Laravel 12 (MVC estándar).
- **Lenguaje:** PHP 8.2.12 (CLI / XAMPP).
- **Componentes Reactivos:** Livewire 4.
- **Motor de Plantillas:** Blade.
- **Diseño e Interfaz:** Bootstrap 5 nativo (interfaz simple, rápida, responsiva y adaptable a celulares).
- **Base de Datos:** MySQL (ejecutándose en servidor local con XAMPP).
- **Comprobantes Internos:** Generación de comprobantes PDF mediante `barryvdh/laravel-dompdf`.
- **Herramientas de Planificación:** Kit-AI adaptado (Python 3.12, roadmap y visor web).

## 3. Decisiones de Negocio y Reglas Confirmadas

- **Arquitectura:** MVC convencional de Laravel (`app/Models`, `app/Http/Controllers`, `app/Livewire`, `resources/views`, etc.). Sin DDD ni arquitectura hexagonal.
- **Prioridad de Desarrollo:** Vistas y maquetación interactiva con datos ficticios antes de la implementación funcional de negocio.
- **RF 13 (Bajo stock):** Umbral fijado en menos de 10 unidades.
- **RF 19 (Cambio manual de precio):** Permitido para cualquier usuario autenticado; afecta exclusivamente a la operación en curso, sin alterar el precio del catálogo.
- **RF 22 (Devoluciones):** Se realiza cambio de producto; la mercadería devuelta por falla ingresa a estado de "Stock defectuoso" para coordinar reclamos con proveedores (RF 12).
- **RNF 07 (Operación Online y Concurrente):** 100% online con múltiples dispositivos concurrentes (celulares y PCs). Sin sincronización offline ni almacenamiento local de ventas.
- **RNF 08 (Respaldos):** Volcado diario automatizado de base de datos MySQL, retención de 30 días y copia en almacenamiento secundario/nube.

## 4. Exclusiones Expresas del Alcance

- Sin control ni seguimiento de stock por vehículo o depósitos móviles.
- Sin reserva de stock por pedidos mayoristas (el stock se descuenta únicamente con la venta confirmada).
- Sin bloqueo de ventas por deudas pendientes de clientes (se muestra advertencia pero se permite continuar).
- Sin gestión detallada ni cartera de cheques (solo se registra medio e importe).
- Sin facturación fiscal ni electrónica (los comprobantes son internos y no fiscales).
- Sin portales de acceso para clientes, proveedores o contadores.
