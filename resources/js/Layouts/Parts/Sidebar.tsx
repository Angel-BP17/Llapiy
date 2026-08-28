import React, { useState } from 'react';
import { Link } from '@inertiajs/react';
import { NavSection } from '@/Config/Navigation';
import { toUrl } from '@/lib/utils';
import { profile } from '@/routes';
import { usePermissions } from '@/hooks/use-permissions';
import { ChevronDown } from 'lucide-react';

interface SidebarProps {
  sections: NavSection[];
  userName?: string;
  userRole?: string;
  isOpen: boolean;
  setIsOpen?: (val: boolean) => void;
}

export default function Sidebar({ sections, isOpen, setIsOpen }: SidebarProps) {
  const { can, user, auth } = usePermissions();
  const [openSubmenus, setOpenSubmenus] = useState<Record<string, boolean>>({});

  const toggleSubmenu = (label: string, hasChildren: boolean) => {
    if (!isOpen && hasChildren && setIsOpen) {
      setIsOpen(true);
      setOpenSubmenus(prev => ({ ...prev, [label]: true }));
      return;
    }
    setOpenSubmenus(prev => ({ ...prev, [label]: !prev[label] }));
  };

  const getUserDescription = () => {
    if (user?.group_id) {
      return user.subgroup_id ? user.subgroup_descripcion : user.group_descripcion;
    }
    return auth?.roles?.[0]?.name || auth?.roles?.[0] || 'Usuario';
  };

  return (
    <aside
      id="dashboard-sidebar"
      className={`fixed inset-y-0 left-0 z-40 flex flex-col border-r border-border bg-sidebar text-sidebar-foreground shadow-lg transition-all duration-300 ${
        isOpen ? 'w-72 translate-x-0' : '-translate-x-full lg:translate-x-0 lg:w-20'
      }`}
    >
      {/* User Profile Section */}
      <div className={`border-b border-border py-5 transition-all duration-300 ${isOpen ? 'px-6' : 'px-0 flex justify-center'}`}>
        <Link href={profile.url()} className="flex items-center gap-3 transition-opacity hover:opacity-80 group overflow-hidden">
          <div className="flex h-12 w-12 shrink-0 items-center justify-center rounded-full overflow-hidden border border-border group-hover:ring-4 group-hover:ring-primary/5 transition-all shadow-sm">
            {user?.foto_perfil ? (
              <img src={`/storage/${user.foto_perfil}`} alt="P" className="h-full w-full object-cover" />
            ) : (
              <img src="/img/default-avatar.png" alt="P" className="h-full w-full object-cover" />
            )}
          </div>
          <div className={`min-w-0 flex-1 transition-all duration-300 ${isOpen ? 'opacity-100 visible' : 'opacity-0 invisible w-0'}`}>
            <p className="text-sm font-semibold text-foreground truncate">
              {user ? `${user.name} ${user.last_name}` : '...'}
            </p>
            <p className="text-[10px] uppercase font-black tracking-wider text-muted-foreground truncate">
              {getUserDescription()}
            </p>
          </div>
        </Link>
      </div>

      {/* Navigation Links */}
      <nav className="flex-1 overflow-y-auto py-6 scrollbar-hide">
        {sections.map((section, idx) => {
          const visibleItems = section.items.filter(item => can(item.permission));
          if (visibleItems.length === 0) return null;

          return (
            <div key={idx} className="mb-6 px-3">
              <p className={`text-[10px] font-black uppercase tracking-[0.2em] text-muted-foreground/50 mb-4 px-4 transition-opacity duration-300 ${isOpen ? 'opacity-100' : 'opacity-0 h-0 overflow-hidden'}`}>
                {section.title}
              </p>
              <ul className="space-y-2">
                {visibleItems.map((item, itemIdx) => {
                  const hasChildren = item.children && item.children.length > 0;
                  const isSubmenuOpen = openSubmenus[item.label] || item.active;

                  const itemClasses = `group flex items-center h-11 w-full rounded-xl transition-all duration-300 relative overflow-hidden ${
                    item.active 
                      ? 'bg-primary text-primary-foreground shadow-lg shadow-primary/25' 
                      : 'text-muted-foreground hover:bg-muted hover:text-foreground'
                  }`;

                  return (
                    <li key={itemIdx}>
                      {hasChildren ? (
                        <div className="flex flex-col">
                          <button type="button" onClick={() => toggleSubmenu(item.label, true)} className={itemClasses}>
                            <div className="flex-none w-[54px] flex items-center justify-center">
                              <span className="shrink-0 transition-transform duration-300 group-hover:scale-110" dangerouslySetInnerHTML={{ __html: item.icon }} />
                            </div>
                            <div className={`flex flex-1 items-center justify-between pr-4 transition-all duration-300 ${isOpen ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-4 pointer-events-none'}`}>
                              <span className="text-sm font-bold truncate">{item.label}</span>
                              <ChevronDown className={`h-4 w-4 transition-transform duration-300 ${isSubmenuOpen ? 'rotate-180' : ''}`} />
                            </div>
                          </button>
                          
                          <div className={`grid transition-all duration-300 ease-in-out ${isOpen && isSubmenuOpen ? 'grid-rows-[1fr] opacity-100 mt-1' : 'grid-rows-[0fr] opacity-0 overflow-hidden'}`}>
                            <ul className="overflow-hidden space-y-1 pl-12">
                              {item.children?.filter(child => can(child.permission)).map((child, childIdx) => (
                                <li key={childIdx}>
                                  <Link href={toUrl(child.href)} className={`block rounded-lg py-2 text-xs font-bold transition-colors ${child.active ? 'text-primary' : 'text-muted-foreground hover:text-foreground'}`}>{child.label}</Link>
                                </li>
                              ))}
                            </ul>
                          </div>
                        </div>
                      ) : (
                        <Link href={toUrl(item.href || '#')} className={itemClasses}>
                          <div className="flex-none w-[54px] flex items-center justify-center">
                            <span className="shrink-0 transition-transform duration-300 group-hover:scale-110" dangerouslySetInnerHTML={{ __html: item.icon }} />
                          </div>
                          <div className={`transition-all duration-300 ${isOpen ? 'opacity-100 translate-x-0 ml-0' : 'opacity-0 -translate-x-4 pointer-events-none'}`}>
                            <span className="text-sm font-bold truncate">{item.label}</span>
                          </div>
                        </Link>
                      )}
                    </li>
                  );
                })}
              </ul>
            </div>
          );
        })}
      </nav>
    </aside>
  );
}
