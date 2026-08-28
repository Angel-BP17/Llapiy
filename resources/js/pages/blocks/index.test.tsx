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

describe('Blocks Index Page', () => {
  const mockProps = {
    blocks: [{ id: 1, n_bloque: 'B1', asunto: 'Bloque Test', status: 'CREADO', folios: 100, area: 'Area Test' }],
    pagination: { total: 1, current_page: 1, last_page: 1, from: 1 },
    areas: [{ id: 1, descripcion: 'Area Test' }],
    groups: [],
    subgroups: [],
    years: [2024, 2025],
    filters: { asunto: '' },
    stats: { totalBlocks: 1, attendedCount: 1, unattendedCount: 0 },
  };

  beforeEach(() => {
    vi.clearAllMocks();
    (usePage as any).mockReturnValue({
      props: {
        auth: {
          user: { name: 'Admin' },
          roles: ['ADMINISTRADOR'],
          permissions: ['blocks.view', 'blocks.create'],
        },
      },
    });
  });

  it('debe renderizar la lista de bloques y las estadisticas', () => {
    render(<Index {...mockProps} />);
    expect(screen.getByText('Gestión de Bloques')).toBeInTheDocument();
    expect(screen.getByText((content) => content.includes('Bloque Test'))).toBeInTheDocument();
    expect(screen.getByText('B1')).toBeInTheDocument();
  });

  it('debe filtrar al buscar un bloque', () => {
    render(<Index {...mockProps} />);
    const input = screen.getByPlaceholderText('Asunto');

    fireEvent.change(input, { target: { value: 'B1' } });
    fireEvent.click(screen.getByText('Aplicar filtros'));
    
    expect(router.get).toHaveBeenCalledWith(expect.any(String), expect.objectContaining({ asunto: 'B1' }), expect.anything());
  });
});
