# Guía Técnica: Implementación de Arquitectura de Rutas Dinámicas

Esta guía detalla la migración del sistema de navegación de Llapiy a una arquitectura de espejo basada en `laravel/wayfinder`.

---

## Fase 1: Backend (Laravel 12)
**Objetivo:** Modularizar y preparar la base para la generación de rutas.

1.  **Instalación de Wayfinder:**
    ```bash
    composer require laravel/wayfinder
    ```
2.  **Refactorización de Controladores:**
    Migrar a controladores atómicos (un solo método `__invoke` o métodos específicos).
3.  **Modularización de Routes:**
    Dividir `routes/web.php` en archivos temáticos (ej: `routes/documentos.php`). Registrarlos en `bootstrap/app.php`.

---

## Fase 2: Infraestructura Frontend (React + TypeScript)
**Objetivo:** Crear el motor que procesará las rutas generadas.

1.  **Configuración de Wayfinder en JS:**
    Directorio: `resources/js/wayfinder/`. Contiene la lógica para manejar parámetros dinámicos y tipos.
2.  **Helper de Utilidad (`toUrl`):**
    En `resources/js/lib/utils.ts`:
    ```typescript
    export function toUrl(url: NonNullable<InertiaLinkProps['href']>): string {
        return typeof url === 'string' ? url : url.url;
    }
    ```
3.  **Hook `useCurrentUrl`:**
    Lógica de comparación de URLs compatible con Laragon (ignora el subdirectorio).

---

## Fase 3: Generación y Módulos de Rutas
**Objetivo:** Crear el "espejo" de las rutas de Laravel en TypeScript.

1.  **Generación Automática:**
    ```bash
    php artisan wayfinder:generate
    ```
2.  **Estructura de Directorios:**
    Organizar `resources/js/routes/` por módulos funcionales.

---

## Fase 4: Refactorización de la Navegación
**Objetivo:** Eliminar strings hardcodeados en `Navigation.ts`.

1.  **Actualización de `Navigation.ts`:**
    Usar funciones de ruta generadas:
    ```typescript
    {
        label: "Documentos",
        href: documentos.url(),
        match: [documentos.url()],
        permission: "documents.view",
    }
    ```
2.  **Uso en Componentes:**
    Determinar estado activo mediante `useCurrentUrl()`.

---

## Beneficios Clave
*   **Fuente de Verdad Única:** Sincronización automática entre PHP y TypeScript.
*   **Tipado Fuerte:** Errores detectados en tiempo de compilación.
*   **Compatibilidad Laragon:** Funciona sin cambios en Virtual Hosts o subcarpetas.
