import React, { useState, useMemo, FormEvent } from 'react';
import DashboardLayout from '@/Layouts/DashboardLayout';
import { Modal } from '@/components/Modal';
import { router } from '@inertiajs/react';
import { 
  KeyRound, Pencil, Plus, ShieldCheck, Trash2, ChevronRight, Eraser 
} from 'lucide-react';
import { usePermissions } from '@/hooks/use-permissions';
import Pagination from '@/components/ui/Pagination';
import { Role, PaginationData } from '@/types/models';

interface RolesIndexProps {
  roles: Role[];
  permissions: string[];
  pagination: PaginationData;
  filters: {
    search?: string;
  };
}

type RoleForm = { name: string; permissions: string[] };

const moduleLabels: Record<string, string> = {
  users: "Usuarios",
  roles: "Roles",
  permissions: "Permisos",
  documents: "Documentos",
  blocks: "Bloques",
  areas: "Áreas",
  "group-types": "Tipos de Grupos",
  groups: "Grupos",
  subgroups: "Subgrupos",
  "document-types": "Tipos de Documento",
  campos: "Campos de Metadatos",
  sections: "Secciones",
  andamios: "Andamios",
  boxes: "Cajas",
  "activity-logs": "Auditoría",
  inbox: "Bandeja de Entrada",
  notifications: "Notificaciones",
  "clear-system": "Mantenimiento"
};

const actionLabels: Record<string, string> = {
  view: "Ver",
  create: "Crear",
  update: "Editar",
  delete: "Eliminar",
  upload: "Subir Archivos",
  receive: "Recibir"
};

const roleLabels: Record<string, string> = {
  ADMINISTRADOR: "Administrador",
};

const roleTemplates = [
  {
    id: "archivo-central",
    label: "Encargado de Archivo Central",
    description: 'Acceso completo a "Bandeja", "Almacenamiento", "Documentos", "Bloques" y "Notificaciones".',
    modules: ["inbox", "sections", "andamios", "boxes", "documents", "blocks", "notifications"],
  },
  {
    id: "colaborador",
    label: "Colaborador Documental",
    description: 'Acceso a Documentos y Bloques (excepto ver todo el archivo).',
    modules: ["documents", "blocks"],
    exclude: ["documents.view.all", "blocks.view.all"]
  },
];

function getPermissionLabel(key: string) {
  if (key === "clear-system") return "Limpiar Sistema";
  const parts = key.split(".");
  const [module, action, scope] = parts;
  
  if (!action) return key;

  const baseLabel = `${actionLabels[action] || action} ${moduleLabels[module] || module}`;
  
  if (scope) {
    const scopeLabels: Record<string, string> = {
      all: " (Todos)",
      group: " (Grupo)",
      own: " (Propios)"
    };
    return `${baseLabel}${scopeLabels[scope] || ` (${scope})`}`;
  }

  return baseLabel;
}

const emptyForm: RoleForm = { name: "", permissions: [] };

