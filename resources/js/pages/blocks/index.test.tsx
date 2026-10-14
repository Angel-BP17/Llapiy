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
  } as any;

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
    const input = screen.getByPlaceholderText('Buscar por asunto del bloque...');

    fireEvent.change(input, { target: { value: 'B1' } });
    fireEvent.click(screen.getByText('Aplicar filtros'));
    
    expect(router.get).toHaveBeenCalledWith(expect.any(String), expect.objectContaining({ asunto: 'B1' }), expect.anything());
  });

  it('debe permitir editar el número de bloque en el modal de edición', () => {
    const propsWithEditableBlock = {
      ...mockProps,
      blocks: [
        {
          id: 1,
          n_bloque: '10',
          asunto: 'Bloque Modificable',
          status: 'CREADO',
          folios: 100,
          periodo: 2025,
          periods: [
            { id: 1, rango_inicial: 1, rango_final: 100, periodo: 2025 },
          ],
          area: 'Area Test',
          fecha: '2025-01-01',
          can: { update: true, delete: true },
        },
      ],
    };

    render(<Index {...propsWithEditableBlock} />);

    // Abrir modal de edición
    const editButton = screen.getByText('Editar');
    fireEvent.click(editButton);

    // Debe mostrarse el modal "Editar Bloque"
    expect(screen.getByText('Editar Bloque')).toBeInTheDocument();

    // El campo de número de bloque debe existir como input y tener el valor '10'
    const nBloqueInput = screen.getByPlaceholderText('Ej. 45') as HTMLInputElement;
    expect(nBloqueInput).toBeInTheDocument();
    expect(nBloqueInput.value).toBe('10');

    // Modificar el número de bloque
    fireEvent.change(nBloqueInput, { target: { value: '25' } });
    expect(nBloqueInput.value).toBe('25');

    // Enviar formulario
    const submitButton = screen.getByText('Guardar Cambios');
    fireEvent.click(submitButton);

    expect(router.post).toHaveBeenCalledWith(
      '/bloques/1',
      expect.objectContaining({ n_bloque: 25, _method: 'PUT' }),
      expect.anything()
    );
  });

  it('debe mostrar la sección de edición y actualización de PDF para usuario ARCHIVO_CENTRAL', () => {
    (usePage as any).mockReturnValue({
      props: {
        auth: {
          user: { name: 'Encargado Archivo' },
          roles: ['ARCHIVO_CENTRAL'],
          permissions: ['blocks.view', 'blocks.update', 'blocks.upload'],
        },
      },
    });

    const propsWithPdfBlock = {
      ...mockProps,
      blocks: [
        {
          id: 1,
          n_bloque: '10',
          asunto: 'Bloque con PDF',
          status: 'CREADO',
          folios: 100,
          periodo: 2025,
          root: 'blocks/expediente.pdf',
          periods: [
            { id: 1, rango_inicial: 1, rango_final: 100, periodo: 2025 },
          ],
          area: 'Area Test',
          fecha: '2025-01-01',
          can: { update: true, delete: true },
        },
      ],
    };

    render(<Index {...propsWithPdfBlock} />);

    // Abrir modal de edición
    fireEvent.click(screen.getByText('Editar'));

    // Debe mostrar la sección exclusiva de PDF
    expect(screen.getByText('Expediente Digital (PDF)')).toBeInTheDocument();
    expect(screen.getByText('Archivo Central / Administrador')).toBeInTheDocument();
    expect(screen.getByText('Archivo PDF actualmente ingresado')).toBeInTheDocument();
    expect(screen.getByText('Ver PDF Actual')).toBeInTheDocument();
  });

  it('no debe mostrar la sección de edición de PDF para usuarios sin rol ARCHIVO_CENTRAL ni ADMINISTRADOR', () => {
    (usePage as any).mockReturnValue({
      props: {
        auth: {
          user: { name: 'Colaborador' },
          roles: ['COLABORADOR_DOCUMENTAL'],
          permissions: ['blocks.view', 'blocks.update'],
        },
      },
    });

    const propsWithBlock = {
      ...mockProps,
      blocks: [
        {
          id: 1,
          n_bloque: '10',
          asunto: 'Bloque sin PDF',
          status: 'CREADO',
          folios: 100,
          periodo: 2025,
          root: null,
          periods: [
            { id: 1, rango_inicial: 1, rango_final: 100, periodo: 2025 },
          ],
          area: 'Area Test',
          fecha: '2025-01-01',
          can: { update: true, delete: true },
        },
      ],
    };

    render(<Index {...propsWithBlock} />);

    // Abrir modal de edición
    fireEvent.click(screen.getByText('Editar'));

    // NO debe mostrar la sección de PDF
    expect(screen.queryByText('Archivo Central / Administrador')).not.toBeInTheDocument();
    expect(screen.queryByText('Expediente Digital (PDF)')).not.toBeInTheDocument();
  });

  it('debe mostrar input de subir PDF y título Distribución de Documentos al crear bloque con rol ARCHIVO_CENTRAL o ADMINISTRADOR', () => {
    (usePage as any).mockReturnValue({
      props: {
        auth: {
          user: { name: 'Encargado Archivo' },
          roles: ['ARCHIVO_CENTRAL'],
          permissions: ['blocks.view', 'blocks.create'],
        },
      },
    });

    const { unmount } = render(<Index {...mockProps} />);

    // Abrir modal de creación
    fireEvent.click(screen.getByText('Ingresar bloque'));

    // Debe mostrar la sección de PDF y el título actualizado
    expect(screen.getByText('Expediente Digital (PDF)')).toBeInTheDocument();
    expect(screen.getByText('Archivo Central / Administrador')).toBeInTheDocument();
    expect(screen.getByText('Distribución de Documentos por Periodo (Años)')).toBeInTheDocument();

    unmount();

    // Probar también para rol ADMINISTRADOR
    (usePage as any).mockReturnValue({
      props: {
        auth: {
          user: { name: 'Admin' },
          roles: ['ADMINISTRADOR'],
          permissions: ['blocks.view', 'blocks.create'],
        },
      },
    });

    render(<Index {...mockProps} />);
    fireEvent.click(screen.getByText('Ingresar bloque'));
    expect(screen.getByText('Expediente Digital (PDF)')).toBeInTheDocument();
    expect(screen.getByText('Archivo Central / Administrador')).toBeInTheDocument();
  });

  it('no debe mostrar input de subir PDF al crear bloque para usuario sin rol autorizado', () => {
    (usePage as any).mockReturnValue({
      props: {
        auth: {
          user: { name: 'Colaborador' },
          roles: ['COLABORADOR_DOCUMENTAL'],
          permissions: ['blocks.view', 'blocks.create'],
        },
      },
    });

    render(<Index {...mockProps} />);

    // Abrir modal de creación
    fireEvent.click(screen.getByText('Ingresar bloque'));

    // NO debe mostrar la sección de PDF
    expect(screen.queryByText('Expediente Digital (PDF)')).not.toBeInTheDocument();
    expect(screen.queryByText('Archivo Central / Administrador')).not.toBeInTheDocument();
    // Pero sí debe tener el nuevo título
    expect(screen.getByText('Distribución de Documentos por Periodo (Años)')).toBeInTheDocument();
  });
});
