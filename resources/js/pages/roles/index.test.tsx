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

describe('Roles Index Page', () => {
  const mockProps = {
    roles: [
      { id: 1, name: 'ADMINISTRADOR', permissions: ['users.view', 'roles.view'] },
      { id: 2, name: 'OPERADOR', permissions: ['documents.view.own'] },
    ],
    permissions: ['users.view', 'users.create', 'roles.view', 'documents.view.own'],
    pagination: { total: 2, current_page: 1, last_page: 1 },
    filters: { search: '' },
  };

  beforeEach(() => {
    vi.clearAllMocks();
    (usePage as any).mockReturnValue({
      props: {
        auth: {
          user: { name: 'Admin' },
          roles: ['ADMINISTRADOR'],
          permissions: ['roles.view', 'roles.create', 'roles.delete'],
        },
      },
    });
  });

  it('debe renderizar la lista de roles correctamente', () => {
    render(<Index {...mockProps} />);
    
    expect(screen.getAllByText('Roles').length).toBeGreaterThan(0);
    expect(screen.getByText('Administrador')).toBeInTheDocument(); // roleLabels[ADMINISTRADOR]
    expect(screen.getByText('OPERADOR')).toBeInTheDocument();
    expect(screen.getByText('2 roles')).toBeInTheDocument();
  });

  it('debe abrir el modal de creacion y permitir escribir el nombre', () => {
    render(<Index {...mockProps} />);
    
    const newButton = screen.getByText('Nuevo Rol');
    fireEvent.click(newButton);
    
    expect(screen.getByText('Crear Nuevo Rol')).toBeInTheDocument();
    
    const nameInput = screen.getByPlaceholderText('EJ_ROL_NUEVO');
    fireEvent.change(nameInput, { target: { value: 'super test' } });
    
    // Verificamos que se convierta a UPPER_SNAKE_CASE segun la logica del componente
    expect(nameInput).toHaveValue('SUPER_TEST');
  });

  it('debe permitir seleccionar permisos en el modal', () => {
    render(<Index {...mockProps} />);
    fireEvent.click(screen.getByText('Nuevo Rol'));
    
    // El label del permiso 'users.view' segun getPermissionLabel es 'Ver Usuarios'
    const permissionCheckbox = screen.getByLabelText('Ver Usuarios');
    fireEvent.click(permissionCheckbox);
    
    expect(permissionCheckbox).toBeChecked();
  });

  it('debe deshabilitar el boton de eliminar para el rol ADMINISTRADOR', () => {
    render(<Index {...mockProps} />);
    
    // Buscamos el boton de eliminar en la fila de ADMINISTRADOR
    const adminRow = screen.getByText('Administrador').closest('tr');
    const deleteBtn = adminRow?.querySelector('button[title*="Administrador no puede ser eliminado"]');
    
    expect(deleteBtn).toBeDisabled();
  });

  it('debe aplicar una plantilla rapida de permisos', () => {
    render(<Index {...mockProps} />);
    fireEvent.click(screen.getByText('Nuevo Rol'));
    
    // Buscamos la plantilla 'Colaborador Documental'
    const templateBtn = screen.getByText('Colaborador Documental');
    fireEvent.click(templateBtn);
    
    // Re-chequeando logica de labels: getPermissionLabel('documents.view.own') -> Ver Documentos (Propios)
    const permissionCheckbox = screen.getByLabelText('Ver Documentos (Propios)');
    expect(permissionCheckbox).toBeChecked();
  });
});
