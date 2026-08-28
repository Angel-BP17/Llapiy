import React from 'react';
import { router } from '@inertiajs/react';
import NotificationsDropdown from './NotificationsDropdown';
import { logout } from '@/routes';

interface NavbarProps {
  brand?: string;
  onToggleSidebar: () => void;
}

export default function Navbar({ brand = "Llapiy", onToggleSidebar }: NavbarProps) {
  const handleLogout = () => {
    router.post(logout.url());
  };

  return (
    <header className="sticky top-0 z-30 flex h-16 items-center justify-between border-b border-border bg-background/95 px-4 backdrop-blur md:px-6">
      <div className="flex items-center gap-3">
        <button
          type="button"
          onClick={onToggleSidebar}
          className="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-border bg-card text-foreground shadow-sm transition hover:bg-accent"
        >
          <svg viewBox="0 0 24 24" className="h-5 w-5" aria-hidden="true">
            <path fill="currentColor" d="M4 6h16v2H4V6Zm0 5h16v2H4v-2Zm0 5h10v2H4v-2Z" />
          </svg>
        </button>
        <div className="flex items-center gap-2">
          <div className="flex h-9 w-9 items-center justify-center rounded-lg bg-primary text-primary-foreground">
            <span className="text-sm font-semibold">L</span>
          </div>
          <span className="text-base font-semibold text-foreground">{brand}</span>
        </div>
      </div>

      <div className="flex items-center gap-3">
        <NotificationsDropdown />
        <button
          type="button"
          className="inline-flex items-center gap-2 rounded-lg border border-border bg-card px-4 py-2 text-sm font-semibold text-foreground shadow-sm transition hover:bg-accent"
          onClick={handleLogout}
        >
          Salir
          <svg viewBox="0 0 24 24" className="h-4 w-4" aria-hidden="true">
            <path fill="currentColor" d="M16 17v-2h-4v-2h4V11l3 3-3 3ZM5 4h7a2 2 0 0 1 2 2v3h-2V6H5v12h7v-3h2v3a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z" />
          </svg>
        </button>
      </div>
    </header>
  );
}
