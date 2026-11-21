# Trabajo en Ojotas Capioví

Este proyecto utiliza **MVC convencional de Laravel** con PHP, Livewire y Bootstrap 5. Esta decisión prevalece sobre cualquier instrucción previa. No se utiliza arquitectura hexagonal ni DDD, no se generan bounded contexts ni estructuras en `src/`, y no se crean capas de puertos o adaptadores.

## Reglas de Arquitectura y Estructura

La aplicación se organiza bajo la estructura estándar de Laravel:
- `app/Models` — Modelos de Eloquent y relaciones directas.
- `app/Http/Controllers` — Controladores HTTP para rutas y descargas (ej. PDFs).
- `app/Http/Requests` — Form Requests para validaciones.
- `app/Livewire` — Componentes interactivos Livewire.
- `app/Policies` — Políticas de autorización según roles.
- `app/Services` — Lógica de negocio compartida (únicamente cuando exista una necesidad concreta, sin abstracciones preventivas).
- `resources/views` — Vistas Blade y componentes visuales Bootstrap 5.
- `routes/` — Rutas web estándar (`routes/web.php`).
- `database/` — Migraciones, factories y seeders.
- `tests/` — Pruebas automatizadas (Feature y Unit con Pest / PHPUnit).

## Metodología: Vistas antes de Funcionalidad

El desarrollo se realiza en el siguiente orden estricto:
1. Layout, navegación y componentes visuales compartidos.
2. Vistas y flujos de trabajo con datos ficticios.
3. Revisión visual en escritorio y celulares.
4. Implementación funcional.
5. Conexión con datos reales y pruebas.

Durante la fase visual se utilizan datos de ejemplo identificados, simulando la navegación sin implementar persistencia real ni reglas de negocio anticipadas.

## Coordinación y Reserva de Tareas

Al tomar una tarea autorizada, reservala inmediatamente antes de investigar o trabajar en ella:
```sh
python -B .agents/skills/manage-project-roadmap/scripts/roadmap.py claim ID --owner "identificador único de este chat"
```
El comando valida el plan, reserva la tarea localmente entre worktrees del mismo repositorio Git, la pasa a estado `EN_CURSO` con responsable y fechas reales, y devuelve su contexto.

Al cerrar o interrumpir el trabajo:
1. Actualizá estado, evidencia y próximo paso respetando los 14 campos.
2. Validá el plan con `python -B .agents/skills/manage-project-roadmap/scripts/roadmap.py check`.
3. Liberá la reserva con:
```sh
python -B .agents/skills/manage-project-roadmap/scripts/roadmap.py release ID --owner "el mismo identificador"
```

## Visualización del Plan Maestro

Para visualizar el árbol interactivo del plan maestro en el navegador:
```sh
python -B ver-plan.py
```
El visor selecciona automáticamente un puerto libre y se sincroniza en tiempo real. Para una vista estática, ejecutar `python -B ver-plan.py --static`.
