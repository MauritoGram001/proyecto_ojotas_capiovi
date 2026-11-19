# Fase A. Preparación del Proyecto

<!-- plan-schema: 1 -->

Tareas de inicialización, relevamiento del entorno, adaptación del kit a MVC de Laravel y registro formal de decisiones de arquitectura y negocio.

## Tareas

### [HECHO] INI-001 — Entorno, kit y fuentes

- **Capa:** SOLICITADO
- **Fuente:** Solicitud inicial del usuario (2026-11-19), FTE-01, FTE-02
- **Contexto propietario:** Plataforma y entorno de desarrollo
- **Dependencias:** ninguna
- **Alcance:** Obtener el repositorio Kit-AI, registrar su commit y fecha de obtención, inspeccionar herramientas del sistema (PHP, Composer, MySQL, Python, Git) e incorporar el prompt inicial como fuente principal y consolidada de requisitos.
- **Fuera de alcance:** Modificar el repositorio remoto original de Kit-AI o depender de archivos de Google Drive o de otra PC.
- **Invariantes:** No asumir instalaciones no comprobadas; registrar fuentes durables en el inventario; distinguir hechos y requisitos de propuestas no autorizadas.
- **Contratos y archivos previstos:** docs/fuentes/README.md, .agents/project-kit.json, .agents/project.json
- **Criterios de aceptación:** Kit-AI descargado con commit cd0fe95e6c5a6b592b7176d7f9c7cf1e492917b2 registrado; herramientas PHP 8.2.12, Composer 2.10.3, MySQL, Python 3.12 y Git 2.47 operativas; inventario de fuentes documentado en docs/fuentes/README.md sin dependencias externas bloqueantes.
- **Responsable:** Mauricio / Antigravity
- **Fecha de inicio:** 2026-11-19
- **Última actualización:** 2026-11-19
- **Próximo paso:** Tarea concluida. Continuar con la base técnica en INI-002.
- **Evidencia actual:** Repositorio Kit-AI obtenido en commit cd0fe95e6c5a6b592b7176d7f9c7cf1e492917b2 (2026-11-14); Python 3.12 y Git Portable 2.47 configurados en PATH; inventario de fuentes consolidado en docs/fuentes/README.md.

### [HECHO] INI-002 — Arquitectura y base técnica

- **Capa:** SOLICITADO
- **Fuente:** DEC-2026-11-19-01, DEC-2026-11-19-02, FTE-01
- **Contexto propietario:** Infraestructura y base Laravel MVC
- **Dependencias:** INI-001
- **Alcance:** Preparar el proyecto Laravel en C:\xampp\htdocs\ojotas_capiovi, instalar Livewire y DomPDF, configurar entorno .env con base MySQL local, adaptar las skills locales de Kit-AI suprimiendo arquitectura hexagonal y DDD, e incorporar las herramientas de roadmap y el visor ver-plan.py.
- **Fuera de alcance:** Implementación de modelos de negocio o controladores de dominio anticipados.
- **Invariantes:** Estricto respeto del patrón MVC convencional de Laravel; no crear src/<Contexto>, puertos, adaptadores ni bounded contexts; conservar únicamente las skills plan-maestro-desarrollo-sistema y manage-project-roadmap adaptadas.
- **Contratos y archivos previstos:** composer.json, .env, AGENTS.md, ver-plan.py, docs/arquitectura/README.md
- **Criterios de aceptación:** Laravel 12 y Livewire 4 instalados en composer.json; .env configurado con MySQL local; AGENTS.md y skills locales adaptadas a MVC sin directivas hexagonales; roadmap.py y ver-plan.py operativos y validados.
- **Responsable:** Mauricio / Antigravity
- **Fecha de inicio:** 2026-11-19
- **Última actualización:** 2026-11-19
- **Próximo paso:** Tarea concluida. Avanzar con el registro de decisiones en INI-003.
- **Evidencia actual:** Laravel 12.73.0, Livewire 4.4.8 y barryvdh/laravel-dompdf 3.1.2 instalados con composer.lock verificado; Git inicializado; .env configurado para MySQL; AGENTS.md y skills de roadmap adaptadas a MVC y validadas.

### [HECHO] INI-003 — Decisiones pendientes y modelo preliminar

- **Capa:** SOLICITADO
- **Fuente:** FTE-02, DEC-2026-11-19-01 a DEC-2026-11-19-05
- **Contexto propietario:** Definición funcional y arquitectura
- **Dependencias:** INI-001
- **Alcance:** Documentar y fijar las decisiones sobre base de datos, servidor de despliegue, umbrales de stock (RF 13), cambios de precio (RF 19), política de devoluciones y defectos (RF 22), estrategia de respaldos (RNF 08) y selección de interfaz Bootstrap 5 nativa.
- **Fuera de alcance:** Alterar las decisiones aprobadas o reabrir consultas resueltas.
- **Invariantes:** Las decisiones confirmadas son vinculantes; registrar cada definición con identificador formal DEC-AAAA-MM-DD-NN.
- **Contratos y archivos previstos:** docs/decisiones/README.md y registros DEC-2026-11-19-01 a DEC-2026-11-19-05
- **Criterios de aceptación:** Decisiones formalmente aprobadas y registradas para MySQL, servidor local, Bootstrap 5, stock < 10, precio manual local a la venta, devolución como cambio y derivación a stock defectuoso, y respaldos diarios con retención de 30 días.
- **Responsable:** Mauricio / Antigravity
- **Fecha de inicio:** 2026-11-19
- **Última actualización:** 2026-11-19
- **Próximo paso:** Tarea concluida. La base técnica y las definiciones permiten iniciar la Fase B (Vistas) en cuanto el usuario lo autorice.
- **Evidencia actual:** Documentos de decisión formalizados en docs/decisiones/DEC-2026-11-19-01 a 05; índice docs/decisiones/README.md actualizado.
