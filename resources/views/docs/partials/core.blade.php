<!-- Core Services Sections -->
<div class="space-y-24 sm:space-y-32">
    <!-- Auth Section -->
    <section id="mod-auth" class="scroll-mt-32">
        <div class="flex items-center gap-6 mb-12 sm:mb-16">
            <div class="w-14 h-14 rounded-2xl bg-indigo-500/10 dark:bg-indigo-500/20 flex items-center justify-center text-indigo-600 dark:text-indigo-400 shadow-sm ring-1 ring-indigo-500/20">
                <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            </div>
            <div>
                <h2 class="text-3xl sm:text-4xl font-black tracking-tight doc-title">Autenticación</h2>
                <p class="text-sm font-medium text-slate-400 dark:text-slate-500 mt-1">Gestión de sesiones y seguridad de acceso.</p>
            </div>
            <div class="h-px flex-1 bg-gradient-to-r from-indigo-100 dark:from-slate-800 to-transparent transition-colors"></div>
        </div>
        
        <div class="endpoint-card post-bg">
            <div class="px-6 py-5 sm:px-10 sm:py-8 flex flex-wrap items-center justify-between gap-6 border-b border-indigo-100/30 dark:border-slate-800/50 bg-white/40 dark:bg-slate-900/40 backdrop-blur-sm transition-colors">
                <div class="flex items-center gap-4">
                    <span class="method-label bg-blue-500">POST</span>
                    <code class="text-sm sm:text-lg font-bold text-slate-800 dark:text-indigo-300">/api/auth/login</code>
                </div>
                <div class="flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-100 dark:border-emerald-800/50">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    <span class="text-[10px] font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-widest">Acceso Público</span>
                </div>
            </div>
            <div class="p-6 sm:p-10 grid grid-cols-1 xl:grid-cols-2 gap-10 sm:gap-16">
                <div>
                    <h4 class="text-xs font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-[0.2em] mb-4">Descripción General</h4>
                    <p class="text-base sm:text-lg doc-text leading-relaxed mb-8">Valida las credenciales y genera un token **Bearer** (Sanctum).</p>
                    <h4 class="text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-6">Parámetros Body</h4>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-4 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-800">
                            <code class="font-bold text-indigo-600 dark:text-indigo-400">user_name</code>
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Requerido</span>
                        </div>
                        <div class="flex items-center justify-between p-4 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-800">
                            <code class="font-bold text-indigo-600 dark:text-indigo-400">password</code>
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Requerido</span>
                        </div>
                    </div>
                </div>
                <div class="space-y-8">
                    <div class="json-card border-blue-500/20 shadow-2xl">
                        <div class="json-header !border-blue-500/10">
                            <span class="json-label label-req">
                                <span class="w-2 h-2 rounded-full bg-blue-500 shadow-[0_0_8px_rgba(59,130,246,0.5)]"></span>
                                HTTP Request Body
                            </span>
                        </div>
                        <div class="json-body"><pre>{"user_name": "admin", "password": "password"}</pre></div>
                    </div>
                    <div class="json-card border-emerald-500/20 shadow-2xl">
                        <div class="json-header !border-emerald-500/10">
                            <span class="json-label label-res">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></span>
                                Success Response
                            </span>
                        </div>
                        <div class="json-body"><pre>{
  "message": "Sesión iniciada",
  "data": {
    "token": "1|ABC...",
    "user": { "id": 1, "name": "..." }
  }
}</pre></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Profile Section -->
    <section id="mod-profile" class="scroll-mt-32">
        <!-- (Contenido anterior del perfil...) -->
    </section>

    <!-- Dashboard Section -->
    <section id="mod-dashboard" class="scroll-mt-32">
        <div class="flex items-center gap-6 mb-12 sm:mb-16">
            <div class="w-14 h-14 rounded-2xl bg-indigo-500/10 dark:bg-indigo-500/20 flex items-center justify-center text-indigo-600 dark:text-indigo-400 shadow-sm ring-1 ring-indigo-500/20">
                <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </div>
            <div>
                <h2 class="text-3xl sm:text-4xl font-black tracking-tight doc-title">Métricas Globales</h2>
                <p class="text-sm font-medium text-slate-400 dark:text-slate-500 mt-1">Estadísticas y agregados para el Dashboard.</p>
            </div>
            <div class="h-px flex-1 bg-gradient-to-r from-indigo-100 dark:from-slate-800 to-transparent"></div>
        </div>
        <div class="endpoint-card get-bg">
            <div class="px-6 py-5 sm:px-10 sm:py-8 border-b border-indigo-100/30 dark:border-slate-800/50 bg-white/40 dark:bg-slate-900/40 backdrop-blur-sm transition-colors">
                <div class="flex items-center gap-4">
                    <span class="method-label bg-emerald-500">GET</span>
                    <code class="text-sm sm:text-lg font-bold text-slate-800 dark:text-indigo-300">/api/dashboard</code>
                </div>
            </div>
            <div class="p-6 sm:p-10 grid grid-cols-1 xl:grid-cols-2 gap-10">
                <div>
                    <h4 class="text-xs font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-[0.2em] mb-4">Información de Negocio</h4>
                    <p class="text-base doc-text leading-relaxed mb-6">Entrega un resumen consolidado de la actividad. Los datos están filtrados automáticamente según el rol del usuario (los operadores solo ven sus propias estadísticas).</p>
                    <div class="space-y-3">
                        <div class="flex items-center gap-3 text-xs font-medium text-slate-500">
                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                            Conteo total de documentos y bloques.
                        </div>
                        <div class="flex items-center gap-3 text-xs font-medium text-slate-500">
                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                            Distribución porcentual por Tipo de Documento.
                        </div>
                        <div class="flex items-center gap-3 text-xs font-medium text-slate-500">
                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                            Serie temporal de los últimos 17 días.
                        </div>
                    </div>
                </div>
                <div class="json-card border-emerald-500/20 shadow-2xl">
                    <div class="json-header !border-emerald-500/10"><span class="json-label">Stats Contract Schema</span></div>
                    <div class="json-body"><pre>{
  "data": {
    "userCount": 5,
    "documentCount": 150,
    "documentosRecientes": [
      { "fecha": "2024-03-01", "cantidad": 12 }
    ],
    "documentosPorTipo": [
      { "tipo": "Oficio", "porcentaje": 45.5 }
    ],
    "documentosPorMes": [
      { "fecha": "2024-01-01", "cantidad": 80 }
    ]
  }
}</pre></div>
                </div>
            </div>
        </div>
    </section>
</div>
