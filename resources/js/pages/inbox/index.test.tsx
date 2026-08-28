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

describe('Inbox Index Page', () => {
  const mockProps = {
    documents: {
      data: [{ id: 1, asunto: 'Doc Test', folios: 10, document_type: { name: 'Memo' }, status: 'PENDIENTE' }],
      total: 1,
      current_page: 1,
      last_page: 1,
      from: 1,
    },
    areas: [{ id: 1, descripcion: 'Area 1' }],
    sections: [],
    andamios: [],
    boxes: [],
    stats: { attendedCount: 5, unattendedCount: 2, totalBlocks: 7 },
    filters: { search: '', area_id: '', periodo: '' },
    periods: ['2024-01', '2024-02'],
  };

  beforeEach(() => {
    vi.clearAllMocks();
    (usePage as any).mockReturnValue({
      props: {
        auth: {
          user: { name: 'Admin' },
          roles: ['ADMINISTRADOR'],
          permissions: ['inbox.view'],
        },
      },
    });
  });

  it('debe renderizar la bandeja de entrada y las estadisticas', () => {
    render(<Index {...mockProps} />);
    expect(screen.getByText('Bandeja de Entrada')).toBeInTheDocument();
    expect(screen.getByText('Doc Test')).toBeInTheDocument();
    expect(screen.getByText('5')).toBeInTheDocument(); // attendedCount
    expect(screen.getByText('2')).toBeInTheDocument(); // unattendedCount
  });

  it('debe filtrar por termino de busqueda', () => {
    render(<Index {...mockProps} />);
    const input = screen.getByPlaceholderText('N bloque o asunto');
    fireEvent.change(input, { target: { value: 'Memo' } });
    fireEvent.click(screen.getByText('Aplicar filtros'));
    
    expect(router.get).toHaveBeenCalledWith(expect.any(String), expect.objectContaining({ search: 'Memo' }), expect.anything());
  });
});
