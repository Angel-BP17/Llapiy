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

describe('Documents Index Page', () => {
  const mockProps = {
    documents: [{ id: 1, n_documento: 'D1', asunto: 'Doc 1', folios: 5, status: 'REGISTRADO', document_type: { name: 'Memo' } }],
    pagination: { total: 1, current_page: 1, last_page: 1, from: 1 },
    documentTypes: [{ id: 1, name: 'Memo', campo_types: [] }],
    areas: [{ id: 1, descripcion: 'Area 1' }],
    groups: [],
    subgroups: [],
    years: [2024],
    filters: { asunto: '', area_id: '' },
    periods: ['2024-01'],
    stats: { total: 1, registered: 1, archived: 0 },
  };

  beforeEach(() => {
    vi.clearAllMocks();
    (usePage as any).mockReturnValue({
      props: {
        auth: {
          user: { name: 'Admin' },
          roles: ['ADMINISTRADOR'],
          permissions: ['documents.view.all', 'documents.create'],
        },
      },
    });
  });

  it('debe renderizar la lista de documentos y los contadores', () => {
    render(<Index {...mockProps} />);
    expect(screen.getByText('Gestión de Documentos')).toBeInTheDocument();
    expect(screen.getByText('Doc 1')).toBeInTheDocument();
    expect(screen.getAllByText('Memo').length).toBeGreaterThan(0);
  });

  it('debe filtrar por asunto', () => {
    render(<Index {...mockProps} />);
    const input = screen.getByPlaceholderText('Asunto');
    fireEvent.change(input, { target: { value: 'Contrato' } });
    fireEvent.click(screen.getByText('Aplicar filtros'));
    
    expect(router.get).toHaveBeenCalledWith(expect.any(String), expect.objectContaining({ asunto: 'Contrato' }), expect.anything());
  });

  it('debe mostrar el detalle del documento al hacer clic en Ver', () => {
    render(<Index {...mockProps} />);
    fireEvent.click(screen.getByText('Ver'));
    
    expect(screen.getByText('Detalle del documento')).toBeInTheDocument();
    expect(screen.getAllByText('D1').length).toBeGreaterThan(0);
    expect(screen.getAllByText('Doc 1').length).toBeGreaterThan(0);
    // Verificamos que el area se resuelva correctamente
    expect(screen.getByText('Area 1')).toBeInTheDocument();
  });
});
