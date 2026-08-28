================================================================================
ESTÁNDARES Y RESPONSABILIDADES: SERVICE LAYER
================================================================================

ROL PRINCIPAL:
Orquestar la lógica de negocio y la persistencia de datos.

RESPONSABILIDADES:
1. CONSULTAS FILTRADAS: Implementar seguridad a nivel de base de datos (SQL) 
   basada en los permisos del usuario (all, group, own).
2. TRANSACCIONES: Usar DB::transaction para cualquier operación de escritura 
   que afecte a múltiples tablas.
3. CACHÉ: Gestionar la lectura eficiente (Cache::remember) y la invalidación 
   (Cache::forget) tras cambios en los datos.
4. INTEGRIDAD: Asegurar que los modelos se guarden con las relaciones correctas.

ESTÁNDARES DE CÓDIGO:
* Stateless: Los servicios no deben guardar estado entre peticiones.
* Retorno: Los métodos de creación/actualización deben devolver el Modelo 
  para cumplir con el contrato de la API.
* Optimización: Usar select() para limitar columnas y with() para evitar N+1.

REGLA DE ORO:
El controlador pide "qué" hacer, el servicio sabe "cómo" hacerlo eficientemente.
================================================================================