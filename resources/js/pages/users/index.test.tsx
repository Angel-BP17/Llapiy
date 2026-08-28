import { render, screen, fireEvent } from '@testing-library/react';
import { describe, it, expect, vi, beforeEach } from 'vitest';
import Index from './index';
import React from 'react';
import { usePage, router } from '@inertiajs/react';

// Mock de Inertia
vi.mock('@inertiajs/react', async (importOriginal) => {
  const actual = await importOriginal<typeof import('@inertiajs/react')>();
  return {
    ...actual,
    usePage: vi.fn(),
    router: {
      get: vi.fn(),
      post: vi.fn(),
      put: vi.fn(),
      delete: vi.fn(),
    },
    Head: ({ children }: any) => <div data-testid="mock-head">{children}</div>,
    Link: ({ children, href, ...props }: any) => <a href={href} {...props}>{children}</a>,
  };
});

// Mock de layout
vi.mock('@/Layouts/DashboardLayout', () => ({
  default: ({ children, title }: any) => (
    <div data-testid="layout">
      <h1>{title}</h1>
      {children}
    </div>
  ),
}));

describe('Users Index Page', () => {
  const mockProps = {
    users: [
      { id: 1, name: 'JUAN', last_name: 'PEREZ', user_name: 'JUANP', email: 'juan@test.com', roles: ['OPERADOR'], area_id: 1, group_id: 1, subgroup_id: 1, dni: '12345678', foto_perfil: null, group_type_id: 1 },
    ],
    areas: [],
    roles: [{ id: 1, name: 'ADMINISTRADOR' }, { id: 2, name: 'OPERADOR' }],
    stats: { totalUsers: 1, totalRoles: 2, totalAreas: 0 },
    pagination: { total: 1, current_page: 1, last_page: 1, from: 1 },
    filters: { search: '' },
  };

  beforeEach(() => {
    vi.clearAllMocks();
    (usePage as any).mockReturnValue({
      props: {
        auth: {
          user: { name: 'Admin' },
          roles: ['ADMINISTRADOR'],
          permissions: ['users.view', 'users.create', 'users.update', 'users.delete'],
        },
      },
    });
  });

  it('debe renderizar la lista de usuarios correctamente', () => {
    render(<Index {...mockProps} />);
    expect(screen.getAllByText('Gestión de Usuarios').length).toBeGreaterThan(0);
    expect(screen.getByText('JUAN')).toBeInTheDocument();
    expect(screen.getByText('PEREZ')).toBeInTheDocument();
  });

  it('debe abrir el modal de creacion', () => {
    render(<Index {...mockProps} />);
    fireEvent.click(screen.getByText('Nuevo Usuario'));
    expect(screen.getAllByText('Registrar Nuevo Colaborador').length).toBeGreaterThan(0); // Titulo del modal
  });

  it('debe filtrar al buscar', () => {
    render(<Index {...mockProps} />);
    const input = screen.getByPlaceholderText('Buscar por nombre, email o DNI...');
    fireEvent.change(input, { target: { value: 'Juan' } });
    fireEvent.click(screen.getByText('Filtrar'));
    
    expect(router.get).toHaveBeenCalledWith(expect.any(String), { search: 'Juan' }, expect.anything());
  });
});
