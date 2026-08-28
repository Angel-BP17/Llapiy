import React, { useMemo, useState } from 'react';
import DashboardLayout from '@/Layouts/DashboardLayout';
import { Modal } from '@/components/Modal';
import { usePage } from '@inertiajs/react';
import { 
  User, Mail, Shield, Building2, Fingerprint, BadgeCheck, Calendar, Lock, ChevronRight 
} from 'lucide-react';

const moduleLabels: Record<string, string> = {
  "users": "Gestión de Usuarios",
  "roles": "Roles y Seguridad",
  "permissions": "Permisos del Sistema",
  "documents": "Documentos Digitales",
  "blocks": "Bloques de Archivos",
  "areas": "Áreas y Oficinas",
  "group-types": "Tipos de Grupos",
  "groups": "Grupos de Trabajo",
  "subgroups": "Subgrupos / Unidades",
  "document-types": "Tipos de Documento",
  "campos": "Campos de Metadatos",
  "sections": "Secciones de Almacén",
  "andamios": "Andamios / Estantería",
  "boxes": "Cajas de Archivo",
  "activity-logs": "Registro de Auditoría",
  "inbox": "Bandeja de Entrada",
  "notifications": "Notificaciones",
  "clear-system": "Mantenimiento"
};

const actionLabels: Record<string, string> = {
  "view": "Ver listado y detalles",
  "index": "Ver listado principal",
  "create": "Crear nuevo registro",
  "update": "Editar / Actualizar",
  "delete": "Eliminar registros",
  "upload": "Subir archivos / documentos",
  "receive": "Recibir alertas",
  "pdf": "Exportar a PDF",
  "clear-system": "Limpiar datos del sistema"
};

