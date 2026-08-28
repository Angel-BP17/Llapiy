import React, { useState, useMemo, ChangeEvent, FormEvent } from 'react';
import DashboardLayout from '@/Layouts/DashboardLayout';
import { UserTable, defaultAvatar } from '@/components/Users/UserTable';
import { UserFormFields, UserForm } from '@/components/Users/UserFormFields';
import { router } from '@inertiajs/react';
import { 
  Users, UserPlus, Shield, Building2, Eye, Pencil, Trash2, Mail, BadgeCheck, Fingerprint
} from 'lucide-react';
import { Modal } from '@/components/Modal';
import { usePermissions } from '@/hooks/use-permissions';
import Pagination from '@/components/ui/Pagination';
import { User, Area, Role, PaginationData } from '@/types/models';

interface UsersIndexProps {
  users: User[];
  areas: Area[];
  roles: Role[];
  stats: {
    totalUsers: number;
    totalRoles: number;
    totalAreas: number;
  };
  pagination: PaginationData;
  filters: {
    search?: string;
  };
}

const emptyForm: UserForm = {
  name: '',
  last_name: '',
  user_name: '',
  dni: '',
  email: '',
  password: '',
  password_confirmation: '',
  role_id: '',
  area_id: '',
  group_type_id: '',
  group_id: '',
  subgroup_id: '',
  foto_perfil: null
};

