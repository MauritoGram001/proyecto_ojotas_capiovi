# DEC-2026-11-19-03: Base de datos MySQL en servidor local (XAMPP)

- **Estado:** APROBADO
- **Fecha:** 2026-11-19
- **Responsable:** Usuario / Antigravity

## Contexto

El sistema funcionará 100% online con múltiples dispositivos conectados simultáneamente (RNF 07), por lo que se requiere un motor de base de datos relacional robusto con transaccionalidad ácida y soporte de bloqueos para proteger el stock y los saldos.

## Decisión

Se adopta **MySQL** ejecutado en el entorno de servidor local XAMPP (`C:\xampp\mysql`):
- Soporte para transacciones seguras con motor InnoDB.
- Bloqueos pesimistas (`lockForUpdate`) para operaciones concurrentes de ventas, ingresos de mercadería y cobros.
- Esquema relacional con claves foráneas e integridad referencial.
- Configuración en `.env`: base de datos `ojotas_capiovi`.

## Consecuencias

- Excelente rendimiento local y compatibilidad nativa con Laravel Eloquent.
- Facilidad de administración mediante phpMyAdmin / CLI de MySQL en XAMPP.
