import '@testing-library/jest-dom';
import { vi } from 'vitest';

// Mock de Inertia
vi.mock('@inertiajs/react', async (importOriginal) => {
  const actual = await importOriginal<typeof import('@inertiajs/react')>();
  return {
    ...actual,
    usePage: vi.fn(() => ({
      props: {
        auth: {
          user: { name: 'Admin', last_name: 'Test', roles: ['ADMINISTRADOR'], permissions: [
            'documents.view',
            'blocks.view',
            'inbox.view',
            'sections.view',
            'users.view',
            'roles.view',
            'document-types.view',
            'campos.view',
            'areas.view',
            'group-types.view',
            'activity-logs.view'
          ] },
        },
      },
      url: '/',
    })),
    router: {
      post: vi.fn(),
      get: vi.fn(),
      put: vi.fn(),
      delete: vi.fn(),
      reload: vi.fn(),
    },
    Link: ({ children, href, ...props }: any) => {
        // Simple mock component for Link
        return React.createElement('a', { href, ...props }, children);
    },
    Head: ({ children }: any) => children,
  };
});

// Mock de las rutas generadas por Wayfinder
vi.mock('@/routes', () => {
  const mockRoute = (path: string) => {
    const fn = () => ({ url: path, method: 'get' });
    fn.url = () => path;
    return fn;
  };

  const mockModule = (basePath: string) => {
    const index = mockRoute(`[DYNAMIC]${basePath}`);
    const store = mockRoute(`[DYNAMIC]${basePath}`);
    const show = mockRoute(`[DYNAMIC]${basePath}/1`);
    const update = mockRoute(`[DYNAMIC]${basePath}/1`);
    const destroy = mockRoute(`[DYNAMIC]${basePath}/1`);
    const pdf = mockRoute(`[DYNAMIC]${basePath}/pdf`);
    const file = mockRoute(`[DYNAMIC]${basePath}/1/file`);
    
    return {
      index: Object.assign(index, { url: () => `[DYNAMIC]${basePath}` }),
      store: Object.assign(store, { url: () => `[DYNAMIC]${basePath}` }),
      show: Object.assign(show, { url: () => `[DYNAMIC]${basePath}/1` }),
      update: Object.assign(update, { url: () => `[DYNAMIC]${basePath}/1` }),
      destroy: Object.assign(destroy, { url: () => `[DYNAMIC]${basePath}/1` }),
      pdf: Object.assign(pdf, { url: () => `[DYNAMIC]${basePath}/pdf` }),
      file: Object.assign(file, { url: () => `[DYNAMIC]${basePath}/1/file` }),
      url: () => `[DYNAMIC]${basePath}`,
    };
  };

  const loginModule = mockModule('/login');

  return {
    dashboard: mockRoute('[DYNAMIC]/'),
    login: loginModule,
    documents: mockModule('/documentos'),
    blocks: mockModule('/bloques'),
    inbox: mockModule('/bandeja'),
    sections: mockModule('/sections'),
    users: mockModule('/usuarios'),
    roles: mockModule('/roles'),
    document_types: mockModule('/tipos-documentos'),
    campos: mockModule('/campos'),
    areas: mockModule('/areas'),
    group_types: mockModule('/tipos-grupos'),
    activity_logs: mockModule('/actividades'),
    logout: mockRoute('/logout'),
    profile: mockRoute('/perfil'),
  };
});

// Mock específico para importaciones directas de módulos
vi.mock('@/routes/login', () => {
    const loginObj = {
        store: { url: () => '[DYNAMIC]/login' },
        index: { url: () => '[DYNAMIC]/login' }
    };
    return {
        login: loginObj,
        default: loginObj
    };
});

// React must be in scope for our mock Link
import React from 'react';

// Mock de Axios
vi.mock('axios', () => ({
  default: {
    get: vi.fn(() => Promise.resolve({ data: {} })),
    post: vi.fn(() => Promise.resolve({ data: {} })),
    put: vi.fn(() => Promise.resolve({ data: {} })),
    delete: vi.fn(() => Promise.resolve({ data: {} })),
    interceptors: {
      request: { use: vi.fn(), eject: vi.fn() },
      response: { use: vi.fn(), eject: vi.fn() },
    },
  },
}));

// Verificación de integridad de paths para evitar 404s
const originalFetch = window.fetch;
window.fetch = (url: any, ...args) => {
  if (typeof url === 'string' && (url.startsWith('C:/') || url.startsWith('file:///'))) {
    throw new Error(`Asset Path Error: Intentando cargar un recurso local absoluto: ${url}. Esto causará 404 en producción.`);
  }
  return originalFetch(url, ...args);
};

// Mock de window.matchMedia
Object.defineProperty(window, 'matchMedia', {
  writable: true,
  value: vi.fn().mockImplementation(query => ({
    matches: false,
    media: query,
    onchange: null,
    addListener: vi.fn(), // deprecated
    removeListener: vi.fn(), // deprecated
    addEventListener: vi.fn(),
    removeEventListener: vi.fn(),
    dispatchEvent: vi.fn(),
  })),
});
