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

describe('Campos Index Page', () => {
  const mockProps = {
    campos: {
      data: [
        { id: 1, name: 'Campo 1', data_type: 'string', is_nullable: true, document_types_count: 0 },
        { id: 2, name: 'Campo 2', data_type: 'int', is_nullable: false, document_types_count: 1 },
      ],
      total: 2,
      current_page: 1,
      last_page: 1,
      from: 1,
    },
    totalDocumentTypes: 5,
    filters: { search: '' },
    pagination: {},
  };

  beforeEach(() => {
    vi.clearAllMocks();
    (usePage as any).mockReturnValue({
      props: {
        auth: {
          user: { name: 'Admin' },
          roles: ['ADMINISTRADOR'],
          permissions: ['campos.view', 'campos.create', 'campos.delete'],
        },
      },
    });
  });

  it('debe renderizar la lista de campos y las estadisticas', () => {
    render(<Index {...mockProps} />);
    
    expect(screen.getAllByText('Campos de Metadatos').length).toBeGreaterThan(0);
    expect(screen.getByText('Campo 1')).toBeInTheDocument();
    expect(screen.getByText('Campo 2')).toBeInTheDocument();
    expect(screen.getByText('Plantillas asociadas')).toBeInTheDocument();
    expect(screen.getByText('5')).toBeInTheDocument(); // totalDocumentTypes
  });

  it('debe abrir el modal de creacion al hacer clic en Nuevo Campo', () => {
    render(<Index {...mockProps} />);
    
    const newButton = screen.getByText('Nuevo Campo');
    fireEvent.click(newButton);
    
    expect(screen.getByText('Crear Nuevo Campo')).toBeInTheDocument();
  });

  it('debe filtrar la lista al buscar un término', () => {
    render(<Index {...mockProps} />);
    
    const searchInput = screen.getByPlaceholderText('Buscar por nombre...');
    fireEvent.change(searchInput, { target: { value: 'Test' } });
    
    const applyButton = screen.getByText('Aplicar');
    fireEvent.click(applyButton);
    
    expect(router.get).toHaveBeenCalledWith(expect.any(String), { search: 'Test' }, expect.anything());
  });

  it('debe deshabilitar el boton de eliminar si el campo esta en uso', () => {
    render(<Index {...mockProps} />);
    
    // Campo 2 tiene document_types_count: 1
    const deleteButtons = screen.getAllByRole('button').filter(b => b.querySelector('svg'));
    // Encontramos el boton de eliminar de la segunda fila
    const secondRow = screen.getByText('Campo 2').closest('tr');
    const deleteBtn = secondRow?.querySelector('button[title*="utilizado"]');
    
    expect(deleteBtn).toBeDisabled();
  });
});
