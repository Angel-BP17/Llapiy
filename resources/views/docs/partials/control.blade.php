<!-- Control Sections -->
<div class="space-y-24 sm:space-y-32">
    <!-- Users Section -->
    <section id="mod-users" class="scroll-mt-32">
        <div class="flex items-center gap-6 mb-12 sm:mb-16">
            <div class="w-14 h-14 rounded-2xl bg-blue-500/10 dark:bg-blue-500/20 flex items-center justify-center text-blue-600 dark:text-blue-400 shadow-sm ring-1 ring-blue-500/20">
                <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
            <div>
                <h2 class="text-3xl sm:text-4xl font-black tracking-tight doc-title">Usuarios & Roles</h2>
                <p class="text-sm font-medium text-slate-400 dark:text-slate-500 mt-1">Administración de identidades.</p>
            </div>
            <div class="h-px flex-1 bg-gradient-to-r from-blue-100 dark:from-slate-800 to-transparent transition-colors"></div>
        </div>

        <div class="endpoint-card post-bg">
            <div class="px-6 py-5 sm:px-10 sm:py-8 flex items-center justify-between border-b border-indigo-100/30 dark:border-slate-800/50 bg-white/40 dark:bg-slate-900/40 backdrop-blur-sm transition-colors">
                <div class="flex items-center gap-4">
                    <span class="method-label bg-blue-500">POST</span>
                    <code class="text-sm sm:text-lg font-bold text-slate-800 dark:text-indigo-300">/api/users</code>
                </div>
            </div>
            <div class="p-6 sm:p-10 grid grid-cols-1 xl:grid-cols-2 gap-10">
                <div>
                    <h4 class="text-xs font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-[0.2em] mb-4">Integridad de Datos</h4>
                    <p class="text-base doc-text mb-6">Crea un usuario vinculado a un grupo organizacional. Soporta carga de foto de perfil vía <code class="text-blue-500">FormData</code>.</p>
                </div>
                <div class="json-card border-blue-500/20">
                    <div class="json-header !border-blue-500/10"><span class="json-label">Required Schema</span></div>
                    <div class="json-body">
<pre>{
  "name": "Angel",
  "last_name": "Soto",
  "email": "angel@example.com",
  "dni": "12345678",
  "password": "...",
  "group_id": 1,
  "roles": ["OPERADOR"]
}</pre>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Roles & Permissions -->
    <section id="mod-roles" class="scroll-mt-32">
        <div class="flex items-center gap-6 mb-12">
            <div class="w-14 h-14 rounded-2xl bg-violet-500/10 dark:bg-violet-500/20 flex items-center justify-center text-violet-600 dark:text-violet-400 shadow-sm ring-1 ring-violet-500/20">
                <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <div>
                <h2 class="text-3xl font-black tracking-tight doc-title">Roles & Permisos</h2>
                <p class="text-sm font-medium text-slate-400 mt-1">Control de acceso basado en roles (RBAC).</p>
            </div>
        </div>

        <div class="endpoint-card post-bg">
            <div class="px-6 py-5 sm:px-10 sm:py-8 flex items-center justify-between border-b border-indigo-100/30 dark:border-slate-800/50 bg-white/40 dark:bg-slate-900/40 backdrop-blur-sm">
                <div class="flex items-center gap-4">
                    <span class="method-label bg-blue-500">POST</span>
                    <code class="text-sm sm:text-lg font-bold text-slate-800 dark:text-indigo-300">/api/roles</code>
                </div>
            </div>
            <div class="p-6 sm:p-10 grid grid-cols-1 xl:grid-cols-2 gap-10">
                <div>
                    <h4 class="text-xs font-black text-indigo-600 uppercase tracking-widest mb-4">Configuración de Seguridad</h4>
                    <p class="text-sm doc-text leading-relaxed">Crea un rol asignando una lista de permisos técnicos. El sistema resolverá automáticamente el <code class="text-indigo-500">guard_name</code>.</p>
                </div>
                <div class="json-card border-blue-500/20">
                    <div class="json-header !border-blue-500/10"><span class="json-label">Role Definition</span></div>
                    <div class="json-body">
