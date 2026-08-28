# Estrategia de Testing Frontend: Etapas de Cobertura Total

Para garantizar la robustez del frontend, cada suite de tests (`.test.tsx`) debe cumplir con las siguientes etapas:

---

### Etapa 1: Contrato y Renderizado
*   **Objetivo:** Verificar que el componente carga correctamente.
*   **Pruebas:** Presencia de elementos clave, validación de props obligatorias y renderizado de children.

### Etapa 2: Integridad de Rutas (Wayfinder)
*   **Objetivo:** Validar el sistema de espejo backend-frontend.
*   **Pruebas:** Verificar que las URLs generadas coincidan con las expectativas y que el helper `toUrl` funcione correctamente.

### Etapa 3: Seguridad y Visibilidad (RBAC)
*   **Objetivo:** Asegurar que el usuario solo ve lo permitido por sus permisos.
*   **Pruebas:** Ocultamiento de botones (crear/editar/borrar) y secciones de navegación según el rol.

### Etapa 4: Interacción y Lógica de Formularios
*   **Objetivo:** Probar el comportamiento dinámico.
*   **Pruebas:** Apertura de modales, validación de inputs y simulación de envíos vía Inertia.

### Etapa 5: Resiliencia y Casos Borde
*   **Objetivo:** Evitar errores de "undefined" o fallos silenciosos.
*   **Pruebas:** Manejo de listas vacías y datos nulos/malformados.

### Etapa 6: Integridad de Assets y Manifiesto
*   **Objetivo:** Prevenir errores 404 en producción/Laragon.
*   **Pruebas:** Validación de rutas de recursos estáticos y resolución de componentes dinámicos.

### Etapa 7: Consistencia de Rutas Dinámicas
*   **Objetivo:** Garantizar que todas las peticiones (POST, PUT, DELETE) usen el motor de rutas.
*   **Pruebas:** Intercepción de llamadas para asegurar el uso de helpers en lugar de strings manuales.

### Etapa 8: Compatibilidad de Entornos
*   **Objetivo:** Funcionamiento en Virtual Hosts y subcarpetas.
*   **Pruebas:** Simulación de cambios en `window.location.pathname` para verificar adaptabilidad.

### Etapa 9: Coherencia de Casing
*   **Objetivo:** Evitar discrepancias de mayúsculas/minúsculas en el manifiesto de Vite.
*   **Pruebas:** Verificación de importaciones consistentes y nombres de archivos.

---

## Orden de Implementación Recomendado
1.  Setup global (Mocks).
2.  Tests de Layout (Sidebar, Navbar).
3.  Tests de Páginas de Módulo (Index, Show, Edit).
4.  Tests de Componentes Atómicos.
