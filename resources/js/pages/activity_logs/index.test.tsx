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

describe('ActivityLogs Index Page', () => {
  const mockProps = {
    logs: {
      data: [{ id: 1, event: 'Created user', user: { name: 'Admin', last_name: '' }, model: 'App\\Models\\User', created_at: '2024-03-13T10:00' }],
      total: 1,
      current_page: 1,
      last_page: 1,
      from: 1,
    },
    users: [{ id: 1, name: 'Admin', last_name: '' }],
    modules: ['User', 'Document'],
    filters: { date: '', user_id: '', module: '' },
  };

  beforeEach(() => {
    vi.clearAllMocks();
    (usePage as any).mockReturnValue({
      props: {
        auth: {
          user: { name: 'Admin' },
          roles: ['ADMINISTRADOR'],
          permissions: ['activity-logs.view'],
        },
      },
    });
  });

  it('debe renderizar la lista de actividades', () => {
    render(<Index {...mockProps} />);
    expect(screen.getByText('Registro de Actividades')).toBeInTheDocument();
    expect(screen.getByText((content) => content.includes('Created') && content.toLowerCase().includes('user'))).toBeInTheDocument();
  });

  it('debe filtrar al hacer clic en Aplicar filtros', () => {
    render(<Index {...mockProps} />);
    const select = screen.getByDisplayValue('Cualquier módulo');
    fireEvent.change(select, { target: { value: 'User' } });
    fireEvent.click(screen.getByText('Aplicar filtros'));
    
    expect(router.get).toHaveBeenCalledWith(expect.any(String), expect.objectContaining({ module: 'User' }), expect.anything());
  });
});
