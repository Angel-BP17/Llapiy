# Informe Técnico: Refacción Arquitectónica y Solución de Errores 404

**Proyecto:** Llapiy  
**Objetivo:** Igualar la robustez del proyecto "Ejemplo" (Laravel Starter Kit) y solucionar fallos de rutas/assets en entornos Laragon.

---

## 1. Hallazgos Críticos (Análisis Comparativo)

*   **A. Desconexión de Rutas:** Llapiy utilizaba strings hardcodeados (ej: `"/login"`) que no se adaptaban a subdirectorios. El proyecto "Ejemplo" utiliza un espejo (mirroring) donde el frontend conoce exactamente las rutas del backend mediante **Wayfinder**.
*   **B. Fallo en el Build de Vite:** Los activos (JS/CSS) daban error 404 porque Vite generaba rutas absolutas al root del dominio. Se requiere un plugin de Wayfinder para Vite que sincronice el manifiesto.
*   **C. Controladores Monolíticos:** Los controladores gestionaban demasiadas responsabilidades, dificultando la generación automática de un árbol de acciones limpio para el frontend.
*   **D. Redundancia de Middleware:** Se aplicaba el middleware `web` múltiples veces en `bootstrap/app.php` y archivos de rutas, provocando inconsistencias en cookies de sesión y CSRF (errores 404/419 en peticiones POST).
*   **E. Vulnerabilidad de Cifrado:** Fallos de arranque por `APP_KEY` no sincronizada, resueltos mediante regeneración forzada.
*   **F. Fragilidad en el Cliente:** Errores de `TypeError` en el frontend por asunciones de datos no nulos, corregidos mediante blindaje de props.

---

## 2. Tareas Realizadas

### A. Fase de Backend (Modularización)
*   Instalación de `laravel/wayfinder`.
*   División de controladores monolíticos en **Controladores Atómicos** (Single Action Controllers) para todos los módulos: Usuarios, Documentos, Bloques, Roles, Almacenamiento, Áreas, Inbox y Auth.
*   Creación de 10 nuevos archivos de rutas en `routes/*.php`.
*   Centralización de la carga de rutas en `web.php` mediante `require` para eliminar lógica compleja en el bootstrap.

### B. Fase de Frontend (Infraestructura)
*   Implementación del motor **Wayfinder** en `resources/js/wayfinder/`.
*   Creación del hook `useCurrentUrl` para detección inteligente de navegación en Laragon.
*   Creación del helper `toUrl` para normalizar objetos de ruta y strings.
*   Instalación e integración del plugin `@laravel/vite-plugin-wayfinder` en Vite.

### C. Fase de Testing (Calidad)
*   Definición de una estrategia de **9 etapas** para asegurar cobertura total (Contrato, RBAC, Resiliencia, Integridad de Assets, Casing, etc.).
*   Actualización de toda la suite de tests del backend (Feature) para validar componentes Inertia en minúsculas.
*   Creación de tests del frontend con **Vitest** y **React Testing Library**.

---

## 3. Cambios Realizados en el Código (Resumen)

*   **`bootstrap/app.php`**: Simplificación total del routing.
*   **`vite.config.js`**: Inclusión de `base: ""` y el plugin wayfinder. Fusión de bloques de servidor.
*   **`app.blade.php`**: Corrección de la directiva `@vite` para usar un bundle estable y evitar errores de localización en el manifiesto.
*   **`HandleInertiaRequests.php`**: Se comparte `app_url` y `flash` messages para dar contexto y feedback al frontend.
*   **`Navigation.ts`**: Eliminación de strings manuales; ahora usa funciones como `dashboard.url()` o `users.index.url()`.
*   **Normalización de Archivos**: Todo el directorio `resources/js/pages` y los controladores PHP ahora usan minúsculas consistentes para evitar discrepancias de casing.

---

## 4. Conclusión y Estado Actual

Llapiy ha evolucionado de una aplicación con navegación manual a una plataforma con **arquitectura de espejo**. Cualquier cambio de ruta en el backend se refleja automáticamente en el frontend tras ejecutar `php artisan wayfinder:generate`. La configuración de Vite es ahora agnóstica al entorno, permitiendo el funcionamiento en Virtual Hosts o subcarpetas sin errores de assets o peticiones POST.

---
*Memoria Técnica - Desarrollo Full Stack Experto*
