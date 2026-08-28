# Estándar de Pruebas para API Llapiy: Suite de Testeo de 8 Etapas

Este documento detalla las fases necesarias para garantizar la integridad y el rendimiento del backend.

---

### 1. Etapa de Contrato de Vista (Inertia Props)
*   **Objetivo:** Asegurar que el componente React reciba las "props" con la estructura esperada.
*   **Pruebas:** Verificar el componente devuelto (ej. `Areas/Index`) y que las llaves de las propiedades (`areas`, `filters`, `pagination`) sean exactas.
*   **Evita:** Errores de "Cannot read properties of undefined" en el cliente.

### 2. Etapa de Validación (Gatekeeping)
*   **Objetivo:** Probar exhaustivamente los `FormRequests`.
*   **Pruebas:** Enviar campos vacíos, tipos de datos erróneos o strings excesivamente largos.
*   **Evita:** Errores de SQL de bajo nivel por fallos en la validación de Laravel.

### 3. Etapa de Seguridad y Roles (RBAC)
*   **Objetivo:** Verificar `Policies` y `Middlewares`.
*   **Pruebas:** Intentar acciones no permitidas según el rol (ej. Operador eliminando documentos) y verificar el código 403.
*   **Evita:** Fugas de información y accesos no autorizados.

### 4. Etapa de Integridad de Modelos (Relaciones)
*   **Objetivo:** Validar la consistencia de los modelos y sus relaciones.
*   **Pruebas:** Asegurar que los `foreign_keys` asociados pertenezcan a registros reales.
*   **Evita:** Errores de "Integrity constraint violation" en ejecución.

### 5. Etapa de Reglas de Negocio (Service Layer)
*   **Objetivo:** Probar la lógica compleja fuera de los controladores.
*   **Pruebas:** Cálculos estadísticos, contadores en tiempo real y jerarquías de áreas.
*   **Evita:** Datos erróneos en dashboards o reportes.

### 6. Etapa de Manejo de Archivos (Storage)
*   **Objetivo:** Probar la subida, descarga y visualización de archivos.
*   **Pruebas:** Extensiones no permitidas, discos llenos y streaming de archivos con token.
*   **Evita:** Errores de "File not found" o archivos corruptos.

### 7. Etapa de Resiliencia (Edge Cases)
*   **Objetivo:** Estresar la API ante datos inusuales.
*   **Pruebas:** Peticiones concurrentes, caracteres especiales (tildes, emojis) y paginación masiva.
*   **Evita:** Caídas del servidor por consumo de memoria o errores de codificación.

### 8. Etapa de Rendimiento (N+1 Queries)
*   **Objetivo:** Optimizar las consultas a la base de datos.
*   **Pruebas:** Uso de logs de queries para asegurar el `Eager Loading` de relaciones.
*   **Evita:** Timeouts y lentitud conforme crece la base de datos.
