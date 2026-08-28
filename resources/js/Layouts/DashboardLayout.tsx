import React, { useState, useEffect } from 'react';
import { Head, router, usePage } from '@inertiajs/react';
import Sidebar from './Parts/Sidebar';
import Navbar from './Parts/Navbar';
import { defaultSections } from '@/Config/Navigation';
import { useCurrentUrl } from '@/hooks/use-current-url';
import { usePermissions } from '@/hooks/use-permissions';

interface DashboardLayoutProps {
  children: React.ReactNode;
  title?: string;
  brand?: string;
}

export default function DashboardLayout({ children, title = "Inicio", brand = "Llapiy" }: DashboardLayoutProps) {
  const { auth, user } = usePermissions();
  const { isCurrentUrl, isCurrentOrParentUrl } = useCurrentUrl();
  const { props } = usePage();
  const theme = (props.auth as any)?.theme || 'light';

  useEffect(() => {
    if (theme === 'dark') {
      document.documentElement.classList.add('dark');
    } else {
      document.documentElement.classList.remove('dark');
    }
  }, [theme]);
  
  // Inicializar basado en el ancho de la ventana (servidor/SSR seguro con 1024 como fallback)
  const [isSidebarOpen, setIsSidebarOpen] = useState(
    typeof window !== 'undefined' ? window.innerWidth >= 1024 : true
  );

  // Asegurar el estado correcto al montar el componente
  useEffect(() => {
    if (window.innerWidth < 1024) {
      setIsSidebarOpen(false);
    }
  }, []);

  // Cerrar sidebar automáticamente al navegar en móvil
  useEffect(() => {
    const unbind = router.on('finish', () => {
      if (window.innerWidth < 1024) { // Breakpoint 'lg' de Tailwind
        setIsSidebarOpen(false);
      }
    });

    return () => unbind();
  }, []);

  const markItemState = (item: any): any => {
    const children = item.children?.map(markItemState) ?? null;
    
    // Un item está activo si su URL coincide o si alguno de sus hijos está activo
    const selfActive = item.href ? isCurrentUrl(item.href) : false;
    
    // También revisamos los match adicionales si existen
    const matchActive = Array.isArray(item.match) &&
        item.match.some((matcher: string) => isCurrentOrParentUrl(matcher));

    const childActive = children ? children.some((child: any) => child.active) : false;
    const active = Boolean(selfActive || matchActive || childActive);

    return {
      ...item,
      children,
      active,
      open: item.open ?? childActive,
    };
  };

  const navSections = defaultSections.map((section) => ({
    ...section,
    items: section.items.map(markItemState),
  }));

  const toggleSidebar = () => setIsSidebarOpen(!isSidebarOpen);

  return (
    <div className="bg-app-bg text-foreground min-h-screen">
      <Head title={title} />
      
      <div id="app-shell" className="relative min-h-screen flex flex-col">
        <Sidebar 
          sections={navSections} 
          isOpen={isSidebarOpen}
          setIsOpen={setIsSidebarOpen}
        />

        <div className={`flex min-h-screen flex-col transition-all duration-300 ${
          isSidebarOpen ? 'lg:pl-72' : 'lg:pl-20'
        }`}>
          <Navbar brand={brand} onToggleSidebar={toggleSidebar} />

          <main className="flex-1 px-4 pb-12 pt-6 sm:px-6 lg:px-8">
            <div className="mx-auto w-full max-w-6xl">
              {children}
            </div>
          </main>

          <footer className="border-t border-border bg-background px-4 py-4 text-xs text-muted-foreground sm:px-6 lg:px-8">
            UGEL Santa &copy; {new Date().getFullYear()}
          </footer>
        </div>

        {/* Overlay para móvil */}
        {isSidebarOpen && (
          <div 
            className="fixed inset-0 z-30 bg-black/50 backdrop-blur-sm lg:hidden"
            onClick={toggleSidebar}
          ></div>
        )}
      </div>
    </div>
  );
}