export default function Index({ users, areas, roles, stats, pagination, filters }: UsersIndexProps) {
  const { can } = usePermissions();
  const [isCreateOpen, setIsCreateOpen] = useState(false);
  const [isEditOpen, setIsEditOpen] = useState(false);
  const [selectedUser, setSelectedUser] = useState<User | null>(null);
  
  const [createForm, setCreateForm] = useState<UserForm>(emptyForm);
  const [editForm, setEditForm] = useState<UserForm>(emptyForm);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [search, setSearch] = useState(filters.search || "");

  const handleSearch = () => {
    router.get('/usuarios', { search }, { preserveState: true });
  };

  const handleCreateSubmit = (e: FormEvent) => {
    e.preventDefault();
    setIsSubmitting(true);
    router.post('/usuarios', createForm as any, {
      forceFormData: true,
      onSuccess: () => {
        setIsCreateOpen(false);
        setCreateForm(emptyForm);
      },
      onFinish: () => setIsSubmitting(false)
    });
  };

  const handleEditSubmit = (e: FormEvent) => {
    e.preventDefault();
    if (!selectedUser) return;
    setIsSubmitting(true);
    // Laravel no soporta PUT con archivos nativamente, usamos POST con _method
    router.post(`/usuarios/${selectedUser.id}`, {
      ...editForm,
      _method: 'put'
    } as any, {
      forceFormData: true,
      onSuccess: () => {
        setIsEditOpen(false);
        setSelectedUser(null);
      },
      onFinish: () => setIsSubmitting(false)
    });
  };

  const handlePageChange = (page: number) => {
    router.get('/usuarios', { ...filters, page }, { preserveState: true });
  };

  return (
    <DashboardLayout title="Usuarios">
      <div className="space-y-5">
        <header className="rounded-2xl border border-border bg-gradient-to-r from-slate-900 to-indigo-700 p-5 text-white shadow-sm">
          <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
              <p className="text-xs uppercase tracking-[0.24em] text-white/60">Administración de Personal</p>
              <h2 className="mt-2 text-2xl font-semibold">Gestión de Usuarios</h2>
              <p className="mt-1 text-sm text-white/75">Controla las cuentas de acceso, perfiles y asignaciones de los colaboradores.</p>
            </div>
            <div className="flex h-10 items-center gap-2 rounded-xl border border-white/20 bg-white/10 px-4 py-2 backdrop-blur-md">
              <Users className="h-4 w-4 text-sky-300" />
              <span className="text-xs font-black uppercase tracking-wider text-white">
                {pagination.total} <span className="text-white/60 ml-1">Usuarios</span>
              </span>
            </div>
          </div>
        </header>

        {/* Stats Grid */}
        <div className="grid grid-cols-1 gap-4 sm:grid-cols-3">
          <div className="rounded-xl border border-border bg-card p-4 shadow-sm flex items-center gap-4">
            <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-sky-500/10 text-sky-600"><Users className="h-6 w-6" /></div>
            <div><p className="text-xs text-muted-foreground font-medium uppercase">Colaboradores</p><p className="text-2xl font-bold text-foreground">{stats.totalUsers}</p></div>
          </div>
          <div className="rounded-xl border border-border bg-card p-4 shadow-sm flex items-center gap-4">
            <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-500/10 text-amber-600"><Shield className="h-6 w-6" /></div>
            <div><p className="text-xs text-muted-foreground font-medium uppercase">Roles Activos</p><p className="text-2xl font-bold text-foreground">{stats.totalRoles}</p></div>
          </div>
          <div className="rounded-xl border border-border bg-card p-4 shadow-sm flex items-center gap-4">
            <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-500/10 text-indigo-600"><Building2 className="h-6 w-6" /></div>
            <div><p className="text-xs text-muted-foreground font-medium uppercase">Áreas Registradas</p><p className="text-2xl font-bold text-foreground">{stats.totalAreas}</p></div>
          </div>
        </div>

        <div className="rounded-xl border border-border bg-card p-4 shadow-sm">
          <div className="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div className="flex flex-1 max-w-xl gap-2">
              <input type="text" value={search} onChange={(e) => setSearch(e.target.value)} className="h-10 flex-1 rounded-lg border border-border bg-background px-3 text-sm focus:ring-2 focus:ring-primary/20 outline-none" placeholder="Buscar por nombre, email o DNI..." />
              <button onClick={handleSearch} className="h-10 rounded-lg bg-primary px-6 text-sm font-semibold text-primary-foreground transition hover:opacity-90">Filtrar</button>
            </div>
            <div className="flex gap-2">
              <button onClick={() => window.open('/usuarios/pdf', '_blank')} className="h-10 rounded-lg border border-border bg-card px-4 text-sm font-semibold text-foreground hover:bg-muted transition-colors">Exportar PDF</button>
              {can('users.create') && (
                <button onClick={() => { setCreateForm(emptyForm); setIsCreateOpen(true); }} className="inline-flex h-10 items-center gap-2 rounded-lg bg-emerald-600 px-4 text-sm font-semibold text-white hover:bg-emerald-700 transition-colors shadow-lg shadow-emerald-600/20"><UserPlus className="h-4 w-4" /> Nuevo Usuario</button>
              )}
            </div>
          </div>
        </div>

        <div className="rounded-xl border border-border bg-card shadow-sm overflow-hidden mb-4">
          <UserTable 
            users={users} 
            onEdit={(u: User) => { 
              let areaId = '';
              let groupTypeId = '';
              if (u.group_id) {
                const gId = Number(u.group_id);
                for (const area of areas) {
                  for (const agt of area.area_group_types || []) {
                    if ((agt.groups || []).some((g: any) => g.id === gId)) {
                      areaId = area.id.toString();
                      groupTypeId = agt.group_type_id.toString();
                      break;
                    }
                  }
                  if (areaId) break;
                }
              }

              setSelectedUser(u); 
              setEditForm({
                ...emptyForm,
                name: u.name,
                last_name: u.last_name,
                user_name: u.user_name,
                email: u.email,
                dni: u.dni || '',
                role_id: u.roles?.[0]?.id?.toString() || '',
                area_id: areaId,
                group_type_id: groupTypeId,
                group_id: u.group_id?.toString() || '',
                subgroup_id: u.subgroup_id?.toString() || ''
              });
              setIsEditOpen(true); 
            }} 
            onDelete={(u: User) => confirm(`¿Eliminar a ${u.name}?`) && router.delete(`/usuarios/${u.id}`)} 
            onView={(u: User) => setSelectedUser(u)}
          />
        </div>

        <Pagination 
          {...pagination}
          onPageChange={handlePageChange}
          label="usuarios"
        />

        <div className="h-8" />

        {/* MODAL CREAR */}
        <Modal open={isCreateOpen} title="Registrar Nuevo Colaborador" onClose={() => setIsCreateOpen(false)} maxWidth="max-w-4xl">
          <form onSubmit={handleCreateSubmit} className="py-2">
            <UserFormFields form={createForm} setForm={setCreateForm} areas={areas} roles={roles} isSubmitting={isSubmitting} submitLabel="Crear Usuario" />
          </form>
        </Modal>

        {/* MODAL EDITAR */}
        <Modal open={isEditOpen} title={`Editar Usuario: ${selectedUser?.name}`} onClose={() => setIsEditOpen(false)} maxWidth="max-w-4xl">
          <form onSubmit={handleEditSubmit} className="py-2">
            <UserFormFields form={editForm} setForm={setEditForm} areas={areas} roles={roles} isSubmitting={isSubmitting} isEdit submitLabel="Actualizar Información" />
          </form>
        </Modal>

        {/* MODAL DETALLES */}
        <Modal open={!!selectedUser && !isEditOpen} title="Ficha del Usuario" onClose={() => setSelectedUser(null)} maxWidth="max-w-2xl">
          {selectedUser && (
            <div className="space-y-6">
              <div className="flex flex-col items-center text-center sm:flex-row sm:text-left gap-6 pb-6 border-b border-border">
                <img src={selectedUser.foto_perfil || defaultAvatar} alt={selectedUser.name} className="h-24 w-24 rounded-2xl object-cover border-4 border-muted shadow-sm" />
                <div className="space-y-1">
                  <h3 className="text-xl font-bold text-foreground">{selectedUser.name} {selectedUser.last_name}</h3>
                  <div className="flex flex-wrap justify-center sm:justify-start gap-2 pt-1">
                    <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-indigo-50 text-indigo-700 text-[10px] font-black uppercase border border-indigo-100">{selectedUser.roles?.[0]?.name || 'Sin Rol'}</span>
                    <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 text-[10px] font-black uppercase border border-slate-200">@{selectedUser.user_name}</span>
                  </div>
                </div>
              </div>

              <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div className="space-y-4">
                  <div className="flex items-center gap-3">
                    <div className="flex h-8 w-8 items-center justify-center rounded-lg bg-muted text-muted-foreground"><Mail className="h-4 w-4" /></div>
                    <div><p className="text-[10px] font-black uppercase text-muted-foreground">Email Institucional</p><p className="text-sm font-semibold">{selectedUser.email}</p></div>
                  </div>
                  <div className="flex items-center gap-3">
                    <div className="flex h-8 w-8 items-center justify-center rounded-lg bg-muted text-muted-foreground"><Fingerprint className="h-4 w-4" /></div>
                    <div><p className="text-[10px] font-black uppercase text-muted-foreground">Documento de Identidad</p><p className="text-sm font-semibold">{selectedUser.dni || 'No registrado'}</p></div>
                  </div>
                </div>
                <div className="space-y-4">
                  <div className="flex items-center gap-3">
                    <div className="flex h-8 w-8 items-center justify-center rounded-lg bg-muted text-muted-foreground"><Building2 className="h-4 w-4" /></div>
                    <div><p className="text-[10px] font-black uppercase text-muted-foreground">Área / Unidad</p><p className="text-sm font-semibold">{selectedUser.group?.area_group_type?.area?.descripcion || 'Sin asignar'}</p></div>
                  </div>
                  <div className="flex items-center gap-3">
                    <div className="flex h-8 w-8 items-center justify-center rounded-lg bg-muted text-muted-foreground"><BadgeCheck className="h-4 w-4" /></div>
                    <div><p className="text-[10px] font-black uppercase text-muted-foreground">Grupo de Trabajo</p><p className="text-sm font-semibold">{selectedUser.group?.descripcion || 'Sin asignar'}</p></div>
                  </div>
                </div>
              </div>
            </div>
          )}
        </Modal>
      </div>
    </DashboardLayout>
  );
}
