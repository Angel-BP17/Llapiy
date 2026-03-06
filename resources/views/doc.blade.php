<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LlapiyAPI - Documentación Profesional</title>
    @vite(['resources/css/app.css'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <style>
        :root {
            --primary: #4f46e5;
            --get: #10b981;
            --post: #3b82f6;
            --put: #f59e0b;
            --delete: #ef4444;
        }
        body { font-family: 'Inter', sans-serif; }
        code, pre { font-family: 'JetBrains Mono', monospace; }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { @apply bg-indigo-200 dark:bg-slate-800 rounded-full hover:bg-indigo-300 dark:hover:bg-slate-700; }

        /* Typography Contrast */
        .doc-title { @apply text-slate-900 dark:text-slate-50 transition-colors duration-300; }
        .doc-text { @apply text-slate-600 dark:text-slate-400 transition-colors duration-300; }

        /* Endpoint Cards - Definidos en Light Mode */
        .endpoint-card { 
            @apply bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl sm:rounded-3xl overflow-hidden 
                    shadow-[0_4px_20px_rgb(0,0,0,0.03)] dark:shadow-none backdrop-blur-sm
                    hover:border-indigo-300 dark:hover:border-indigo-500/50 transition-all duration-300 mb-8 sm:mb-12; 
        }
        
        /* Method Badges */
        .method-label { 
            @apply px-2.5 py-1 sm:px-3 sm:py-1.5 text-[9px] sm:text-[10px] font-black rounded-lg sm:rounded-xl uppercase tracking-widest text-white shadow-sm ring-1 ring-inset ring-black/5; 
        }
        
        .get-bg { @apply border-l-[4px] sm:border-l-[6px] border-emerald-500 bg-gradient-to-r from-emerald-50/60 to-transparent dark:from-emerald-500/5; }
        .post-bg { @apply border-l-[4px] sm:border-l-[6px] border-blue-500 bg-gradient-to-r from-blue-50/60 to-transparent dark:from-blue-500/5; }
        .put-bg { @apply border-l-[4px] sm:border-l-[6px] border-amber-500 bg-gradient-to-r from-amber-50/60 to-transparent dark:from-amber-500/5; }
        .delete-bg { @apply border-l-[4px] sm:border-l-[6px] border-rose-500 bg-gradient-to-r from-rose-50/60 to-transparent dark:from-rose-500/5; }

        /* JSON Code Cards - Terminal Style */
        .json-card { 
            @apply mt-8 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 shadow-2xl shadow-indigo-500/5 overflow-hidden transition-all duration-300; 
        }
        .json-header {
            @apply px-5 py-3 border-b border-slate-100 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-900/50 flex items-center justify-between;
        }
        .json-label { 
            @apply text-[10px] font-black uppercase tracking-[0.2em] flex items-center gap-2.5; 
        }
        .label-req { @apply text-blue-600 dark:text-blue-400; }
        .label-res { @apply text-emerald-600 dark:text-emerald-400; }
        
        .json-body {
            @apply p-3 sm:p-4 bg-slate-50/50 dark:bg-slate-900/20 overflow-hidden;
        }
        .json-body pre { 
            @apply p-5 sm:p-8 bg-slate-950 text-indigo-100 font-medium leading-relaxed text-xs sm:text-sm overflow-x-auto rounded-xl shadow-inner selection:bg-indigo-500/30; 
        }

        /* Sidebar Styling */
        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            font-weight: 600;
            border-radius: 1rem;
            transition: all 0.2s;
            border: 1px solid transparent;
            @apply text-slate-500 dark:text-slate-400;
        }
        .sidebar-link:hover {
            @apply text-indigo-600 bg-white border-slate-200 shadow-sm dark:text-indigo-300 dark:bg-indigo-500/10 dark:border-transparent dark:shadow-none;
        }
        .sidebar-icon {
            width: 20px !important;
            height: 20px !important;
            flex-shrink: 0;
            display: block;
            @apply text-slate-400 transition-colors;
        }
        .sidebar-link:hover .sidebar-icon {
            @apply text-indigo-500;
        }

        #mobile-menu-overlay {
            @apply fixed inset-0 bg-indigo-950/40 backdrop-blur-sm z-[60] opacity-0 pointer-events-none transition-opacity duration-300;
        }
        #mobile-menu-overlay.active { @apply opacity-100 pointer-events-auto; }
    </style>