<pre>{
  "name": "GESTOR_ARCHIVO",
  "permissions": ["documents.view.group", "blocks.upload"]
}</pre>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Inbox -->
    <section id="mod-inbox" class="scroll-mt-32">
        <div class="flex items-center gap-6 mb-12 sm:mb-16">
            <div class="w-14 h-14 rounded-2xl bg-indigo-500/10 dark:bg-indigo-500/20 flex items-center justify-center text-indigo-600 dark:text-indigo-400 shadow-sm ring-1 ring-indigo-500/20">
                <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
            </div>
            <div>
                <h2 class="text-3xl sm:text-4xl font-black tracking-tight doc-title">Bandeja (Inbox)</h2>
                <p class="text-sm font-medium text-slate-400 dark:text-slate-500 mt-1">Clasificación de bloques sin ubicación física.</p>
            </div>
            <div class="h-px flex-1 bg-gradient-to-r from-indigo-100 dark:from-slate-800 to-transparent transition-colors"></div>
        </div>
        <div class="endpoint-card get-bg">
            <div class="px-6 py-5 sm:px-10 sm:py-8 border-b border-indigo-100/30 dark:border-slate-800/50 bg-white/40 dark:bg-slate-900/40 backdrop-blur-sm transition-colors">
                <div class="flex items-center gap-4">
                    <span class="method-label bg-emerald-500">GET</span>
                    <code class="text-sm sm:text-lg font-bold text-slate-800 dark:text-indigo-300">/api/inbox</code>
                </div>
            </div>
            <div class="p-6 sm:p-10 grid grid-cols-1 xl:grid-cols-2 gap-10">
                <p class="text-base doc-text italic">Retorna bloques que requieren ser asignados a una Caja. Incluye catálogos de Secciones y Andamios para poblar los selectores del modal.</p>
                <div class="json-card border-emerald-500/20 shadow-xl">
                    <div class="json-header !border-emerald-500/10"><span class="json-label">Extended Data Schema</span></div>
                    <div class="json-body"><pre>{ "data": { "documents": {...}, "sections": [...], "boxes": [...] } }</pre></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Activity Log -->
    <section id="mod-activity" class="scroll-mt-32">
        <div class="flex items-center gap-6 mb-12 sm:mb-16">
            <div class="w-14 h-14 rounded-2xl bg-slate-500/10 dark:bg-slate-500/20 flex items-center justify-center text-slate-600 dark:text-slate-400 shadow-sm ring-1 ring-slate-500/20">
                <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <h2 class="text-3xl sm:text-4xl font-black tracking-tight doc-title">Bitácora</h2>
                <p class="text-sm font-medium text-slate-400 dark:text-slate-500 mt-1">Auditoría y trazabilidad.</p>
            </div>
            <div class="h-px flex-1 bg-gradient-to-r from-slate-100 dark:from-slate-800 to-transparent transition-colors"></div>
        </div>
        <div class="endpoint-card get-bg">
            <div class="px-6 py-5 sm:px-10 sm:py-8 border-b border-indigo-100/30 dark:border-slate-800/50 bg-white/40 dark:bg-slate-900/40 backdrop-blur-sm transition-colors">
                <div class="flex items-center gap-4">
                    <span class="method-label bg-emerald-500">GET</span>
                    <code class="text-sm sm:text-lg font-bold text-slate-800 dark:text-indigo-300">/api/activity-logs</code>
                </div>
            </div>
            <div class="p-6 sm:p-10">
                <div class="json-card border-slate-200/50 dark:border-slate-800">
                    <div class="json-header"><span class="json-label">Audit Log Schema</span></div>
                    <div class="json-body">
<pre>{
  "data": {
    "logs": { "data": [ { "id": 1, "action": "CREATED", "model": "Document" } ] },
    "users": [...],
    "modules": ["Document", "Block", "Area"]
  }
}</pre>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
