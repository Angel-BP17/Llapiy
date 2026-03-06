<!-- System & Errors Sections -->
<div class="space-y-24 sm:space-y-32">
    <section id="mod-system" class="scroll-mt-32">
        <div class="flex items-center gap-6 mb-12 sm:mb-16">
            <div class="w-14 h-14 rounded-2xl bg-rose-500/10 dark:bg-rose-500/20 flex items-center justify-center text-rose-600 dark:text-rose-400 shadow-sm ring-1 ring-rose-500/20">
                <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div>
                <h2 class="text-3xl sm:text-4xl font-black tracking-tight doc-title">Mantenimiento</h2>
                <p class="text-sm font-medium text-slate-400 dark:text-slate-500 mt-1">Operaciones críticas del sistema.</p>
            </div>
            <div class="h-px flex-1 bg-gradient-to-r from-rose-100 dark:from-slate-800 to-transparent transition-colors"></div>
        </div>

        <!-- Clear All -->
        <div class="endpoint-card delete-bg">
            <div class="px-6 py-5 sm:px-10 sm:py-8 border-b border-rose-100/30 dark:border-slate-800/50 bg-white/40 dark:bg-slate-900/40 backdrop-blur-sm transition-colors">
                <div class="flex items-center gap-4">
                    <span class="method-label bg-rose-500">DELETE</span>
                    <code class="text-sm sm:text-lg font-bold text-slate-800 dark:text-indigo-300">/api/admin/clear-all</code>
                </div>
            </div>
            <div class="p-6 sm:p-10">
                <div class="flex items-center gap-4 p-5 rounded-2xl bg-rose-50 dark:bg-rose-950/20 border border-rose-100 dark:border-rose-900 shadow-sm mb-6">
                    <div class="w-12 h-12 rounded-xl bg-white dark:bg-slate-800 flex items-center justify-center border border-rose-200 dark:border-rose-900 shadow-sm text-rose-500">
                        <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-black text-rose-700 dark:text-rose-400 uppercase tracking-widest">Atención: Acción Destructiva</p>
                        <p class="text-xs text-rose-600 dark:text-rose-500 font-medium italic">Protegido con el permiso <code class="text-rose-500 font-bold">clear-system</code>.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Errors Section -->
    <section id="mod-errors" class="scroll-mt-32 pb-24">
        <div class="flex items-center gap-6 mb-12 sm:mb-16">
            <div class="w-14 h-14 rounded-2xl bg-amber-500/10 dark:bg-amber-500/20 flex items-center justify-center text-amber-600 dark:text-amber-400 shadow-sm ring-1 ring-amber-500/20">
                <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div>
                <h2 class="text-3xl sm:text-4xl font-black tracking-tight doc-title">Manejo de Errores</h2>
                <p class="text-sm font-medium text-slate-400 dark:text-slate-500 mt-1">Contrato de errores estándar.</p>
            </div>
            <div class="h-px flex-1 bg-gradient-to-r from-amber-100 dark:from-slate-800 to-transparent transition-colors"></div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 sm:gap-12">
            <div class="p-6 sm:p-10 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm">
                <h4 class="text-lg font-black text-slate-900 dark:text-white mb-6 flex items-center gap-3">
                    <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                    Validación Fallida (422)
                </h4>
                <div class="json-card !mt-0 border-rose-500/20 shadow-2xl">
                    <div class="json-header !border-rose-500/10"><span class="json-label !text-rose-400">Error Contract Schema</span></div>
                    <div class="json-body">
<pre>{
  "message": "The given data was invalid.",
  "errors": {
    "dni": ["El formato del DNI es inválido"]
  }
}</pre>
                    </div>
                </div>
            </div>

            <div class="p-6 sm:p-10 bg-slate-50/50 dark:bg-indigo-950/10 border border-slate-200/60 dark:border-indigo-800/30 rounded-3xl">
                <h4 class="text-xs font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-widest mb-10">Otros Estados HTTP</h4>
                <div class="space-y-8">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-200 dark:border-indigo-900/30">
                        <span class="text-lg font-black doc-title">401</span>
                        <span class="text-[10px] font-bold text-slate-400 italic">Unauthenticated</span>
                    </div>
                    <div class="flex items-center justify-between pb-4 border-b border-slate-200 dark:border-indigo-900/30">
                        <span class="text-lg font-black doc-title">403</span>
                        <span class="text-[10px] font-bold text-slate-400 italic">Policy Denied (RBAC)</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-lg font-black doc-title">404</span>
                        <span class="text-[10px] font-bold text-slate-400 italic">Not Found</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
