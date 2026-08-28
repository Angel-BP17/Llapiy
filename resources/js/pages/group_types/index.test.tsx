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

describe('GroupTypes Index Page', () => {
  const mockProps = {
    groupTypes: {
      data: [
        { id: 1, descripcion: 'Tipo 1', abreviacion: 'T1', groups_count: 0 },
        { id: 2, descripcion: 'Tipo 2', abreviacion: 'T2', groups_count: 5 },
      ],
      total: 2,
      current_page: 1,
      last_page: 1,
      from: 1,
    },
    filters: { search: '' },
  };

  beforeEach(() => {
    vi.clearAllMocks();
    (usePage as any).mockReturnValue({
      props: {
        auth: {
          user: { name: 'Admin' },
          roles: ['ADMINISTRADOR'],
          permissions: ['group-types.view', 'group-types.create', 'group-types.delete'],
        },
      },
    });
  });

  it('debe renderizar la lista de tipos de grupos correctamente', () => {
    render(<Index {...mockProps} />);
    
    expect(screen.getByText('Gestión de tipos de grupos')).toBeInTheDocument();
    expect(screen.getByText('Tipo 1')).toBeInTheDocument();
    expect(screen.getByText('Tipo 2')).toBeInTheDocument();
  });

  it('debe abrir el modal de creacion al hacer clic en Nuevo Tipo de Grupo', () => {
    render(<Index {...mockProps} />);
    
    const newButton = screen.getByText('Nuevo Tipo de Grupo');
    fireEvent.click(newButton);
    
    expect(screen.getByText('Crear Tipo de Grupo')).toBeInTheDocument();
  });

  it('debe filtrar al escribir en el buscador y dar clic en filtrar', () => {
    render(<Index {...mockProps} />);
    
    const searchInput = screen.getByPlaceholderText('Descripción o abreviación...');
    fireEvent.change(searchInput, { target: { value: 'Búsqueda' } });
    
    const filterBtn = screen.getByText('Filtrar');
    fireEvent.click(filterBtn);
    
    expect(router.get).toHaveBeenCalledWith(expect.any(String), { search: 'Búsqueda' }, expect.anything());
  });
});
