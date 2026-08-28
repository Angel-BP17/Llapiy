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

describe('DocumentTypes Index Page', () => {
  const mockProps = {
    documentTypes: [
      { 
        id: 1, 
        name: 'Acta', 
        campo_types: [{ id: 1, name: 'Fecha' }], 
        groups: [{ id: 1, descripcion: 'Gerencia' }],
        subgroups: [],
        documents_count: 0
      }
    ],
    areas: [],
    campoTypes: [],
    paginationData: { total: 1, current_page: 1, last_page: 1 },
    filters: { name: '' },
  };

  beforeEach(() => {
    vi.clearAllMocks();
    (usePage as any).mockReturnValue({
      props: {
        auth: {
          user: { name: 'Admin' },
          roles: ['ADMINISTRADOR'],
          permissions: ['document-types.view', 'document-types.create'],
        },
      },
    });
  });

  it('debe renderizar el mensaje de estado vacio cuando no hay registros', () => {
    render(<Index {...mockProps} documentTypes={[]} paginationData={{total: 0}} />);
    expect(screen.getByText('No se encontraron tipos de documentos.')).toBeInTheDocument();
  });

  it('debe renderizar la lista de tipos de documentos correctamente', () => {
    render(<Index {...mockProps} />);
    expect(screen.getByText('Acta')).toBeInTheDocument();
    expect(screen.getByText('Ver 1 campos')).toBeInTheDocument();
  });

  it('debe abrir el modal de campos al hacer clic en el boton correspondiente', () => {
    render(<Index {...mockProps} />);
    fireEvent.click(screen.getByText('Ver 1 campos'));
    
    // El titulo del modal de lista seria "Campos: Acta"
    expect(screen.getByText('Campos: Acta')).toBeInTheDocument();
    expect(screen.getByText('Fecha')).toBeInTheDocument();
  });

  it('debe abrir el modal de creacion', () => {
    render(<Index {...mockProps} />);
    fireEvent.click(screen.getByText('Crear Nuevo'));
    
    expect(screen.getByText('Crear Tipo de Documento')).toBeInTheDocument();
  });
});
