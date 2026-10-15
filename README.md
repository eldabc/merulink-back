# MeruLink — Backend (API)

API REST del sistema de gestión hotelera **MeruLink** (Hotel Plaza Meru). Es el núcleo de datos y de reglas de negocio: expone los servicios que consume el cliente web y concentra la autenticación, los permisos, la planificación de turnos y el resto de los módulos operativos del hotel.

## 📋 Alcance

- **Empleados**: ficha completa, periodos laborales, contactos de emergencia, vacaciones y ausencias, altas y bajas (incluidas las programadas) y asignación de vestuario.
- **Estructura organizativa**: departamentos, sub-departamentos y cargos.
- **Seguridad**: usuarios, roles y permisos, y menú dinámico construido según los permisos del usuario.
- **Planificación de horarios**: turnos y quincenas con flujo de estados (creado → revisado → aprobado → cerrado) y autocompletado.
- **Vestuarios**: lockers, candados, patrones y sus asignaciones.
- **Eventos**: eventos, categorías, plantillas, contactos y sincronización con Google Calendar.
- **Asistencia**: control de asistencia y prenómina (en desarrollo).
- **Integraciones**: consulta automatizada de datos en portales institucionales (SENIAT / IVSS) e importación de información desde Excel.

## 🛠️ Stack tecnológico

- **PHP 8.3** + **Laravel 12** — API REST.
- **PostgreSQL** como base de datos.
- **Laravel Sanctum** para la autenticación por tokens.
- **spatie/laravel-permission** para roles y permisos.
- **maatwebsite/excel** para importación y exportación de hojas de cálculo.
- **Symfony Panther** (navegador headless) para la consulta de portales externos.
- **Docker**: Nginx + PHP-FPM + Supervisor en un contenedor, con un contenedor independiente para la cola de trabajos.

## 🧱 Convenciones del proyecto

- **Respuestas estandarizadas**: todos los endpoints responden con la misma estructura (`status`, `code`, `message`, `data`, `timestamp`) usando el helper `ApiResponseHelper::createResponse()` (ver `response.md`).
- **Menú dirigido por el backend**: la estructura del menú vive en el backend, se filtra por permisos con `MenuService` y el frontend renderiza lo que devuelve `GET /me/menu`.
- **Permisos por empleado**: cada empleado guarda un *snapshot* con sus permisos y los departamentos a los que tiene acceso.
- **Tareas programadas**: generación de eventos recurrentes, cierre y autocompletado de quincenas, bajas programadas y sincronización de ausencias (`routes/console.php`).

## ▶️ Puesta en marcha

El sistema se levanta con Docker Compose desde la raíz del repositorio (backend, base de datos, cola de trabajos y frontend). 

## 📄 Documentación relacionada

- `response.md` — formato estándar de las respuestas de la API.
- `.github/copilot-instructions.md` — estándares de código del proyecto.

* Todos los derechos reservados Hotel Plaza Meru. 2026
