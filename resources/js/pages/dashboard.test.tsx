import { render, screen } from '@testing-library/react';
import { describe, it, expect, vi, beforeEach } from 'vitest';
import Dashboard from './dashboard';
import React from 'react';
import { usePage } from '@inertiajs/react';

// Mock de usePage
vi.mock('@inertiajs/react', async (importOriginal) => {
  const actual = await importOriginal<typeof import('@inertiajs/react')>();
  return {
    ...actual,
    usePage: vi.fn(),
  };
});

// Mock de componentes de gráficos para evitar errores de renderizado de SVG
vi.mock('@/components/Dashboard/RecentDocumentsChart', () => ({
  default: () => <div data-testid="recent-docs-chart">Recent Docs Chart</div>
}));

vi.mock('@/components/Dashboard/DocumentTypesPieChart', () => ({
  default: () => <div data-testid="pie-chart">Pie Chart</div>
}));

vi.mock('@/components/Dashboard/DashboardCharts', () => ({
  default: () => <div data-testid="area-stats-chart">Area Stats Chart</div>
}));

vi.mock('@/Layouts/DashboardLayout', () => ({
  default: ({ children }: any) => <div data-testid="layout">{children}</div>
}));


describe('Dashboard Page - Etapas de Cobertura', () => {
  const mockProps = {
    stats: {
      userCount: 10,
      documentCount: 50,
      totalNoAlmacenados: 5,
      documentTypeCount: 4,
    },
    documentosRecientes: [],
    documentosPorTipo: [],
    docsByArea: [],
    funnelData: [
      { name: 'Entrada', value: 10, fill: '#8884d8' },
      { name: 'Procesados', value: 5, fill: '#83a6ed' }
    ],
    activityStats: [
      { name: 'User 1', actividad: 20 }
    ]
  };

  const setupMockPage = (isAdmin = true) => {
    (usePage as any).mockReturnValue({
      props: {
        auth: {
          user: { name: 'Admin' },
          roles: isAdmin ? ['ADMINISTRADOR'] : ['OPERADOR'],
          permissions: ['users.view', 'documents.view.all', 'blocks.view.all', 'inbox.view', 'document-types.view']
        }
      }
    });
  };

  beforeEach(() => {
    vi.clearAllMocks();
  });

  // ETAPA 1: CONTRATO Y RENDERIZADO
  it('ETAPA 1: debe renderizar el título principal y el layout', () => {
    setupMockPage();
    render(<Dashboard {...mockProps} />);
    expect(screen.getByText('Panel de Control')).toBeInTheDocument();
    expect(screen.getByTestId('layout')).toBeInTheDocument();
  });

  // ETAPA 2: INTEGRIDAD DE RUTAS (Wayfinder)
  it('ETAPA 2: los enlaces de las tarjetas deben apuntar a las rutas correctas', () => {
    setupMockPage();
    render(<Dashboard {...mockProps} />);
    
    // El enlace de 'Archivos registrados' debe ir a /documentos
    const docsLink = screen.getByText('Archivos registrados').closest('div')?.querySelector('a');
    expect(docsLink).toHaveAttribute('href', '/documentos');
  });

  // ETAPA 3: SEGURIDAD Y VISIBILIDAD (RBAC)
  it('ETAPA 3: debe mostrar gráficos adicionales solo para ADMINISTRADORES', () => {
    // Caso Admin
    setupMockPage(true);
    const { rerender } = render(<Dashboard {...mockProps} />);
    expect(screen.getByTestId('area-stats-chart')).toBeInTheDocument();

    // Caso Operador
    setupMockPage(false);
    rerender(<Dashboard {...mockProps} />);
    expect(screen.queryByTestId('area-stats-chart')).not.toBeInTheDocument();
  });

  // ETAPA 5: RESILIENCIA
  it('ETAPA 5: debe manejar props de estadísticas nulas sin explotar', () => {
    setupMockPage();
    // @ts-ignore
    render(<Dashboard stats={null} documentosRecientes={[]} documentosPorTipo={[]} docsByArea={[]} funnelData={[]} activityStats={[]} />);
    
    // Debería mostrar 0 por defecto si stats es null
    const values = screen.getAllByText('0');
    expect(values.length).toBeGreaterThan(0);
  });
});
