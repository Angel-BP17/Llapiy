import { render, screen, fireEvent } from '@testing-library/react';
import { describe, it, expect, vi, beforeEach } from 'vitest';
import Sidebar from './Sidebar';
import { defaultSections } from '@/Config/Navigation';
import React from 'react';
import { usePage } from '@inertiajs/react';

// Mock de usePage
vi.mock('@inertiajs/react', async (importOriginal) => {
  const actual = await importOriginal<typeof import('@inertiajs/react')>();
  return {
    ...actual,
    usePage: vi.fn(),
    Link: ({ children, href }: any) => <a href={href}>{children}</a>,
  };
});

// Mock de toUrl y routes
vi.mock('@/lib/utils', () => ({
  toUrl: (url: any) => typeof url === 'string' ? url : url.url,
  cn: (...args: any[]) => args.filter(Boolean).join(' '),
}));

describe('Sidebar Component - Etapas de Cobertura', () => {
  const mockProps = {
    sections: defaultSections,
    userName: 'Test User',
    userRole: 'Administrador',
    isOpen: true,
  };

  const setPermissions = (perms: string[]) => {
    (usePage as any).mockReturnValue({
      props: {
        auth: {
          user: { name: 'Test' },
          permissions: perms,
          roles: perms.includes('ADMIN') ? ['ADMINISTRADOR'] : ['OPERADOR']
        }
      }
    });
  };

  beforeEach(() => {
    vi.clearAllMocks();
  });

  // ETAPA 1: CONTRATO Y RENDERIZADO
  it('ETAPA 1: debe renderizar la estructura base y el perfil del usuario', () => {
    setPermissions(['users.view']);
    render(<Sidebar {...mockProps} />);
    expect(screen.getByText('Test User')).toBeInTheDocument();
    expect(screen.getByText('Administrador')).toBeInTheDocument();
    expect(screen.getByRole('navigation')).toBeInTheDocument();
  });

  // ETAPA 2: INTEGRIDAD DE RUTAS
  it('ETAPA 2: debe generar URLs dinámicas correctas mediante Wayfinder', () => {
    setPermissions(['users.view', 'dashboard.view']);
    render(<Sidebar {...mockProps} />);
    
    // Verificamos que el link de Usuarios apunte a la ruta espejo del backend
    const usersLink = screen.getByText('Usuarios').closest('a');
    expect(usersLink).toHaveAttribute('href', '/usuarios');
  });

  // ETAPA 3: SEGURIDAD Y VISIBILIDAD (RBAC)
  it('ETAPA 3: debe ocultar módulos si el usuario no tiene permisos', () => {
    // Solo damos permiso de Dashboard, no de Usuarios ni Roles
    setPermissions(['dashboard.view']);
    render(<Sidebar {...mockProps} />);
    
    expect(screen.queryByText('Usuarios')).not.toBeInTheDocument();
    expect(screen.queryByText('Roles')).not.toBeInTheDocument();
    expect(screen.getByText('Home')).toBeInTheDocument();
  });

  it('ETAPA 3: debe mostrar todo si el usuario es ADMINISTRADOR', () => {
    setPermissions(['ADMIN']); // Nuestro mock interpreta esto como rol ADMIN
    render(<Sidebar {...mockProps} />);
    
    expect(screen.getByText('Usuarios')).toBeInTheDocument();
    expect(screen.getByText('Roles')).toBeInTheDocument();
    expect(screen.getByText('ADMINISTRACION')).toBeInTheDocument();
  });

  // ETAPA 4: INTERACCIÓN Y LÓGICA
  it('ETAPA 4: debe manejar el colapso visual correctamente', () => {
    const { container } = render(<Sidebar {...mockProps} isOpen={false} />);
    const aside = container.querySelector('aside');
    expect(aside).toHaveClass('-translate-x-full');
  });

  // ETAPA 5: RESILIENCIA
  it('ETAPA 5: debe manejar secciones vacías o nulas sin romperse', () => {
    render(<Sidebar {...mockProps} sections={[]} />);
    expect(screen.queryByRole('listitem')).not.toBeInTheDocument();
  });
});
