================================================================================
ESTÁNDARES Y RESPONSABILIDADES: FORM REQUESTS
================================================================================

ROL PRINCIPAL:
Garantizar la integridad de los datos de entrada (Gatekeeping de Datos).

RESPONSABILIDADES:
1. VALIDACIÓN: Definir reglas estrictas (tipos, longitud, existencia en DB).
2. AUTORIZACIÓN: El método authorize() debe devolver SIEMPRE true. 
   La seguridad de acceso se delega a los Middlewares y Policies.
3. TRANSFORMACIÓN: Usar prepareForValidation() para normalizar datos (ej. mayúsculas).

ESTÁNDARES DE CÓDIGO:
* Suffix: Todas las clases deben terminar en "Request".
* Seguridad: Implementar límites de longitud (max:255) para evitar errores SQL.
* Reutilización: Usar Traits para validaciones compartidas (ej. HasDocumentValidation).

REGLA DE ORO:
Un Form Request NO debe contener lógica de negocio ni consultas pesadas a la DB.
================================================================================