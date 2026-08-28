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

describe('Areas Index Page', () => {
  const mockProps = {
    areas: [
      { id: 1, descripcion: 'Area 1', abreviacion: 'A1' },
      { id: 2, descripcion: 'Area 2', abreviacion: 'A2' },
    ],
    groups: [],
    subgroups: [],
    groupTypes: [],
    pagination: { total: 2, current_page: 1, last_page: 1, from: 1, to: 2 },
    filters: {},
  };

  beforeEach(() => {
    vi.clearAllMocks();
    (usePage as any).mockReturnValue({
      props: {
        auth: {
          user: { name: 'Admin' },
          roles: ['ADMINISTRADOR'],
          permissions: ['areas.view', 'areas.create', 'areas.update', 'areas.delete'],
        },
      },
    });
  });

  it('debe renderizar la lista de areas correctamente', () => {
    render(<Index {...mockProps} />);
    
    expect(screen.getByText('Áreas y Oficinas')).toBeInTheDocument();
    expect(screen.getByText('Area 1')).toBeInTheDocument();
    expect(screen.getByText('Area 2')).toBeInTheDocument();
  });

  it('debe abrir el modal de creacion al hacer clic en Nueva Área', () => {
    render(<Index {...mockProps} />);
    
    const newButton = screen.getByText('Nueva Área');
    fireEvent.click(newButton);
    
    expect(screen.getByText('Registrar Nueva Área')).toBeInTheDocument();
  });

  it('debe llamar a router.delete al intentar eliminar un area', () => {
    window.confirm = vi.fn().mockReturnValue(true);
    render(<Index {...mockProps} />);
    
    const deleteBtn = screen.getAllByTitle('Eliminar Área')[0];
    fireEvent.click(deleteBtn); 
    
    // El mock de Wayfinder en setup.ts devuelve un objeto con .url()
    expect(router.delete).toHaveBeenCalled();
  });
});
