import { dashboard } from '@/routes';
import documents from '@/routes/documents';
import blocks from '@/routes/blocks';
import inbox from '@/routes/inbox';
import sections from '@/routes/sections';
import users from '@/routes/users';
import roles from '@/routes/roles';
import document_types from '@/routes/document_types';
import documentary_series from '@/routes/documentary_series';
import campos from '@/routes/campos';
import areas from '@/routes/areas';
import group_types from '@/routes/group_types';
import activity_logs from '@/routes/activity_logs';

export type NavChild = {
  label: string;
  href: string;
  match: string[];
  permission?: string | string[];
  active?: boolean;
};

export type NavItem = {
  label: string;
  href?: string;
  match?: string[];
  icon: string;
  permission?: string | string[];
  children?: NavChild[];
  open?: boolean;
  active?: boolean;
};

export type NavSection = {
  title: string;
  items: NavItem[];
};

export const defaultSections: NavSection[] = [
  {
    title: "NAVEGACION",
    items: [
      {
        label: "Home",
        href: dashboard.url(),
        icon: '<svg viewBox="0 0 24 24" class="h-5 w-5" aria-hidden="true"><path fill="currentColor" d="M12 4 3 11h2v9h6v-6h2v6h6v-9h2Z"/></svg>',
      },
      {
        label: "Gestionar documentos",
        match: [documents.index.url(), blocks.index.url()],
        permission: ["documents.view.all", "documents.view.own", "documents.view.group", "blocks.view.all", "blocks.view.own", "blocks.view.group"],
        icon: '<svg viewBox="0 0 24 24" class="h-5 w-5" aria-hidden="true"><path fill="currentColor" d="M6 2h9l5 5v15a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2Zm8 1.5V8h4.5L14 3.5ZM8 12h8v2H8v-2Zm0 4h8v2H8v-2Z"/></svg>',
        children: [
          {
            label: "Documentos",
            href: documents.index.url(),
            match: [documents.index.url()],
            permission: ["documents.view.all", "documents.view.own", "documents.view.group"],
          },
          {
            label: "Bloques",
            href: blocks.index.url(),
            match: [blocks.index.url()],
            permission: ["blocks.view.all", "blocks.view.own", "blocks.view.group"],
          },
        ],
      },
    ],
  },
  {
    title: "BANDEJA",
    items: [
      {
        label: "Bandeja de entrada",
        href: inbox.index.url(),
        match: [inbox.index.url()],
        permission: "inbox.view",
        icon: '<svg viewBox="0 0 24 24" class="h-5 w-5" aria-hidden="true"><path fill="currentColor" d="M4 4h16v8h-4l-2 4h-4l-2-4H4V4Zm0 10h4l2 4h4l2-4h4v6H4v-6Z"/></svg>',
      },
      {
        label: "Almacenamiento",
        href: sections.index.url(),
        match: [sections.index.url(), "/sections", "/almacenamiento"],
        permission: "sections.view",
        icon: '<svg viewBox="0 0 24 24" class="h-5 w-5" aria-hidden="true"><path fill="currentColor" d="M4 6h16v4H4V6Zm0 6h16v6H4v-6Zm2 2v2h12v-2H6Z"/></svg>',
      },
    ],
  },
  {
    title: "ADMINISTRACION",
    items: [
      {
        label: "Usuarios",
        href: users.index.url(),
        match: [users.index.url()],
        permission: "users.view",
        icon: '<svg viewBox="0 0 24 24" class="h-5 w-5" aria-hidden="true"><path fill="currentColor" d="M12 12a4 4 0 1 0-4-4 4 4 0 0 0 4 4Zm0 2c-4.42 0-8 2-8 4.5V20h16v-1.5c0-2.5-3.58-4.5-8-4.5Z"/></svg>',
      },
      {
        label: "Roles",
        href: roles.index.url(),
        match: [roles.index.url()],
        permission: "roles.view",
        icon: '<svg viewBox="0 0 24 24" class="h-5 w-5" aria-hidden="true"><path fill="currentColor" d="m4 10 8-6 8 6v8a2 2 0 0 1-2 2h-4v-6H10v6H6a2 2 0 0 1-2-2v-8Z"/></svg>',
      },
      {
        label: "Inf. adicional de documentos",
        match: [document_types.index.url(), campos.index.url()],
        permission: "document-types.view",
        icon: '<svg viewBox="0 0 24 24" class="h-5 w-5" aria-hidden="true"><path fill="currentColor" d="M7 3h8l4 4v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Zm7 1.5V8h3.5L14 4.5ZM9 12h6v2H9v-2Zm0 4h6v2H9v-2Z"/></svg>',
        children: [
          {
            label: "Tipos de documentos",
            href: document_types.index.url(),
            match: [document_types.index.url()],
            permission: "document-types.view",
          },
          {
            label: "Series Documentales",
            href: documentary_series.index.url(),
            match: [documentary_series.index.url()],
            permission: "documentary-series.view",
          },
          {
            label: "Campos",
            href: campos.index.url(),
            match: [campos.index.url()],
            permission: "campos.view",
          },
        ],
      },
      {
        label: "Areas",
        match: [areas.index.url(), group_types.index.url()],
        permission: "areas.view",
        icon: '<svg viewBox="0 0 24 24" class="h-5 w-5" aria-hidden="true"><path fill="currentColor" d="M3 7h18v4H3V7Zm0 6h18v4H3v-4Z"/></svg>',
        children: [
          {
            label: "Gestionar Areas",
            href: areas.index.url(),
            match: [areas.index.url()],
            permission: "areas.view",
          },
          {
            label: "Tipos de Grupos",
            href: group_types.index.url(),
            match: [group_types.index.url()],
            permission: "group-types.view",
          },
        ],
      },
      {
        label: "Registro de actividades",
        href: activity_logs.index.url(),
        match: [activity_logs.index.url()],
        permission: "activity-logs.view",
        icon: '<svg viewBox="0 0 24 24" class="h-5 w-5" aria-hidden="true"><path fill="currentColor" d="M7 4h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Zm2 4h6v2H9V8Zm0 4h6v2H9v-2Zm0 4h4v2H9v-2Z"/></svg>',
      },
      {
        label: "Configuración",
        href: "/configuracion",
        match: ["/configuracion"],
        permission: "configuration.view",
        icon: '<svg viewBox="0 0 24 24" class="h-5 w-5" aria-hidden="true"><path fill="currentColor" d="M12 15.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"/><path fill="currentColor" d="m19.14 12.94.7-1.48a.5.5 0 0 0-.15-.6l-1.49-1.1a.5.5 0 0 1-.17-.55 7.15 7.15 0 0 0 0-2.43.5.5 0 0 1 .17-.55l1.49-1.1a.5.5 0 0 0 .15-.6l-.7-1.48a.5.5 0 0 0-.6-.25l-1.77.63a.5.5 0 0 1-.54-.13 7.41 7.41 0 0 0-1.72-1.72.5.5 0 0 1-.13-.54l.63-1.77a.5.5 0 0 0-.25-.6l-1.48-.7a.5.5 0 0 0-.6.15l-1.1 1.49a.5.5 0 0 1-.55.17 7.15 7.15 0 0 0-2.43 0 .5.5 0 0 1-.55-.17L8.31 2.15a.5.5 0 0 0-.6-.15l-1.48.7a.5.5 0 0 0-.25.6l.63 1.77a.5.5 0 0 1-.13.54 7.41 7.41 0 0 0-1.72 1.72.5.5 0 0 1-.54.13L2.45 6.43a.5.5 0 0 0-.6.25l-.7 1.48a.5.5 0 0 0 .15.6l1.49 1.1a.5.5 0 0 1 .17.55 7.15 7.15 0 0 0 0 2.43.5.5 0 0 1-.17.55l-1.49 1.1a.5.5 0 0 0-.15.6l.7 1.48a.5.5 0 0 0 .6.25l1.77-.63a.5.5 0 0 1 .54.13 7.41 7.41 0 0 0 1.72 1.72.5.5 0 0 1 .13.54l-.63 1.77a.5.5 0 0 0 .25.6l1.48.7a.5.5 0 0 0 .6-.15l1.1-1.49a.5.5 0 0 1 .55-.17 7.15 7.15 0 0 0 2.43 0 .5.5 0 0 1 .55.17l1.1 1.49a.5.5 0 0 0 .6.15l1.48-.7a.5.5 0 0 0 .25-.6l-.63-1.77a.5.5 0 0 1 .13-.54 7.41 7.41 0 0 0 1.72-1.72.5.5 0 0 1 .54-.13l1.77.63a.5.5 0 0 0 .6-.25ZM12 18a6 6 0 1 1 0-12 6 6 0 0 1 0 12Z"/></svg>',
      },
    ],
  },
];