export default function Show() {
  const { auth } = usePage().props as any;
  const [isPermsModalOpen, setIsPermsModalOpen] = useState(false);

  const profile = auth.user;
  const roles = auth.roles || [];
  const permissions = auth.permissions || [];

  const groupedPermissions = useMemo(() => {
    const groups: Record<string, string[]> = {};
    permissions.forEach((perm: string) => {
      let [module, action] = perm.split(".");
      if (!action) { action = module; module = "clear-system"; }
      if (!groups[module]) groups[module] = [];
      groups[module].push(action);
    });
    return groups;
  }, [permissions]);

  const userPhoto = profile.foto_perfil 
    ? `/storage/${profile.foto_perfil}` 
    : "/img/default-avatar.png";

  return (
    <DashboardLayout title="Mi Perfil">
      <div className="mx-auto max-w-4xl space-y-6">
        {/* HEADER PREMIUM */}
        <section className="overflow-hidden rounded-2xl border border-border bg-card shadow-sm">
          <div className="h-32 bg-gradient-to-r from-blue-600 to-indigo-700" />
          <div className="px-6 pb-6">
            <div className="relative -mt-16 flex flex-col items-center sm:flex-row sm:items-end sm:gap-6">
              <div className="h-32 w-32 rounded-2xl border-4 border-card bg-card overflow-hidden shadow-md">
                <img src={userPhoto} alt={profile.name} className="h-full w-full object-cover" />
              </div>
              <div className="mt-4 flex-1 text-center sm:mb-2 sm:text-left">
                <h2 className="text-2xl font-bold text-foreground">{profile.name} {profile.last_name}</h2>
                <p className="text-sm text-muted-foreground">@{profile.user_name}</p>
              </div>
              <div className="mt-4 sm:mb-2 flex flex-col gap-2">
                <span className="inline-flex items-center gap-1.5 rounded-full bg-primary/10 px-3 py-1 text-xs font-semibold text-primary">
                  <BadgeCheck className="h-3.5 w-3.5" /> Cuenta verificada
                </span>
                <button onClick={() => setIsPermsModalOpen(true)} className="inline-flex items-center gap-1.5 text-xs font-medium text-muted-foreground hover:text-primary transition-colors">
                  <Lock className="h-3.5 w-3.5" /> Ver mis permisos
                </button>
              </div>
            </div>
          </div>
        </section>

        <div className="animate-in fade-in slide-in-from-bottom-4 duration-500">
          <div className="grid gap-6 md:grid-cols-2">
            <article className="rounded-2xl border border-border bg-card p-6 shadow-sm">
              <h3 className="flex items-center gap-2 text-sm font-semibold uppercase tracking-wider text-muted-foreground">
                <Fingerprint className="h-4 w-4" /> Información de la cuenta
              </h3>
              <div className="mt-4 space-y-4">
                <div className="flex items-start gap-3">
                  <div className="mt-0.5 rounded-lg bg-muted p-2 text-muted-foreground"><User className="h-4 w-4" /></div>
                  <div><p className="text-xs text-muted-foreground">Nombres y apellidos</p><p className="text-sm font-medium text-foreground">{profile.name} {profile.last_name}</p></div>
                </div>
                <div className="flex items-start gap-3">
                  <div className="mt-0.5 rounded-lg bg-muted p-2 text-muted-foreground"><Mail className="h-4 w-4" /></div>
                  <div><p className="text-xs text-muted-foreground">Correo electrónico</p><p className="text-sm font-medium text-foreground">{profile.email}</p></div>
                </div>
                <div className="flex items-start gap-3">
                  <div className="mt-0.5 rounded-lg bg-muted p-2 text-muted-foreground"><Calendar className="h-4 w-4" /></div>
                  <div><p className="text-xs text-muted-foreground">DNI / Identificación</p><p className="text-sm font-medium text-foreground">{profile.dni || "No registrado"}</p></div>
                </div>
              </div>
            </article>

            <article className="rounded-2xl border border-border bg-card p-6 shadow-sm">
              <h3 className="flex items-center gap-2 text-sm font-semibold uppercase tracking-wider text-muted-foreground">
                <Shield className="h-4 w-4" /> Rol y Organización
              </h3>
              <div className="mt-4 space-y-4">
                <div className="flex items-start gap-3">
                  <div className="mt-0.5 rounded-lg bg-muted p-2 text-muted-foreground"><BadgeCheck className="h-4 w-4" /></div>
                  <div><p className="text-xs text-muted-foreground">Rol principal</p><p className="text-sm font-medium text-foreground">{roles.length > 0 ? roles.join(", ") : "Sin rol asignado"}</p></div>
                </div>
                <div className="flex items-start gap-3">
                  <div className="mt-0.5 rounded-lg bg-muted p-2 text-muted-foreground"><Building2 className="h-4 w-4" /></div>
                  <div>
                    <p className="text-xs text-muted-foreground">Área / Organización</p>
                    <p className="text-sm font-medium text-foreground">{profile.area || "Sin área"}</p>
                  </div>
                </div>
              </div>
            </article>
          </div>
        </div>

        <Modal open={isPermsModalOpen} onClose={() => setIsPermsModalOpen(false)} title="Mis Permisos y Capacidades" maxWidth="max-w-3xl">
          <div className="space-y-6">
            <p className="text-sm text-muted-foreground">Listado de acciones permitidas agrupadas por módulo.</p>
            <div className="grid gap-4 sm:grid-cols-2">
              {Object.entries(groupedPermissions).map(([module, actions]) => (
                <div key={module} className="rounded-xl border border-border bg-background p-4">
                  <div className="flex items-center gap-2 mb-3 border-b border-border pb-2">
                    <div className="h-2 w-2 rounded-full bg-primary" />
                    <h4 className="text-sm font-bold text-foreground">{moduleLabels[module] || module.toUpperCase()}</h4>
                  </div>
                  <ul className="space-y-2">
                    {actions.map((action) => (
                      <li key={action} className="flex items-start gap-2 text-xs text-muted-foreground">
                        <ChevronRight className="h-3 w-3 mt-0.5 text-primary/60" />
                        <span>{actionLabels[action] || action}</span>
                      </li>
                    ))}
                  </ul>
                </div>
              ))}
            </div>
          </div>
        </Modal>
      </div>
    </DashboardLayout>
  );
}
