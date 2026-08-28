================================================================================
ESTÁNDARES Y RESPONSABILIDADES: CONTROLADORES
================================================================================

ROL PRINCIPAL:
Orquestar la comunicación entre la Capa de Red (HTTP) y la Capa de Negocio (Servicios).

RESPONSABILIDADES:
1. ORQUESTACIÓN: Recibir el Request, llamar al Servicio y devolver la respuesta.
2. CONTRATO DE API: Garantizar la estructura invariable { "message": "...", "data": { ... } }.
3. AUTORIZACIÓN: Validar permisos de ruta (Middleware) y de recurso (Policies).
4. CAPACIDADES: Inyectar metadatos de permisos ("can") para desacoplar la UI de los Roles.

ESTÁNDARES DE RESPUESTA (API CONTRACT):
* ÉXITO (200/201): Usar siempre $this->apiSuccess($message, $data, $status).
  - La llave "data" debe ser siempre un objeto ({}). Si está vacía, no debe ser null.
* ERROR (4xx/500): Usar siempre $this->apiError($message, $status, $errors).
* MAPEADO DE LLAVES (Data Mapping):
  - Listados (index): Usar nombres en PLURAL (ej. "documents", "blocks", "users").
  - Operaciones Individuales (show/store/update): Usar nombres en SINGULAR (ej. "document", "block", "user").
* ACTUALIZACIÓN ATÓMICA: Tras crear o editar, devolver siempre el objeto completo (usando fresh()) 
  para que el frontend pueda actualizar su estado local sin re-descargar toda la lista.

REGLA DE ORO:
El controlador es un director de orquesta: dice quién entra y qué se hace, pero 
delega el "cómo" se hace a los Servicios.
================================================================================