# Estándares y Prevención de Errores: Llapiy

**Tecnologías:** Laravel + Inertia.js + React  
Este documento define las reglas obligatorias de desarrollo para evitar errores comunes en la integración backend-frontend.

---

## 1. Controladores y Respuestas (Inertia vs API)

*   **REGLA:** Los controladores web que sirven vistas **DEBEN** retornar `Inertia::render()`.
    *   *Nota:* No utilizar métodos diseñados para APIs puras (como `$this->apiSuccess()`) en rutas web, ya que el navegador espera un componente React.
*   **REGLA:** Los métodos de escritura (`store`, `update`, `destroy`) **DEBEN** retornar redirecciones (`redirect()->back()` o `redirect()->route()`) junto con mensajes flash (`->with('message', '...')`), nunca respuestas JSON.
    *   *Excepción:* Peticiones explícitas vía Axios que esperan JSON.

---

## 2. Manejo de Paginación en Inertia

*   **REGLA:** Nunca pasar un objeto `Paginator` completo a una propiedad de React que espera un arreglo (ej. para `.map()`).
*   **Forma Correcta:** Desestructurar los elementos usando `->items()` y enviar la metadata aparte.

```php
// Backend
$paginator = Model::paginate();
return Inertia::render('View', [
    'records' => $paginator->items(),
    'pagination' => [
        'total' => $paginator->total(),
        'current_page' => $paginator->currentPage(),
        'last_page' => $paginator->lastPage(),
    ]
]);
```

---

## 3. Servicios y Controladores (Estructura de Datos)

*   **REGLA:** El Controlador debe verificar estrictamente las llaves del arreglo devuelto por el Servicio.
*   **Solución:** Si la vista requiere datos adicionales que el servicio no proporciona, el controlador debe obtenerlos directamente a través del Modelo correspondiente.

---

## 4. Robustez en el Frontend (React / TypeScript)

*   **REGLA:** **NUNCA** asumir que una propiedad existe o no es nula al renderizar.
*   **Forma Correcta:** Usar Optional Chaining (`?.`) y valores por defecto (`||`).

```tsx
// INCORRECTO: 
{users.map(u => <span>{u.role.name}</span>)}

// CORRECTO:
{users?.map(u => <span>{u?.role?.name || 'Sin rol'}</span>)}
```

---

## 5. Envío de Archivos (Multipart) con Inertia y Laravel

*   **REGLA:** Laravel y PHP no procesan nativamente archivos (`$_FILES`) en peticiones `PUT` o `PATCH`.
*   **Forma Correcta:** Usar `POST` e incluir el método falsificado `_method: 'PUT'`.

```ts
// Frontend (React)
const payload = { ...form, file: miArchivo, _method: 'PUT' };
router.post(`/recurso/${id}`, payload, { forceFormData: true });
```

---

## 6. Normalización de Datos

*   **REGLA:** El backend debe entregar los datos en la estructura más plana posible.
*   **Ejemplo:** Aplanar colecciones de permisos a un simple arreglo de strings usando `pluck('name')->toArray()`.
