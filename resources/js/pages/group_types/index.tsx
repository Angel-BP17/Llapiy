import React, { useState, FormEvent } from 'react';
import DashboardLayout from '@/Layouts/DashboardLayout';
import { Modal } from '@/components/Modal';
import { usePage, router } from '@inertiajs/react';
import { Layers, Plus, Pencil, Trash2, ChevronLeft, ChevronRight, AlertCircle } from 'lucide-react';
import groupTypesRoutes from '@/routes/group_types';

interface GroupTypesIndexProps {
  groupTypes: {
    data: any[];
    total: number;
    current_page: number;
    last_page: number;
    from: number;
  };
  filters: any;
}

type GroupTypeForm = {
  descripcion: string;
  abreviacion: string;
};

const emptyForm: GroupTypeForm = { descripcion: "", abreviacion: "" };

export default function Index({ groupTypes, filters }: GroupTypesIndexProps) {
  const { auth } = usePage().props as any;
  const [search, setSearch] = useState(filters?.search || "");
  
  const [createOpen, setCreateOpen] = useState(false);
  const [editOpen, setEditOpen] = useState(false);
  const [selected, setSelected] = useState<any>(null);
  
  const [form, setForm] = useState<GroupTypeForm>(emptyForm);
  const [error, setError] = useState("");
  const [isSubmitting, setIsSubmitting] = useState(false);

  const canCreateGroupType = auth.permissions?.includes("group-types.create") || auth.roles?.some((r: any) => (typeof r === 'string' ? r : r?.name || '').toUpperCase() === 'ADMINISTRADOR');

  const handleSearch = () => {
    router.get(groupTypesRoutes.index.url(), { search }, { preserveState: true });
  };

  const handlePageChange = (page: number) => {
    router.get(groupTypesRoutes.index.url(), { search, page }, { preserveState: true });
  };

  const handleSubmit = (e: FormEvent) => {
    e.preventDefault();
    if (!form.descripcion.trim()) return setError("Ingrese la descripción.");
    
    setIsSubmitting(true);
    const url = editOpen 
        ? groupTypesRoutes.update.url({ id: selected.id }) 
        : groupTypesRoutes.store.url();
    const method = editOpen ? 'put' : 'post';

    router[method](url, {
      descripcion: form.descripcion.trim(),
      abreviacion: form.abreviacion.trim() || undefined
    }, {
      onSuccess: () => { setCreateOpen(false); setEditOpen(false); setError(""); },
      onFinish: () => setIsSubmitting(false)
    });
  };

  const deleteRecord = (record: any) => {
    if (record.groups_count > 0) return;
    if (confirm(`¿Eliminar el tipo de grupo ${record.descripcion}?`)) {
      router.delete(groupTypesRoutes.destroy.url({ id: record.id }));
    }
  };

  return (
    <DashboardLayout title="Tipos de Grupos">
      <div className="space-y-5">
        <header className="rounded-2xl border border-border bg-gradient-to-r from-slate-900 to-cyan-700 p-5 text-white shadow-sm">
          <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
              <p className="text-xs uppercase tracking-[0.24em] text-white/60">Catálogo institucional</p>
              <h2 className="mt-2 text-2xl font-semibold">Gestión de tipos de grupos</h2>
              <p className="mt-1 text-sm text-white/75">Define y organiza categorías para la estructura de áreas y grupos.</p>
            </div>
            <span className="rounded-full border border-white/25 bg-white/10 px-3 py-1 text-xs font-semibold">
              {groupTypes.total} registros
            </span>
          </div>
        </header>

        <article className="rounded-xl border border-border bg-card p-4 shadow-sm flex items-center gap-3">
          <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-primary/10 text-primary">
            <Layers className="h-5 w-5" />
          </div>
          <div>
            <p className="text-xs text-muted-foreground">Tipos registrados</p>
            <p className="mt-1 text-2xl font-semibold text-foreground">{groupTypes.total}</p>
          </div>
        </article>

        <div className="rounded-xl border border-border bg-card p-4 shadow-sm">
          <div className="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div className="w-full lg:max-w-xl">
              <label htmlFor="gt-search-input" className="mb-2 block text-sm font-medium text-foreground">Buscar tipo</label>
              <div className="flex flex-col gap-2 sm:flex-row">
                <input 
                  id="gt-search-input" 
                  type="text" 
                  placeholder="Descripción o abreviación..." 
                  value={search} 
                  onChange={(e) => setSearch(e.target.value)} 
                  className="h-10 flex-1 rounded-lg border border-border bg-background px-3 text-sm" 
                />
                <button id="gt-apply-filter" type="button" onClick={handleSearch} className="h-10 rounded-lg bg-primary px-4 text-sm font-semibold text-primary-foreground">Filtrar</button>
                <button type="button" onClick={() => { setSearch(""); router.get(groupTypesRoutes.index.url()); }} className="h-10 rounded-lg border border-border bg-card px-4 text-sm font-semibold text-foreground">Limpiar</button>
              </div>
            </div>
            {canCreateGroupType && (
              <button id="gt-create-button" onClick={() => { setForm(emptyForm); setError(""); setCreateOpen(true); }} className="inline-flex h-10 items-center gap-2 rounded-lg bg-emerald-600 px-4 text-sm font-semibold text-white transition-opacity hover:opacity-90">
                <Plus className="h-4 w-4" />Nuevo Tipo de Grupo
              </button>
            )}
          </div>
        </div>

        <div className="overflow-hidden rounded-xl border border-border bg-card shadow-sm">
          <div className="overflow-x-auto">
            <table id="gt-table" className="w-full min-w-[800px] text-sm">
              <thead className="bg-muted/60">
                <tr className="text-left text-xs uppercase tracking-wide text-muted-foreground">
                  <th className="px-4 py-3">#</th>
                  <th className="px-4 py-3">Descripción</th>
                  <th className="px-4 py-3">Abreviación</th>
                  <th className="px-4 py-3">Grupos Asociados</th>
                  <th className="px-4 py-3 text-right">Acciones</th>
                </tr>
              </thead>
              <tbody>
                {groupTypes.data.map((record: any, i: number) => (
                  <tr key={record.id} className="border-t border-border transition-colors hover:bg-muted/30">
                    <td className="px-4 py-3 text-muted-foreground">{(groupTypes.from || 1) + i}</td>
                    <td className="px-4 py-3 font-semibold text-foreground">{record.descripcion}</td>
                    <td className="px-4 py-3 font-mono text-xs uppercase text-muted-foreground">{record.abreviacion || "-"}</td>
                    <td className="px-4 py-3">{record.groups_count || 0} grupos</td>
                    <td className="px-4 py-3 text-right">
                      <div className="flex justify-end gap-2">
                        <button onClick={() => { 
                          setSelected(record); 
                          setForm({ descripcion: record.descripcion, abreviacion: record.abreviacion || "" }); 
                          setError(""); 
                          setEditOpen(true); 
                        }} className="inline-flex items-center gap-1.5 rounded-md bg-amber-500 px-3 py-1.5 text-xs font-semibold text-white">
                          <Pencil className="h-3.5 w-3.5" /> Editar
                        </button>
                        <button 
                          onClick={() => deleteRecord(record)} 
                          disabled={record.groups_count > 0} 
                          className="inline-flex items-center gap-1.5 rounded-md bg-red-600 px-3 py-1.5 text-xs font-semibold text-white disabled:opacity-50 disabled:cursor-not-allowed"
                          title={record.groups_count > 0 ? "No se puede eliminar porque existen grupos asociados a esta categoría" : ""}
                        >
                          <Trash2 className="h-3.5 w-3.5" />
                        </button>
                      </div>
                    </td>
                  </tr>
                ))}
                {groupTypes.data.length === 0 && (
                  <tr>
                    <td colSpan={5} className="px-4 py-16 text-center">
                      <div className="flex flex-col items-center justify-center max-w-md mx-auto">
                        <div className="flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-500/10 text-indigo-600 mb-4 animate-bounce">
                          <Layers className="h-8 w-8" />
                        </div>
                        <h3 className="text-lg font-semibold text-foreground">No se encontraron tipos de grupo</h3>
                        <p className="mt-1 text-sm text-muted-foreground">
                          No hay tipos de grupo registrados en la base de datos o ningún registro coincide con la búsqueda.
                        </p>
                      </div>
                    </td>
                  </tr>
                )}
              </tbody>
            </table>
          </div>
        </div>

        <div className="flex items-center justify-between rounded-xl border border-border bg-card px-4 py-3 text-sm shadow-sm">
          <p className="text-muted-foreground">Página {groupTypes.current_page} de {groupTypes.last_page}</p>
          <div className="flex items-center gap-2">
            <button type="button" disabled={groupTypes.current_page <= 1} onClick={() => handlePageChange(groupTypes.current_page - 1)} className="rounded-md border border-border px-3 py-1.5 text-xs font-semibold disabled:opacity-50">Anterior</button>
            <button type="button" disabled={groupTypes.current_page >= groupTypes.last_page} onClick={() => handlePageChange(groupTypes.current_page + 1)} className="rounded-md border border-border px-3 py-1.5 text-xs font-semibold disabled:opacity-50">Siguiente</button>
          </div>
        </div>

        <Modal open={createOpen || editOpen} title={editOpen ? "Editar Tipo de Grupo" : "Crear Tipo de Grupo"} onClose={() => { setCreateOpen(false); setEditOpen(false); }}>
          <form onSubmit={handleSubmit} className="space-y-4">
            {error && <div className="rounded-lg border border-red-200 bg-red-50 p-3 text-xs text-red-700 flex items-center gap-2"><AlertCircle className="h-4 w-4" />{error}</div>}
            <div className="space-y-3">
              <div>
                <label htmlFor="gt_descripcion" className="text-xs font-bold uppercase text-muted-foreground">Descripción</label>
                <input id="gt_descripcion" type="text" value={form.descripcion} onChange={(e) => setForm(p => ({ ...p, descripcion: e.target.value }))} className="h-10 w-full rounded-lg border border-border bg-background px-3 text-sm" placeholder="Ej. Gerencia, Oficina, Unidad..." required />
              </div>
              <div>
                <label htmlFor="gt_abreviacion" className="text-xs font-bold uppercase text-muted-foreground">Abreviación (Opcional)</label>
                <input id="gt_abreviacion" type="text" value={form.abreviacion} onChange={(e) => setForm(p => ({ ...p, abreviacion: e.target.value }))} className="h-10 w-full rounded-lg border border-border bg-background px-3 text-sm" placeholder="Ej. GER, OFI, UNI..." />
              </div>
            </div>
            <div className="flex justify-end pt-2">
              <button id="gt-save-button" type="submit" disabled={isSubmitting} className="rounded-lg bg-primary px-6 py-2 text-sm font-bold text-primary-foreground disabled:opacity-50">
                {editOpen ? "Actualizar" : "Guardar Tipo"}
              </button>
            </div>
          </form>
        </Modal>
      </div>
    </DashboardLayout>
  );
}
