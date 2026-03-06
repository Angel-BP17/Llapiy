<!-- Documents Sections -->
<div class="space-y-24 sm:space-y-32">
    <section id="mod-docs" class="scroll-mt-32">
        <div class="flex items-center gap-6 mb-12 sm:mb-16">
            <div class="w-14 h-14 rounded-2xl bg-indigo-500/10 dark:bg-indigo-500/20 flex items-center justify-center text-indigo-600 dark:text-indigo-400 shadow-sm ring-1 ring-indigo-500/20">
                <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div>
                <h2 class="text-3xl sm:text-4xl font-black tracking-tight doc-title">Gestión de Documentos</h2>
                <p class="text-sm font-medium text-slate-400 dark:text-slate-500 mt-1">Arquitectura de Carga Bajo Demanda.</p>
            </div>
            <div class="h-px flex-1 bg-gradient-to-r from-indigo-100 dark:from-slate-800 to-transparent transition-colors"></div>
        </div>

        <!-- Index -->
        <div class="endpoint-card get-bg">
            <div class="px-6 py-5 sm:px-10 sm:py-8 flex items-center justify-between border-b border-indigo-100/30 dark:border-slate-800/50 bg-white/40 dark:bg-slate-900/40 backdrop-blur-sm transition-colors">
                <div class="flex items-center gap-4">
                    <span class="method-label bg-emerald-500">GET</span>
                    <code class="text-sm sm:text-lg font-bold text-slate-800 dark:text-indigo-300">/api/documents</code>
                </div>
            </div>
            <div class="p-6 sm:p-10 grid grid-cols-1 xl:grid-cols-2 gap-10 sm:gap-16">
                <div>
                    <h4 class="text-xs font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-[0.2em] mb-4">Listado Delgado</h4>
                    <p class="text-base sm:text-lg doc-text leading-relaxed mb-6">Optimizado para alta concurrencia. No incluye la relación <code class="text-indigo-500">campos</code> (metadatos pesados) para ahorrar RAM.</p>
                    <div class="p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/20 border border-amber-100 dark:border-amber-900/30 text-xs text-amber-700 dark:text-amber-400 leading-relaxed">
                        <span class="font-black uppercase tracking-widest block mb-1">Nota IA Frontend:</span>
                        Cada documento incluye un objeto <code class="bg-amber-100 dark:bg-amber-900/50 px-1 rounded">can</code>. Úsalo para habilitar o deshabilitar botones de edición sin calcular lógica de áreas en el cliente.
                    </div>
                </div>
                <div class="json-card border-emerald-500/20">
                    <div class="json-header !border-emerald-500/10">
                        <span class="json-label label-res">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></span>
                            Response Sample
                        </span>
                    </div>
                    <div class="json-body">
<pre>{
  "data": {
    "documents": {
      "data": [
        { 
          "id": 1, 
          "asunto": "...", 
          "can": { "update": true, "delete": false } 
        }
      ]
    }
  }
}</pre>
                    </div>
                </div>
            </div>
        </div>

        <!-- Show -->
        <div class="endpoint-card get-bg">
            <div class="px-6 py-5 sm:px-10 sm:py-8 flex items-center justify-between border-b border-indigo-100/30 dark:border-slate-800/50 bg-white/40 dark:bg-slate-900/40 backdrop-blur-sm transition-colors">
                <div class="flex items-center gap-4">
                    <span class="method-label bg-emerald-500">GET</span>
                    <code class="text-sm sm:text-lg font-bold text-slate-800 dark:text-indigo-300">/api/documents/{id}</code>
                </div>
            </div>
            <div class="p-6 sm:p-10 grid grid-cols-1 xl:grid-cols-2 gap-10">
                <div>
                    <h4 class="text-xs font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-[0.2em] mb-4">Detalle Completo</h4>
                    <p class="text-base doc-text">Usa este endpoint para poblar el **Modal de Edición**. Aquí sí se incluye la relación completa de <code class="text-indigo-500">campos</code> y tipos.</p>
                </div>
                <div class="json-card border-emerald-500/20">
                    <div class="json-header !border-emerald-500/10"><span class="json-label">Full Metadata Schema</span></div>
                    <div class="json-body">