export default function Index({ roles, permissions, pagination, filters }: RolesIndexProps) {
  const { can } = usePermissions();
  const [isCreateOpen, setIsCreateOpen] = useState(false);
  const [isEditOpen, setIsEditOpen] = useState(false);
  const [selectedRole, setSelectedRole] = useState<any>(null);
  
  const [createForm, setCreateForm] = useState<RoleForm>(emptyForm);
  const [editForm, setEditForm] = useState<RoleForm>(emptyForm);
  const [search, setSearch] = useState(filters.search || "");
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [createErr, setCreateErr] = useState("");
  const [editErr, setEditErr] = useState("");

  const permissionGroups = useMemo(() => {
    const grouped = new Map<string, string[]>();
    permissions.forEach((key) => {
      let module = key.split(".")[0];
      if (key === "clear-system") module = "clear-system";
      if (!grouped.has(module)) grouped.set(module, []);
      grouped.get(module)!.push(key);
    });

    const result: any[] = [];
    grouped.forEach((items, module) => {
      result.push({
        module,
        moduleLabel: moduleLabels[module] || module,
        permissions: items.map(k => ({ key: k, label: getPermissionLabel(k) }))
      });
    });

    return result.sort((a, b) => a.moduleLabel.localeCompare(b.moduleLabel));
  }, [permissions]);

  const togglePermission = (form: RoleForm, setFn: any, key: string) => {
    const has = form.permissions.includes(key);
    setFn({
      ...form,
      permissions: has ? form.permissions.filter(p => p !== key) : [...form.permissions, key]
    });
  };

  const applyTemplate = (form: RoleForm, setFn: any, template: any) => {
    const nextPerms = new Set(form.permissions);
    permissions.forEach(p => {
      const mod = p.split(".")[0];
      if (template.modules.includes(mod)) {
        if (!template.exclude?.includes(p)) {
          nextPerms.add(p);
        }
      }
    });
    setFn({ ...form, permissions: Array.from(nextPerms) });
  };

  const handleSubmit = (e: FormEvent, form: RoleForm, isEdit: boolean) => {
    e.preventDefault();
    if (!form.name.trim()) return isEdit ? setEditErr("Ingrese el nombre") : setCreateErr("Ingrese el nombre");
    if (form.permissions.length === 0) return isEdit ? setEditErr("Seleccione permisos") : setCreateErr("Seleccione permisos");
    
    setIsSubmitting(true);
    const url = isEdit ? `/roles/${selectedRole.id}/permissions` : '/roles';
    const method = isEdit ? 'put' : 'post';

    const normalizedPermissions = form.permissions.map(p => typeof p === 'string' ? p : (p as any).name);

    router[method](url, { ...form, permissions: normalizedPermissions } as any, {
      onSuccess: () => {
        setIsCreateOpen(false);
        setIsEditOpen(false);
        setCreateForm(emptyForm);
        setEditForm(emptyForm);
        setCreateErr("");
        setEditErr("");
      },
      onError: (err: any) => isEdit ? setEditErr(Object.values(err)[0] as string) : setCreateErr(Object.values(err)[0] as string),
      onFinish: () => setIsSubmitting(false)
    });
  };

  const handlePageChange = (page: number) => {
    router.get('/roles', { ...filters, page }, { preserveState: true });
  };

  return (
    <DashboardLayout title="Roles">
      <div className="space-y-5">
        <header className="rounded-2xl border border-border bg-gradient-to-r from-slate-900 to-indigo-700 p-5 text-white shadow-sm">
          <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
              <p className="text-xs uppercase tracking-[0.24em] text-white/60">Seguridad y Acceso</p>
              <h2 className="mt-2 text-2xl font-semibold">Roles del Sistema</h2>
              <p className="mt-1 text-sm text-white/75">Administra los permisos y niveles de acceso para los diferentes tipos de usuarios.</p>
            </div>
            <div className="flex h-10 items-center gap-2 rounded-xl border border-white/20 bg-white/10 px-4 py-2 backdrop-blur-md">
              <ShieldCheck className="h-4 w-4 text-amber-300" />
              <span className="text-xs font-black uppercase tracking-wider text-white">
                {pagination.total} <span className="text-white/60 ml-1">Roles</span>
              </span>
            </div>
            </div>
        </header>

        <div className="grid gap-3 sm:grid-cols-2">
          <article className="rounded-xl border border-border bg-card p-4 shadow-sm flex items-center gap-3">
            <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-primary/10 text-primary">
              <ShieldCheck className="h-5 w-5" />
            </div>
            <div><p className="text-xs text-muted-foreground">Total de roles</p><p className="mt-1 text-2xl font-semibold text-foreground">{pagination.total}</p></div>
          </article>
          <article className="rounded-xl border border-border bg-card p-4 shadow-sm flex items-center gap-3">
            <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-primary/10 text-primary">
              <KeyRound className="h-5 w-5" />
            </div>
            <div><p className="text-xs text-muted-foreground">Capacidades del sistema</p><p className="mt-1 text-2xl font-semibold text-foreground">{permissions.length}</p></div>
          </article>
        </div>

        <div className="rounded-xl border border-border bg-card p-4 shadow-sm">
          <div className="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div className="w-full lg:max-w-xl">
              <label htmlFor="role-search" className="mb-2 block text-sm font-medium text-foreground">Buscar rol</label>
              <div className="flex flex-col gap-2 sm:flex-row">
                <input id="role-search" type="text" value={search} onChange={(e) => setSearch(e.target.value)} className="h-10 flex-1 rounded-lg border border-border bg-background px-3 text-sm" placeholder="Ej. Administrador..." />
                <button type="button" onClick={() => router.get('/roles', { search }, { preserveState: true })} className="h-10 rounded-lg bg-primary px-4 text-sm font-semibold text-primary-foreground">Filtrar</button>
                <button type="button" onClick={() => { setSearch(""); router.get('/roles'); }} className="h-10 rounded-lg border border-border bg-card px-4 text-sm font-semibold text-foreground">Limpiar</button>
              </div>
            </div>
            {can('roles.create') && (
              <button type="button" onClick={() => { setCreateForm(emptyForm); setCreateErr(""); setIsCreateOpen(true); }} className="inline-flex h-10 items-center gap-2 rounded-lg bg-emerald-600 px-4 text-sm font-semibold text-white"><Plus className="h-4 w-4" /> Nuevo Rol</button>
            )}
          </div>
        </div>

        <div className="overflow-hidden rounded-xl border border-border bg-card shadow-sm mb-4">
          <div className="overflow-x-auto">
            <table className="w-full min-w-[800px] text-sm">
              <thead className="bg-muted/60">
                <tr className="text-left text-xs uppercase tracking-wide text-muted-foreground"><th className="px-4 py-3">#</th><th className="px-4 py-3">Nombre del Rol</th><th className="px-4 py-3">Permisos Asignados</th><th className="px-4 py-3 text-right">Acciones</th></tr>
              </thead>
              <tbody>
                {roles.map((role: Role, i: number) => (
                  <tr key={role.id} className="border-t border-border hover:bg-muted/30 transition-colors">
                    <td className="px-4 py-3 text-muted-foreground">{((pagination.current_page - 1) * 10) + i + 1}</td>
                    <td className="px-4 py-3 font-semibold text-foreground">{roleLabels[role.name] || role.name}</td>
                    <td className="px-4 py-3"><span className="inline-flex items-center gap-1.5 rounded-full border border-border bg-muted/50 px-2.5 py-0.5 text-xs font-medium"><KeyRound className="h-3 w-3" /> {role.permissions?.length || 0} acciones</span></td>
                    <td className="px-4 py-3 text-right">
                      <div className="flex justify-end gap-2">
                        <button type="button" onClick={() => { 
                          setSelectedRole(role); 
                          setEditForm({ name: role.name, permissions: role.permission_list || [] }); 
                          setEditErr(""); 
                          setIsEditOpen(true); 
                        }} className="inline-flex items-center gap-1.5 rounded-md bg-amber-500 px-3 py-1.5 text-xs font-semibold text-white"><Pencil className="h-3.5 w-3.5" /> Editar</button>
                        <button 
                          type="button" 
                          onClick={() => confirm(`¿Eliminar ${role.name}?`) && router.delete(`/roles/${role.id}`)} 
                          disabled={role.name.toUpperCase() === "ADMINISTRADOR" || (role.users_count ?? 0) > 0} 
                          className="inline-flex items-center gap-1.5 rounded-md bg-red-600 px-3 py-1.5 text-xs font-semibold text-white disabled:opacity-50" 
                          title={
                            role.name.toUpperCase() === "ADMINISTRADOR" 
                              ? "El rol Administrador no puede ser eliminado" 
                              : (role.users_count ?? 0) > 0 
                                ? "No se puede eliminar un rol que tiene usuarios asignados" 
                                : ""
                          }
                        >
                          <Trash2 className="h-3.5 w-3.5" />
                        </button>
                      </div>
                    </td>
                  </tr>
                ))}
                {roles.length === 0 && (
                  <tr>
                    <td colSpan={4} className="px-4 py-16 text-center">
                      <div className="flex flex-col items-center justify-center max-w-md mx-auto">
                        <div className="flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-500/10 text-indigo-600 mb-4 animate-bounce">
                          <ShieldCheck className="h-8 w-8" />
                        </div>
                        <h3 className="text-lg font-semibold text-foreground">No se encontraron roles</h3>
                        <p className="mt-1 text-sm text-muted-foreground">
                          No hay roles definidos en la base de datos o ningún registro coincide con la búsqueda.
                        </p>
                      </div>
                    </td>
                  </tr>
                )}
              </tbody>
            </table>
          </div>
        </div>

        <Pagination 
          {...pagination}
          onPageChange={handlePageChange}
          label="roles"
        />

        <div className="h-8" /> {/* Espaciador inferior */}

        {/* MODAL CREAR */}
        <Modal open={isCreateOpen} title="Crear Nuevo Rol" onClose={() => setIsCreateOpen(false)} maxWidth="max-w-5xl">
          <form onSubmit={(e) => handleSubmit(e, createForm, false)} className="space-y-6">
            {createErr && <div className="rounded-lg border border-red-200 bg-red-50 p-3 text-xs text-red-700">{createErr}</div>}
            <div className="grid gap-6 lg:grid-cols-3">
              <div className="lg:col-span-1 space-y-4">
                <div className="space-y-1.5">
                  <label htmlFor="role_identifier" className="text-xs font-black uppercase text-muted-foreground">Identificador</label>
                  <input id="role_identifier" type="text" value={createForm.name} onChange={(e) => setCreateForm({ ...createForm, name: e.target.value.toUpperCase().replace(/\s/g, "_") })} className="h-10 w-full rounded-lg border border-border bg-background px-3 text-sm font-mono" placeholder="EJ_ROL_NUEVO" required />
                </div>
                <div className="space-y-3">
                  <p className="text-xs font-black uppercase text-muted-foreground">Plantillas Rápidas</p>
                  <div className="space-y-2">
                    {roleTemplates.map(t => (
                      <button key={t.id} type="button" onClick={() => applyTemplate(createForm, setCreateForm, t)} className="w-full text-left p-3 rounded-xl border border-border bg-muted/30 hover:bg-primary/5 transition-all group">
                        <div className="flex items-center gap-2 font-bold text-xs text-foreground group-hover:text-primary"><ShieldCheck className="h-3.5 w-3.5" /> {t.label}</div>
                        <p className="mt-1 text-[10px] text-muted-foreground leading-tight">{t.description}</p>
                      </button>
                    ))}
                  </div>
                </div>
              </div>
              <div className="lg:col-span-2 space-y-4">
                <p className="text-xs font-black uppercase text-muted-foreground">Matriz de Permisos</p>
                <div className="grid gap-4 sm:grid-cols-2 max-h-[50vh] overflow-y-auto pr-2 custom-scrollbar">
                  {permissionGroups.map(group => (
                    <div key={group.module} className="rounded-xl border border-border bg-muted/20 p-4">
                      <h4 className="mb-3 text-[10px] font-black uppercase text-indigo-600 flex items-center gap-2"><ChevronRight className="h-3 w-3" /> {group.moduleLabel}</h4>
                      <div className="space-y-2">
                        {group.permissions.map((p: any) => (
                          <label key={p.key} className="flex items-center gap-2 cursor-pointer group">
                            <input type="checkbox" checked={createForm.permissions.includes(p.key)} onChange={() => togglePermission(createForm, setCreateForm, p.key)} className="h-4 w-4 rounded border-border text-primary" />
                            <span className="text-xs text-muted-foreground group-hover:text-foreground transition-colors">{p.label}</span>
                          </label>
                        ))}
                      </div>
                    </div>
                  ))}
                </div>
              </div>
            </div>
            <div className="flex justify-end gap-3 border-t border-border pt-4">
              <button type="button" onClick={() => setCreateForm({ ...createForm, permissions: [] })} className="inline-flex items-center gap-2 text-xs font-bold text-muted-foreground hover:text-red-600"><Eraser className="h-3.5 w-3.5" /> Limpiar todo</button>
              <button type="submit" disabled={isSubmitting} className="rounded-lg bg-primary px-8 py-2 text-sm font-bold text-primary-foreground">Guardar Rol</button>
            </div>
          </form>
        </Modal>

        {/* MODAL EDITAR */}
        <Modal open={isEditOpen} title={`Editar Rol: ${selectedRole?.name}`} onClose={() => setIsEditOpen(false)} maxWidth="max-w-5xl">
          <form onSubmit={(e) => handleSubmit(e, editForm, true)} className="space-y-6">
            {editErr && <div className="rounded-lg border border-red-200 bg-red-50 p-3 text-xs text-red-700">{editErr}</div>}
            <div className="grid gap-6 lg:grid-cols-3">
              <div className="lg:col-span-1 space-y-4">
                <div className="p-4 rounded-xl border border-indigo-100 bg-indigo-50/30">
                  <div className="flex items-center gap-2 font-bold text-indigo-700 mb-1 text-sm"><ShieldCheck className="h-4 w-4" /> Modo Edición</div>
                  <p className="text-xs text-indigo-600/80 leading-relaxed">Estás modificando los permisos del rol <strong>{selectedRole?.name}</strong>. Los cambios afectarán a todos los usuarios vinculados.</p>
                </div>
                <div className="space-y-3">
                  <p className="text-xs font-black uppercase text-muted-foreground">Añadir Bloques</p>
                  <div className="space-y-2">
                    {roleTemplates.map(t => (
                      <button key={t.id} type="button" onClick={() => applyTemplate(editForm, setEditForm, t)} className="w-full text-left p-3 rounded-xl border border-border bg-muted/30 hover:bg-primary/5 transition-all group">
                        <div className="flex items-center gap-2 font-bold text-xs text-foreground group-hover:text-primary"><ShieldCheck className="h-3.5 w-3.5" /> {t.label}</div>
                        <p className="mt-1 text-[10px] text-muted-foreground leading-tight">{t.description}</p>
                      </button>
                    ))}
                  </div>
                </div>
              </div>
              <div className="lg:col-span-2 space-y-4">
                <p className="text-xs font-black uppercase text-muted-foreground">Matriz de Permisos</p>
                <div className="grid gap-4 sm:grid-cols-2 max-h-[50vh] overflow-y-auto pr-2 custom-scrollbar">
                  {permissionGroups.map(group => (
                    <div key={group.module} className="rounded-xl border border-border bg-muted/20 p-4">
                      <h4 className="mb-3 text-[10px] font-black uppercase text-indigo-600 flex items-center gap-2"><ChevronRight className="h-3 w-3" /> {group.moduleLabel}</h4>
                      <div className="space-y-2">
                        {group.permissions.map((p: any) => (
                          <label key={p.key} className="flex items-center gap-2 cursor-pointer group">
                            <input type="checkbox" checked={editForm.permissions.includes(p.key)} onChange={() => togglePermission(editForm, setEditForm, p.key)} className="h-4 w-4 rounded border-border text-primary" />
                            <span className="text-xs text-muted-foreground group-hover:text-foreground transition-colors">{p.label}</span>
                          </label>
                        ))}
                      </div>
                    </div>
                  ))}
                </div>
              </div>
            </div>
            <div className="flex justify-end gap-3 border-t border-border pt-4">
              <button type="button" onClick={() => setEditForm({ ...editForm, permissions: [] })} className="inline-flex items-center gap-2 text-xs font-bold text-muted-foreground hover:text-red-600"><Eraser className="h-3.5 w-3.5" /> Quitar todos</button>
              <button type="submit" disabled={isSubmitting} className="rounded-lg bg-primary px-8 py-2 text-sm font-bold text-primary-foreground">Actualizar Permisos</button>
            </div>
          </form>
        </Modal>
      </div>
    </DashboardLayout>
  );
}
