# Bitácora de Resolución Técnica: Errores y Soluciones (Llapiy)

Este documento registra los problemas críticos encontrados durante la refactorización y sus soluciones definitivas.

---

## 1. Error: 404 Not Found en Assets (JS/CSS) en Laragon
*   **Síntoma:** No se cargan `app.js` o `app.css` vía `llapiy.test`.
*   **Causa Raíz:** Vite generaba rutas absolutas al root del servidor, incompatibles con ciertos Document Roots de Laragon.
*   **Solución:**
    *   Configuración `base: ""` en `vite.config.js`.
    *   Carga dinámica en `app.blade.php`: `@vite(['...', "pages/{$page['component']}.tsx"])`.
    *   Integración de `@laravel/vite-plugin-wayfinder`.

## 2. Error: Unable to locate file in Vite manifest (Casing)
*   **Síntoma:** Excepción de Vite al no encontrar archivos (ej: `Auth/Login.tsx`).
*   **Causa Raíz:** Discrepancia entre mayúsculas en carpetas y minúsculas en el código/manifiesto.
*   **Solución:**
    *   Normalización total de nombres a minúsculas en `resources/js/pages/`, `routes/`, etc.
    *   Actualización de importaciones para usar rutas en minúsculas.

## 3. Error: POST http://llapiy.test/login 404 (Not Found)
*   **Síntoma:** El GET funciona, pero el POST al login devuelve 404.
*   **Causa Raíz:** El Virtual Host apuntaba a la raíz del proyecto en lugar de `/public`, fallando en la reescritura de rutas POST.
*   **Solución:**
    *   Creación de `.htaccess` en la raíz para redirigir tráfico a `public/`.
    *   División del controlador de Login en controladores atómicos (`IndexController` y `LoginController`) con nombres de ruta únicos.

## 4. Error: 419 Unknown Status (CSRF Mismatch)
*   **Síntoma:** Curl o el navegador devuelven 419 tras corregir el 404.
*   **Causa Raíz:** `APP_URL` en `.env` no coincidía con la URL del navegador (`localhost:8000` vs `llapiy.test`).
*   **Solución:** Sincronización de `APP_URL` y compartición de la misma vía `HandleInertiaRequests`.

## 5. Error: Page Not Found (Inertia Resolve)
*   **Síntoma:** Error de JS indicando que no se encuentra el componente React.
*   **Causa Raíz:** Llamadas `Inertia::render()` en PHP usaban mayúsculas mientras el sistema de archivos ya era minúscula.
*   **Solución:** Normalización masiva de llamadas `Inertia::render` a minúsculas (ej: `Inertia::render('auth/login')`).

## 6. Error: TS2322 / TS2440 (TypeScript Conflicts)
*   **Síntoma:** Fallo en el typecheck por conflictos en el campo `foto_perfil`.
*   **Causa Raíz:** Declaraciones duplicadas y asignación de `File` a un campo que esperaba `string`.
*   **Solución:**
    *   Unificación de la interfaz `UserForm`.
    *   Tipos actualizados a `string | File`.
    *   Implementación de `imagePreview` para visualización.

## 7. Error: MissingAppKeyException (Encryption Key)
*   **Síntoma:** Error 500 indicando "No application encryption key has been specified".
*   **Causa Raíz:** Aunque la clave existía en el `.env`, Laravel utilizaba una configuración cacheada vacía o el servidor web no la detectaba.
*   **Solución:** 
    *   Ejecución de `php artisan key:generate --force` para asegurar una clave activa.
    *   Limpieza de caché: `php artisan config:clear`.

## 8. Error: TypeError: Cannot read properties of undefined (reading 'search' / 'attendedCount')
*   **Síntoma:** El componente de Inbox colapsaba al intentar leer propiedades de un objeto `filters` o `stats` nulo.
*   **Causa Raíz:** Falta de robustez en el frontend al procesar datos que el backend enviaba de forma parcial o nula.
*   **Solución:** 
    *   Implementación de valores por defecto en la desestructuración de props (`filters = {}`, `stats = {...}`).
    *   Uso extensivo de Optional Chaining (`filters?.search`) para blindar el renderizado.

## 9. Error: Page not found: ./pages/storage/index.tsx (Case Persistence)
*   **Síntoma:** Tras renombrar archivos, el navegador seguía buscando rutas con mayúsculas.
*   **Causa Raíz:** Controladores PHP invocando `Inertia::render('Storage/Index')` en lugar de la ruta física normalizada en minúsculas.
*   **Solución:** Normalización masiva de todos los strings de renderizado en los controladores a minúsculas y limpieza de caché de rutas (`route:clear`).

## 10. Warning: Duplicate key "server" in vite.config.js
*   **Síntoma:** Advertencia durante el build indicando claves duplicadas.
*   **Causa Raíz:** Existencia de dos bloques `server: {}` independientes en la configuración de Vite.
*   **Solución:** Fusión de los bloques en una única declaración que integra `host` y `watch`.

---

### Estado Final del Proyecto:
*   **Tests Backend:** 42/42 PASSED.
*   **Tests Frontend:** 15/15 PASSED.
*   **Build Vite:** EXITOSO (Sin advertencias).
*   **Typecheck:** 0 ERRORES.