<pre>{
  "data": {
    "document": {
      "id": 1,
      "campos": [ { "id": 5, "dato": "2024", "campo_type": {...} } ]
    }
  }
}</pre>
                    </div>
                </div>
            </div>
        </div>

        <!-- File Streaming -->
        <div class="endpoint-card get-bg">
            <div class="px-6 py-5 sm:px-10 sm:py-8 border-b border-blue-100/30 dark:border-slate-800/50 bg-white/40 dark:bg-slate-900/40 backdrop-blur-sm transition-colors">
                <div class="flex items-center gap-4">
                    <span class="method-label bg-blue-500">GET</span>
                    <code class="text-sm sm:text-lg font-bold text-slate-800 dark:text-indigo-300">/api/documents/{id}/file</code>
                </div>
            </div>
            <div class="p-6 sm:p-10">
                <p class="text-base doc-text mb-4 italic">Retorna el archivo binario (PDF/Imagen) para ser usado en etiquetas <code class="text-blue-500">&lt;iframe&gt;</code> o <code class="text-blue-500">&lt;embed&gt;</code>.</p>
                <div class="p-4 rounded-xl bg-slate-900 text-slate-300 text-xs font-mono">
                    Content-Type: application/pdf <br>
                    Content-Disposition: inline; filename="..."
                </div>
            </div>
        </div>
    </section>

    <!-- Metadata Configuration -->
    <section id="mod-config" class="scroll-mt-32">
        <div class="flex items-center gap-6 mb-12">
            <div class="w-14 h-14 rounded-2xl bg-emerald-500/10 dark:bg-emerald-500/20 flex items-center justify-center text-emerald-600 dark:text-emerald-400 shadow-sm ring-1 ring-emerald-500/20">
                <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div>
                <h2 class="text-3xl font-black tracking-tight doc-title">Estructura de Metadatos</h2>
                <p class="text-sm font-medium text-slate-400 mt-1">Configuración de Tipos y Campos.</p>
            </div>
        </div>

        <div class="endpoint-card post-bg">
            <div class="p-6 sm:p-10 grid grid-cols-1 xl:grid-cols-2 gap-10">
                <div>
                    <h4 class="text-xs font-black text-indigo-600 uppercase tracking-widest mb-4">Campos Dinámicos</h4>
                    <p class="text-sm doc-text leading-relaxed">Define reglas de validación para los metadatos. Soporta tipos: <code class="text-indigo-500">string, int, boolean, enum, float</code>.</p>
                </div>
                <div class="json-card border-blue-500/20">
                    <div class="json-header !border-blue-500/10"><span class="json-label">Field Definition Schema</span></div>
                    <div class="json-body">
<pre>{
  "name": "Año de emisión",
  "data_type": "int",
  "length": 4,
  "is_nullable": false
}</pre>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Blocks Section -->
    <section id="mod-blocks" class="scroll-mt-32">
        <div class="flex items-center gap-6 mb-12 sm:mb-16">
            <div class="w-14 h-14 rounded-2xl bg-violet-500/10 dark:bg-violet-500/20 flex items-center justify-center text-violet-600 dark:text-violet-400 shadow-sm ring-1 ring-violet-500/20">
                <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            </div>
            <div>
                <h2 class="text-3xl sm:text-4xl font-black tracking-tight doc-title">Bloques Físicos</h2>
                <p class="text-sm font-medium text-slate-400 dark:text-slate-500 mt-1">Gestión de paquetes documentales.</p>
            </div>
            <div class="h-px flex-1 bg-gradient-to-r from-violet-100 dark:from-slate-800 to-transparent transition-colors"></div>
        </div>

        <div class="endpoint-card post-bg">
            <div class="px-6 py-5 sm:px-10 sm:py-8 flex items-center justify-between border-b border-indigo-100/30 dark:border-slate-800/50 bg-white/40 dark:bg-slate-900/40 backdrop-blur-sm transition-colors">
                <div class="flex items-center gap-4">
                    <span class="method-label bg-blue-500">POST</span>
                    <code class="text-sm sm:text-lg font-bold text-slate-800 dark:text-indigo-300">/api/blocks</code>
                </div>
            </div>
            <div class="p-6 sm:p-10 grid grid-cols-1 xl:grid-cols-2 gap-10">
                <div>
                    <h4 class="text-xs font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-[0.2em] mb-4">Actualización Atómica</h4>
                    <p class="text-base doc-text">Tanto al crear como al editar, la API devuelve el objeto <code class="text-indigo-500">block</code> completo. Úsalo para actualizar tu estado local sin llamar a <code class="text-indigo-500">loadDocuments()</code>.</p>
                </div>
                <div class="json-card border-blue-500/20">
                    <div class="json-header !border-blue-500/10"><span class="json-label">Atomic Response Contract</span></div>
                    <div class="json-body">
<pre>{
  "message": "Actualizado",
  "data": {
    "block": { "id": 1, "n_bloque": "B-01", "asunto": "..." }
  }
}</pre>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
