<!-- Organization Sections -->
<div class="space-y-24 sm:space-y-32">
    <!-- Areas Section -->
    <section id="mod-areas" class="scroll-mt-32">
        <div class="flex items-center gap-6 mb-12 sm:mb-16">
            <div class="w-14 h-14 rounded-2xl bg-indigo-500/10 dark:bg-indigo-500/20 flex items-center justify-center text-indigo-600 dark:text-indigo-400 shadow-sm ring-1 ring-indigo-500/20">
                <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
            <div>
                <h2 class="text-3xl sm:text-4xl font-black tracking-tight doc-title">Estructura Organizacional</h2>
                <p class="text-sm font-medium text-slate-400 dark:text-slate-500 mt-1">Jerarquía de Áreas, Grupos y Subgrupos.</p>
            </div>
            <div class="h-px flex-1 bg-gradient-to-r from-indigo-100 dark:from-slate-800 to-transparent transition-colors"></div>
        </div>

        <!-- Areas List -->
        <div class="endpoint-card get-bg">
            <div class="px-6 py-5 sm:px-10 sm:py-8 flex items-center justify-between border-b border-indigo-100/30 dark:border-slate-800/50 bg-white/40 dark:bg-slate-900/40 backdrop-blur-sm transition-colors">
                <div class="flex items-center gap-4">
                    <span class="method-label bg-emerald-500">GET</span>
                    <code class="text-sm sm:text-lg font-bold text-slate-800 dark:text-indigo-300">/api/areas</code>
                </div>
            </div>
            <div class="p-6 sm:p-10 grid grid-cols-1 xl:grid-cols-2 gap-10 sm:gap-16">
                <div>
                    <h4 class="text-xs font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-[0.2em] mb-4">Árbol Organizacional</h4>
                    <p class="text-base sm:text-lg doc-text leading-relaxed mb-6">Obtiene la estructura completa optimizada. Las áreas contienen grupos vinculados a través de <code class="text-indigo-500">area_group_type</code>.</p>
                </div>
                <div class="json-card border-emerald-500/20">
                    <div class="json-header !border-emerald-500/10">
                        <span class="json-label label-res">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></span>
                            Consistent Hierarchy
                        </span>
                    </div>
                    <div class="json-body">
<pre>{
  "data": {
    "areas": {
      "data": [
        {
          "id": 1,
          "descripcion": "Gerencia",
          "groups": [ { "id": 5, "descripcion": "Finanzas" } ]
        }
      ]
    }
  }
}</pre>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Storage Section -->
    <section id="mod-storage" class="scroll-mt-32">
        <div class="flex items-center gap-6 mb-12 sm:mb-16">
            <div class="w-14 h-14 rounded-2xl bg-amber-500/10 dark:bg-amber-500/20 flex items-center justify-center text-amber-600 dark:text-amber-400 shadow-sm ring-1 ring-amber-500/20">
                <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2z"/></svg>
            </div>
            <div>
                <h2 class="text-3xl sm:text-4xl font-black tracking-tight doc-title">Archivo Físico</h2>
                <p class="text-sm font-medium text-slate-400 dark:text-slate-500 mt-1">Gestión jerárquica: Secciones > Andamios > Cajas.</p>
            </div>
            <div class="h-px flex-1 bg-gradient-to-r from-amber-100 dark:from-slate-800 to-transparent transition-colors"></div>
        </div>

        <div class="endpoint-card post-bg">
            <div class="p-6 sm:p-10">
                <h4 class="text-xs font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-[0.2em] mb-6">Rutas Jerárquicas (Nested Resources)</h4>
                <div class="space-y-4">
                    <div class="p-4 rounded-xl bg-slate-900 font-mono text-xs sm:text-sm text-indigo-300 border border-slate-800">
                        <span class="text-blue-400 font-bold">POST</span> /api/sections <br>
                        <span class="text-slate-500 italic"># Crear Sección raíz</span>
                    </div>
                    <div class="p-4 rounded-xl bg-slate-900 font-mono text-xs sm:text-sm text-indigo-300 border border-slate-800">
                        <span class="text-blue-400 font-bold">POST</span> /api/sections/<span class="text-amber-400">{section}</span>/andamios <br>
                        <span class="text-slate-500 italic"># Crear andamio dentro de sección</span>
                    </div>
                    <div class="p-4 rounded-xl bg-slate-900 font-mono text-xs sm:text-sm text-indigo-300 border border-slate-800">
                        <span class="text-blue-400 font-bold">POST</span> /api/sections/<span class="text-amber-400">{section}</span>/andamios/<span class="text-amber-400">{andamio}</span>/boxes <br>
                        <span class="text-slate-500 italic"># Crear caja dentro de andamio</span>
                    </div>
                </div>
                <div class="mt-8 p-5 rounded-2xl bg-blue-50 dark:bg-blue-950/20 border border-blue-100 dark:border-blue-900/30 text-xs text-blue-700 dark:text-blue-400 leading-relaxed shadow-sm">
                    <span class="font-black uppercase block mb-1">Nota IA de Integridad:</span>
                    La API garantiza que un hijo solo pueda existir si el padre está presente en la base de datos. Todas las rutas de escritura devuelven el objeto actualizado para permitir actualizaciones de estado atómicas en el frontend.
                </div>
            </div>
        </div>
    </section>
</div>
