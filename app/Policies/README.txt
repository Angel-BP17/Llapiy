================================================================================
ESTÁNDARES Y RESPONSABILIDADES: POLICIES
================================================================================

ROL PRINCIPAL:
Autorización quirúrgica sobre recursos específicos (Propiedad y Pertenencia).

RESPONSABILIDADES:
1. CONTROL DE ACCESO: Determinar si un usuario específico puede realizar 
   una acción sobre un modelo específico (ej. "Solo el dueño puede editar").
2. BYPASS ADMINISTRATIVO: Implementar el método before() para asegurar 
   que el ADMINISTRADOR siempre tenga acceso total.
3. SEGMENTACIÓN: Validar pertenencia a Áreas, Grupos o Subgrupos.

ESTÁNDARES DE CÓDIGO:
* Suffix: Todas las clases deben terminar en "Policy".
* Integración: Se ejecutan dentro de los controladores mediante $this->authorize().
* Consistencia: Usar los permisos definidos en Spatie para las comprobaciones ($user->can).

REGLA DE ORO:
Un Middleware dice si puedes usar el "botón", la Policy dice si ese botón 
puede tocar "este dato" en particular.
================================================================================