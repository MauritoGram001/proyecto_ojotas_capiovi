# Arquitectura del Sistema — Ojotas Capioví

## 1. Patrón Arquitectónico Confirmado: MVC de Laravel

El sistema se construye siguiendo estrictamente el patrón **Modelo-Vista-Controlador (MVC)** convencional de Laravel.

Esta decisión prevalece sobre cualquier plantilla, perfil o skill previa del kit. Se rechaza explícitamente el uso de arquitectura hexagonal, DDD, puertos, adaptadores o partición de código en `src/<Contexto>/...`.

## 2. Estructura de Directorios

```text
app/
├── Http/
│   ├── Controllers/       # Controladores para descargas PDF y endpoints específicos
│   ├── Requests/          # Validaciones de entrada (Form Requests)
│   └── Middleware/        # Control de autenticación y roles
├── Livewire/              # Componentes interactivos Livewire (gestión de vistas y eventos)
├── Models/                # Modelos de Eloquent (Entidades de negocio y relaciones)
├── Policies/              # Políticas de autorización según roles (Administrador, Dueño, Empleado)
└── Services/              # Servicios de lógica compartida (creados solo ante necesidad real)
resources/
└── views/
    ├── layouts/           # Plantillas base (HTML5, Bootstrap 5, navegación común)
    ├── livewire/          # Vistas asociadas a componentes Livewire
    └── pdf/               # Plantillas Blade para generación de comprobantes PDF
routes/
└── web.php                # Rutas web de la aplicación
database/
├── migrations/            # Esquema de tablas y claves foráneas en MySQL
├── factories/             # Generación de datos de prueba
└── seeders/               # Población inicial de roles, usuarios base y categorías
tests/
├── Feature/               # Pruebas de integración de flujos completos
└── Unit/                  # Pruebas unitarias de modelos y reglas
```

## 3. Rol de Livewire y Controladores

- **Componentes Livewire:** Encapsulan el estado visual, la reactividad de la interfaz y la interacción directa con los modelos Eloquent. Permiten una experiencia ágil y sin recargas de página completas. No se crea un controlador HTTP redundante por cada pantalla que resuelva Livewire.
- **Controladores HTTP:** Se reservan para acciones puntuales que requieren una respuesta HTTP clásica, como la descarga/stream de comprobantes internos en PDF (`barryvdh/laravel-dompdf`) o integraciones estándar.
- **Modelos Eloquent:** Representan las tablas del negocio (`Cliente`, `Producto`, `Variante`, `Venta`, `VentaItem`, `PedidoMayorista`, `Proveedor`, `Compra`, `Defecto`, `Comprobante`, etc.), encapsulando relaciones y scopes de consulta.

## 4. Concurrencia y Consistencia de Datos

- **RNF 07 (Operación Concurrente):** El sistema corre 100% online con múltiples usuarios (celulares en recorridos y PCs en mostrador).
- **Control de Stock y Cobros:** Las operaciones críticas que afectan existencias (`RF 05`, `RF 11`, `RF 22`) o saldos de cuenta corriente (`RF 14`, `RF 20`, `RF 21`) se ejecutarán dentro de transacciones de base de datos (`DB::transaction`) utilizando bloqueos pesimistas (`lockForUpdate`) para evitar condiciones de carrera y dobles imputaciones.
