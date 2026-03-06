================================================================================
ESTÁNDARES Y RESPONSABILIDADES: MIDDLEWARE
================================================================================

ROL PRINCIPAL:
Filtro global de peticiones y control de acceso de primera capa.

RESPONSABILIDADES:
1. AUTENTICACIÓN: Verificar la validez del token (Sanctum).
2. SEGURIDAD (RBAC): Controlar el acceso a los endpoints mediante can:permission.
3. TRANSFORMACIÓN: Limpiar entradas (TrimStrings), manejar proxies y cookies.
4. GATEKEEPING DE RUTA: Detener peticiones no autorizadas ANTES de llegar al controlador.

ESTÁNDARES DE CÓDIGO:
* Centralización: Definir la protección de rutas siempre en routes/api.php.
* Agrupación: Usar Route::group para aplicar middlewares a múltiples recursos.
* Claridad: Usar Gates unificadas (ej. view-documents) si la lógica de acceso 
  depende de múltiples permisos.

REGLA DE ORO:
El Middleware es la "puerta del edificio"; si no tienes invitación, no pasas 
de la recepción.
================================================================================