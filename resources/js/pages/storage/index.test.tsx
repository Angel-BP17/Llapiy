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

describe('Storage Index Page', () => {
  const mockProps = {
    level: 'sections' as const,
    sections: [{ id: 1, n_section: '10', descripcion: 'S10', andamios_count: 5 }],
    andamios: [],
    boxes: [],
    archivos: [],
    activeSection: null,
    activeAndamio: null,
    activeBox: null,
    filters: { search: '' },
    counts: { total: 1 },
    stats: {
      totalBlocks: 100,
      archivedBlocks: 75,
      indexedBlocks: 50,
      bothArchivedAndIndexed: 45,
      totalBoxes: 10,
      filledBoxes: 8,
      emptyBoxes: 2,
    },
  };

  beforeEach(() => {
    vi.clearAllMocks();
    (usePage as any).mockReturnValue({
      props: {
        auth: {
          user: { name: 'Admin' },
          roles: ['ADMINISTRADOR'],
          permissions: ['sections.view', 'sections.create'],
        },
      },
    });
  });

  it('debe renderizar la lista de secciones', () => {
    render(<Index {...mockProps} />);
    expect(screen.getByText('Gestión de Secciones')).toBeInTheDocument();
    expect(screen.getByText('Sección 10')).toBeInTheDocument();
    expect(screen.getByText('5 andamios')).toBeInTheDocument();
  });

  it('debe renderizar contadores de almacenamiento y el boton de reporte', () => {
    render(<Index {...mockProps} />);
    expect(screen.getByText('Bloques archivados')).toBeInTheDocument();
    expect(screen.getByText('75')).toBeInTheDocument();
    expect(screen.getByText('Bloques digitalizados')).toBeInTheDocument();
    expect(screen.getByText('50')).toBeInTheDocument();
    expect(screen.getByText('Cajas ocupadas')).toBeInTheDocument();
    expect(screen.getByText('8')).toBeInTheDocument();
    expect(screen.getByText('Reporte PDF')).toBeInTheDocument();
  });

  it('debe permitir buscar en el nivel actual', () => {
    render(<Index {...mockProps} />);
    const input = screen.getByPlaceholderText('Buscar en sections... (o por código de bloque)');
    fireEvent.change(input, { target: { value: '99' } });
    fireEvent.click(screen.getByText('Filtrar'));
    
    expect(router.get).toHaveBeenCalled();
  });

  it('debe llamar a router.post al guardar un nuevo item', () => {
    render(<Index {...mockProps} />);
    const numInput = screen.getByPlaceholderText('Número');
    fireEvent.change(numInput, { target: { value: '11' } });
    
    fireEvent.click(screen.getByText('Guardar'));
    expect(router.post).toHaveBeenCalled();
  });
});
