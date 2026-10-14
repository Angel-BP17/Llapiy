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
    const input = screen.getByPlaceholderText('Buscar por código, serie, asunto, rango o periodo...');
    fireEvent.change(input, { target: { value: '99' } });
    fireEvent.click(screen.getByText('Filtrar'));
    
    expect(router.get).toHaveBeenCalled();
  });

  it('debe permitir filtrar ingresando un periodo manualmente', () => {
    render(<Index {...mockProps} />);
    const periodoInput = screen.getByPlaceholderText('Periodo (año ej. 2026)...');
    fireEvent.change(periodoInput, { target: { value: '2026' } });
    fireEvent.click(screen.getByText('Filtrar'));

    expect(router.get).toHaveBeenCalledWith(
      expect.any(String),
      expect.objectContaining({ periodo: '2026' }),
      expect.any(Object)
    );
  });

  it('debe llamar a router.post al guardar un nuevo item', () => {
    render(<Index {...mockProps} />);
    const numInput = screen.getByPlaceholderText('Número');
    fireEvent.change(numInput, { target: { value: '11' } });
    
    fireEvent.click(screen.getByText('Guardar'));
    expect(router.post).toHaveBeenCalled();
  });

  it('debe renderizar botones para editar bloque y actualizar archivo pdf en nivel archivos', () => {
    const archivosProps = {
      ...mockProps,
      level: 'archivos' as const,
      activeSection: { id: 1, n_section: '1' },
      activeAndamio: { id: 1, n_andamio: '1' },
      activeBox: { id: 1, n_box: '1' },
      archivos: [
        {
          id: 101,
          n_bloque: '55',
          asunto: 'Contrato de Servicios 2026',
          folios: '25',
          periodo: '2026',
          fecha: '2026-03-15',
          root: 'blocks/contrato.pdf',
          documentary_series: { id: 2, codigo: 'SER-02', nombre: 'Contratos' },
          periods: [{ id: 1, rango_inicial: 1, rango_final: 25, periodo: 2026 }],
        },
        {
          id: 102,
          n_bloque: '56',
          asunto: 'Informes Mensuales',
          folios: '10',
          periodo: '2026',
          fecha: '2026-04-10',
          root: null,
          periods: [{ id: 2, rango_inicial: 26, rango_final: 35, periodo: 2026 }],
        },
      ],
      documentarySeries: [{ id: 2, codigo: 'SER-02', nombre: 'Contratos' }],
    };

    render(<Index {...archivosProps} />);

    // Verificar que los asuntos y números de bloque se muestran
    expect(screen.getByText('Contrato de Servicios 2026')).toBeInTheDocument();
    expect(screen.getByText('Informes Mensuales')).toBeInTheDocument();

    // Debe mostrar badges de digitalización
    expect(screen.getByText('Digitalizado')).toBeInTheDocument();
    expect(screen.getByText('Sin PDF')).toBeInTheDocument();

    // Debe haber botones "Editar"
    const editButtons = screen.getAllByRole('button', { name: /editar/i });
    expect(editButtons.length).toBeGreaterThanOrEqual(2);

    // Debe haber botones de PDF ("Actualizar PDF" para el digitalizado y "Subir PDF" para el que no tiene)
    expect(screen.getByRole('button', { name: /actualizar pdf/i })).toBeInTheDocument();
    expect(screen.getByRole('button', { name: /subir pdf/i })).toBeInTheDocument();

    // Al hacer clic en Editar, se abre el modal de edición
    fireEvent.click(editButtons[0]);
    expect(screen.getByText(/Editar Información del Bloque Nº 55/i)).toBeInTheDocument();
    expect(screen.getByDisplayValue('Contrato de Servicios 2026')).toBeInTheDocument();

    // Al hacer clic en Subir PDF, se abre el modal de actualizar archivo PDF
    const uploadBtn = screen.getByRole('button', { name: /subir pdf/i });
    fireEvent.click(uploadBtn);
    expect(screen.getByText(/Actualizar Archivo PDF - Bloque Nº 56/i)).toBeInTheDocument();
  });

  it('debe renderizar bloques encontrados en el almacén con serie documental y rangos', () => {
    const searchedProps = {
      ...mockProps,
      searchedBlocks: [
        {
          id: 501,
          n_bloque: '77',
          asunto: 'Planilla de Sueldos',
          folios: 30,
          periodo: 2026,
          documentary_series: { id: 10, codigo: 'SER-PLN', nombre: 'Planillas' },
          periods: [
            { id: 1, rango_inicial: 1, rango_final: 30, periodo: 2026 },
          ],
          path: {
            section: { id: 1, n_section: '1', descripcion: 'Sec 1' },
            andamio: { id: 2, n_andamio: '3', descripcion: 'And 3' },
            box: { id: 4, n_box: '9', descripcion: 'Box 9' },
          },
        },
      ],
    };

    render(<Index {...searchedProps} />);

    expect(screen.getByText(/Bloques encontrados en el almacén/i)).toBeInTheDocument();
    expect(screen.getByText('77 - Planilla de Sueldos')).toBeInTheDocument();
    expect(screen.getByText('SER-PLN - Planillas')).toBeInTheDocument();
    expect(screen.getByText('1 - 30 (Año 2026)')).toBeInTheDocument();
    expect(screen.getByText('Sec. 1')).toBeInTheDocument();
    expect(screen.getByText('And. 3')).toBeInTheDocument();
    expect(screen.getByText('Caja 9')).toBeInTheDocument();
  });
});
