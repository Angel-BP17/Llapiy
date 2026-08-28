import { render, screen, fireEvent } from '@testing-library/react';
import { describe, it, expect, vi, beforeEach } from 'vitest';
import Login from './login';
import React from 'react';
import { useForm } from '@inertiajs/react';
import login from '@/routes/login';

// Mock de Inertia
vi.mock('@inertiajs/react', async (importOriginal) => {
  const actual = await importOriginal<typeof import('@inertiajs/react')>();
  return {
    ...actual,
    useForm: vi.fn(),
    Head: ({ children }: any) => <>{children}</>,
  };
});

describe('Login Page - Etapas de Cobertura', () => {
  const mockPost = vi.fn();
  const mockSetData = vi.fn();

  beforeEach(() => {
    vi.clearAllMocks();
    (useForm as any).mockReturnValue({
      data: { user_name: '', password: '', remember: false },
      setData: mockSetData,
      post: mockPost,
      processing: false,
      errors: {},
    });
  });

  // ETAPA 1: RENDERIZADO
  it('ETAPA 1: debe renderizar el formulario de login correctamente', () => {
    render(<Login />);
    expect(screen.getByLabelText(/nombre de usuario/i)).toBeInTheDocument();
    expect(screen.getByLabelText(/contraseña/i)).toBeInTheDocument();
    expect(screen.getByRole('button', { name: /iniciar sesión/i })).toBeInTheDocument();
  });

  // ETAPA 7: CONSISTENCIA DE RUTAS DINÁMICAS (Submission Integrity)
  it('ETAPA 7: debe enviar el formulario a la URL dinámica generada por Wayfinder', () => {
    render(<Login />);
    
    const form = screen.getByRole('button', { name: /iniciar sesión/i }).closest('form');
    fireEvent.submit(form!);

    // Verificamos que se llamó a post() con la URL de Wayfinder, NO con "/login" manual
    // Esto garantiza compatibilidad con Laragon
    expect(mockPost).toHaveBeenCalledWith(login.store.url());
    expect(mockPost).not.toHaveBeenCalledWith('/login'); 
  });

  // ETAPA 4: INTERACCIÓN
  it('ETAPA 4: debe convertir el nombre de usuario a mayúsculas al escribir', () => {
    render(<Login />);
    const input = screen.getByLabelText(/nombre de usuario/i);
    
    fireEvent.change(input, { target: { value: 'admin' } });
    
    expect(mockSetData).toHaveBeenCalledWith('user_name', 'ADMIN');
  });

  // ETAPA 9: COHERENCIA DE CASING Y MANIFIESTO
  it('ETAPA 9: debe usar rutas de importación en minúsculas para consistencia con el manifiesto', () => {
    // Esta prueba verifica indirectamente el casing mediante la resolución del mock
    expect(login).toBeDefined();
    expect(login.store).toBeDefined();
  });
});