</head>
<body class="bg-slate-50 dark:bg-[#0b0f1a] text-slate-900 dark:text-slate-100 antialiased transition-colors duration-300">
    <div id="mobile-menu-overlay"></div>
    <div class="min-h-screen flex flex-col">
        
        <!-- Header -->
        <header class="sticky top-0 z-50 w-full backdrop-blur-xl bg-white/80 dark:bg-[#0b0f1a]/70 border-b border-slate-200 dark:border-slate-800/50 shadow-sm">
            <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 h-16 sm:h-20 flex items-center justify-between">
                <div class="flex items-center gap-3 sm:gap-4">
                    <button id="mobile-menu-toggle" class="lg:hidden p-2 rounded-xl bg-white dark:bg-slate-900 text-slate-600 dark:text-indigo-400 border border-slate-200 dark:border-slate-800">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                    <div class="flex items-center gap-2 sm:gap-3">
                        <div class="w-9 h-9 sm:w-11 sm:h-11 bg-gradient-to-br from-indigo-600 via-indigo-500 to-violet-600 rounded-xl sm:rounded-2xl flex items-center justify-center text-white font-black shadow-lg">L</div>
                        <div class="hidden xs:block">
                            <span class="text-lg sm:text-xl font-black tracking-tight text-slate-900 dark:text-white block leading-none">Llapiy<span class="text-indigo-600">API</span></span>
                            <span class="text-[9px] sm:text-[10px] font-bold text-slate-400 dark:text-indigo-500 uppercase tracking-[0.2em]">Documentation Hub</span>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center gap-3 sm:gap-4">
                    <div class="hidden md:flex items-center px-4 py-1.5 bg-white dark:bg-slate-900 rounded-full border border-slate-200 dark:border-slate-800 shadow-sm text-[11px] font-bold text-slate-600 dark:text-slate-400">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse mr-2"></span>v1.0.0 Stable
                    </div>
                    <button id="theme-toggle" class="p-2 sm:p-3 rounded-xl sm:rounded-2xl bg-white dark:bg-slate-900 text-slate-500 border border-slate-200 dark:border-slate-800 shadow-sm hover:scale-105 transition-transform">
                        <svg id="theme-toggle-dark-icon" class="hidden w-4 h-4 sm:w-5 sm:h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path></svg>
                        <svg id="theme-toggle-light-icon" class="hidden w-4 h-4 sm:w-5 sm:h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 14.95a1 1 0 01-1.414 0l-.707-.707a1 1 0 011.414-1.414l.707.707a1 1 0 010 1.414zm2.12-10.607a1 1 0 011.414 0l.707.707a1 1 0 11-1.414 1.414l-.707-.707a1 1 0 010-1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"></path></svg>
                    </button>
                </div>
            </div>
        </header>

        <div class="flex-1 max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
            <div class="lg:flex lg:gap-12 xl:gap-16">
                
                <!-- Sidebar -->
                <aside id="sidebar" class="fixed inset-y-0 left-0 z-[70] w-72 bg-white dark:bg-[#0b0f1a] transform -translate-x-full transition-transform duration-300 lg:translate-x-0 lg:static lg:block lg:w-64 xl:w-72 lg:shrink-0 lg:sticky lg:top-20 lg:h-[calc(100vh-5rem)] overflow-y-auto border-r border-slate-200 dark:border-slate-800/50 shadow-2xl lg:shadow-none">
                    <div class="flex lg:hidden items-center justify-between p-6 border-b border-slate-200 dark:border-slate-800 mb-6">
                        <span class="font-black text-indigo-600 uppercase tracking-widest text-xs">Menú Principal</span>
                        <button id="mobile-menu-close" class="p-2 rounded-xl bg-slate-100 dark:bg-slate-900 text-slate-600">
                            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    <nav class="space-y-10 px-6 lg:pl-0 lg:pr-6 xl:pr-8 lg:pt-4">
                        @php
                            $menu = [
                                'Core Services' => [
                                    '#mod-auth' => ['Autenticación', '<path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>'],
                                    '#mod-profile' => ['Perfil & Sesión', '<path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>'],
                                    '#mod-dashboard' => ['Métricas Globales', '<path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>']
                                ],
                                'Contenido' => [
                                    '#mod-docs' => ['Documentos', '<path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>'],
                                    '#mod-blocks' => ['Bloques Físicos', '<path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>']
                                ],
                                'Estructura' => [
                                    '#mod-areas' => ['Organización', '<path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>']
                                ],
                                'Gestión' => [
                                    '#mod-users' => ['Usuarios', '<path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>'],
                                    '#mod-inbox' => ['Bandeja', '<path d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>'],
                                    '#mod-activity' => ['Bitácora', '<path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>']
                                ],
                                'Control Central' => [
                                    '#mod-system' => ['Mantenimiento', '<path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>']
                                ]
                            ];
                        @endphp
                        @foreach($menu as $title => $links)
                        <div class="mb-10">
                            <h5 class="mb-4 text-[10px] font-black tracking-[0.3em] text-indigo-400 dark:text-slate-600 uppercase leading-none">{{ $title }}</h5>
                            <ul class="space-y-1">
                                @foreach($links as $href => $data)
                                <li>
                                    <a href="{{ $href }}" class="sidebar-link group">
                                        <svg class="sidebar-icon" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            {!! $data[1] !!}
                                        </svg>
                                        {{ $data[0] }}
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endforeach
                    </nav>
                </aside>

                <!-- Main -->
                <main class="flex-1 pt-8 sm:pt-12 pb-16 sm:pb-24 min-w-0">
                    <section id="intro" class="mb-12 sm:mb-20 scroll-mt-32 relative">
                        <div class="absolute -top-12 sm:-top-24 -left-12 sm:-left-24 w-64 sm:w-96 h-64 sm:h-96 bg-indigo-500/10 dark:bg-indigo-500/5 rounded-full blur-[80px] sm:blur-[120px] -z-10"></div>
                        <span class="inline-flex items-center px-3 py-1 sm:px-4 sm:py-1.5 rounded-full bg-emerald-50 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 text-[10px] sm:text-xs font-bold tracking-widest uppercase mb-4 sm:mb-6 ring-1 ring-emerald-200 dark:ring-emerald-800">
                            API Certificada - 8 Etapas de Calidad
                        </span>
                        <h1 class="text-4xl sm:text-5xl md:text-6xl xl:text-7xl font-black tracking-tight doc-title mb-6 sm:mb-8 leading-[1.1] sm:leading-[0.95]">
                            High Performance<br class="hidden sm:block"><span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 via-violet-600 to-indigo-600">Document Engine</span>
                        </h1>
                        <p class="text-base sm:text-lg md:text-xl xl:text-2xl doc-text leading-relaxed max-w-3xl font-medium">
                            Infraestructura documental optimizada para alta concurrencia. Implementa un contrato estricto de respuesta <code class="text-indigo-500">{message, data}</code>, seguridad RBAC de tres capas y carga asíncrona de metadatos.
                        </p>
                    </section>

                    <div class="space-y-16 sm:space-y-24 md:space-y-32">
                        @include('docs.partials.core')
                        @include('docs.partials.documents')
                        @include('docs.partials.organization')
                        @include('docs.partials.control')
                        @include('docs.partials.system')
                    </div>
                </main>
            </div>
        </div>

        <footer class="bg-slate-950 text-white py-12 sm:py-20 border-t border-slate-900 mt-auto">
            <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row justify-between items-center gap-10 sm:gap-12">
                    <div class="flex items-center gap-3 sm:gap-4">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-indigo-600 to-violet-600 rounded-xl sm:rounded-2xl flex items-center justify-center shadow-xl shadow-indigo-500/20 text-white font-black">L</div>
                        <div class="text-left">
                            <span class="text-xl sm:text-2xl font-black">LlapiyAPI</span>
                            <p class="text-[10px] sm:text-xs text-slate-500 font-bold tracking-widest uppercase">The Document Engine</p>
                        </div>
                    </div>
                    <div class="text-center md:text-right">
                        <p class="text-sm sm:text-base text-slate-400 font-medium mb-2">Construido con pasión para desarrolladores modernos.</p>
                        <div class="text-[9px] sm:text-[10px] text-slate-600 uppercase tracking-[0.4em] font-black">
                            &copy; {{ date('Y') }} LlapiyAPI Hub • All Rights Reserved
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <script>
        // Theme Toggle
        const themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
        const themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');
        const themeToggleBtn = document.getElementById('theme-toggle');

        if (document.documentElement.classList.contains('dark')) {
            themeToggleLightIcon.classList.remove('hidden');
        } else {
            themeToggleDarkIcon.classList.remove('hidden');
        }

        themeToggleBtn.addEventListener('click', function() {
            themeToggleDarkIcon.classList.toggle('hidden');
            themeToggleLightIcon.classList.toggle('hidden');

            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            } else {
                document.documentElement.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            }
        });

        // Mobile Menu Toggle
        const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
        const mobileMenuClose = document.getElementById('mobile-menu-close');
        const mobileMenuOverlay = document.getElementById('mobile-menu-overlay');
        const sidebar = document.getElementById('sidebar');
        const sidebarLinks = document.querySelectorAll('.sidebar-link');

        function toggleMenu() {
            sidebar.classList.toggle('-translate-x-full');
            mobileMenuOverlay.classList.toggle('active');
            document.body.classList.toggle('overflow-hidden');
        }

        if(mobileMenuToggle) mobileMenuToggle.addEventListener('click', toggleMenu);
        if(mobileMenuClose) mobileMenuClose.addEventListener('click', toggleMenu);
        if(mobileMenuOverlay) mobileMenuOverlay.addEventListener('click', toggleMenu);

        sidebarLinks.forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 1024) {
                    toggleMenu();
                }
            });
        });
    </script>
</body>
</html>
