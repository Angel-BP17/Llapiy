import React, { useState, FormEvent } from 'react';
import DashboardLayout from '@/Layouts/DashboardLayout';
import { Modal } from '@/components/Modal';
import { router } from '@inertiajs/react';
import { 
  ListChecks, Pencil, Plus, Trash2
} from 'lucide-react';
import camposRoutes from '@/routes/campos';
import { CampoFormFields, CampoForm } from '@/components/campos/campo-form-fields';
import { usePermissions } from '@/hooks/use-permissions';
import Pagination from '@/components/ui/Pagination';

interface CamposIndexProps {
  campos: {
    data: any[];
    total: number;
    current_page: number;
    last_page: number;
    from: number;
  };
  totalDocumentTypes: number;
  pagination: any;
  filters?: any;
}

const emptyForm: CampoForm = {
  name: "",
  data_type: "string",
  is_nullable: true,
  length: "",
  allow_negative: false,
  allow_zero: true,
  enum_values_text: ""
};

export default function Index({ campos, totalDocumentTypes, filters = {} }: CamposIndexProps) {
  const { can } = usePermissions();
  const [createOpen, setCreateOpen] = useState(false);
  const [editOpen, setEditOpen] = useState(false);
  const [selected, setSelected] = useState<any>(null);
  const [form, setForm] = useState<CampoForm>(emptyForm);
  const [error, setError] = useState("");
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [search, setSearch] = useState(filters?.search || "");

  const handleSearch = () => {
    router.get(camposRoutes.index.url(), { search }, { preserveState: true });
  };

  const handleSubmit = (e: FormEvent) => {
    e.preventDefault();
    if (!form.name.trim()) return setError("Ingrese el nombre del campo.");
    
    setIsSubmitting(true);
    const url = editOpen 
        ? camposRoutes.update.url({ campo: selected.id }) 
        : camposRoutes.store.url();
    const method = editOpen ? 'put' : 'post';

    router[method](url, {
      ...form,
      length: form.length ? Number(form.length) : null,
      enum_values: form.enum_values_text // Enviamos el string bruto, el Service se encarga de parsearlo
    }, {
      onSuccess: () => { setCreateOpen(false); setEditOpen(false); setError(""); },
      onFinish: () => setIsSubmitting(false)
    });
  };

  return (
    <DashboardLayout title="Campos de Metadatos">
      <div className="space-y-5">
        <header className="rounded-2xl border border-border bg-gradient-to-r from-slate-900 to-indigo-700 p-5 text-white shadow-sm">
          <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
              <p className="text-xs uppercase tracking-[0.24em] text-white/60">Configuración Documental</p>
              <h2 className="mt-2 text-2xl font-semibold">Campos de Metadatos</h2>
              <p className="mt-1 text-sm text-white/75">Define los elementos de información atómicos para las plantillas documentales.</p>
            </div>
            <div className="flex h-10 items-center gap-2 rounded-xl border border-white/20 bg-white/10 px-4 py-2 backdrop-blur-md self-start sm:self-center">
              <ListChecks className="h-4 w-4 text-indigo-300" />
              <span className="text-xs font-black uppercase tracking-wider text-white">
                {campos.total} <span className="text-white/60 ml-1">Registros</span>
              </span>
            </div>
          </div>
        </header>

        <div className="grid gap-3 sm:grid-cols-2">
          <article className="rounded-xl border border-border bg-card p-4 shadow-sm flex items-center gap-3">
            <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-primary/10 text-primary"><ListChecks className="h-5 w-5" /></div>
            <div><p className="text-xs text-muted-foreground">Campos definidos</p><p className="mt-1 text-2xl font-semibold text-foreground">{campos.total}</p></div>
          </article>
          <article className="rounded-xl border border-border bg-card p-4 shadow-sm flex items-center gap-3">
            <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600"><Plus className="h-5 w-5" /></div>
            <div><p className="text-xs text-muted-foreground">Plantillas asociadas</p><p className="mt-1 text-2xl font-semibold text-foreground">{totalDocumentTypes}</p></div>
          </article>
        </div>

        <div className="rounded-xl border border-border bg-card p-4 shadow-sm flex flex-col sm:flex-row justify-between items-center gap-4">
          <div className="flex-1 w-full max-w-md flex gap-2">
            <input 
              id="campos-search"
              name="search"
              type="text" 
              placeholder="Buscar por nombre..." 
              value={search}
              onChange={(e) => setSearch(e.target.value)}
              onKeyDown={(e) => e.key === 'Enter' && handleSearch()}
              className="h-10 flex-1 rounded-lg border border-border bg-background px-3 text-sm" 
            />
            <button 
              type="button" 
              onClick={handleSearch} 
              className="h-10 rounded-lg bg-primary px-4 text-sm font-semibold text-primary-foreground transition hover:opacity-90"
            >
              Aplicar
            </button>
            <button 
              type="button" 
              onClick={() => { setSearch(""); router.get(camposRoutes.index.url(), {}, { preserveState: true }); }} 
              className="h-10 rounded-lg border border-border bg-card px-4 text-sm font-semibold text-foreground transition hover:bg-muted"
            >
              Limpiar
            </button>
          </div>
          <p className="text-sm text-muted-foreground hidden lg:block">Administra la estructura granular de los datos institucionales.</p>
          {can('campos.create') && (
            <button type="button" onClick={() => { setForm(emptyForm); setError(""); setCreateOpen(true); }} className="inline-flex h-10 items-center gap-2 rounded-lg bg-emerald-600 px-4 text-sm font-semibold text-white transition hover:opacity-90">
              <Plus className="h-4 w-4" /> Nuevo Campo
            </button>
          )}
        </div>

        <div className="overflow-hidden rounded-xl border border-border bg-card shadow-sm mb-4">
          <div className="overflow-x-auto">
            <table className="w-full text-sm">
              <thead className="bg-muted/60">
                <tr className="text-left text-xs uppercase tracking-wide text-muted-foreground">
                  <th className="px-4 py-3">#</th>
                  <th className="px-4 py-3">Nombre</th>
                  <th className="px-4 py-3">Tipo</th>
                  <th className="px-4 py-3">Validaciones</th>
                  <th className="px-4 py-3 text-right">Acciones</th>
                </tr>
              </thead>
              <tbody>
                {campos.data.length > 0 ? (
                  campos.data.map((c, i) => (
                    <tr key={c.id} className="border-t border-border hover:bg-muted/30 transition-colors">
                      <td className="px-4 py-3 text-muted-foreground">{campos.from + i}</td>
                      <td className="px-4 py-3 font-semibold text-foreground">{c.name}</td>
                      <td className="px-4 py-3"><span className="rounded bg-primary/10 px-2 py-0.5 text-[10px] font-bold uppercase text-primary">{c.data_type}</span></td>
                      <td className="px-4 py-3">
                        <div className="flex flex-wrap gap-1">
                          {c.is_nullable && <span className="rounded-full border border-border bg-background px-2 py-0.5 text-[10px] text-muted-foreground">Nulable</span>}
                          {c.length && <span className="rounded-full border border-border bg-background px-2 py-0.5 text-[10px] text-muted-foreground">Len: {c.length}</span>}
                          {c.enum_values?.length > 0 && <span className="rounded-full border border-border bg-background px-2 py-0.5 text-[10px] text-muted-foreground">Enum</span>}
                        </div>
                      </td>
                      <td className="px-4 py-3 text-right">
                        <div className="flex justify-end gap-2">
                          <button type="button" onClick={() => { 
                            setSelected(c); 
                            setForm({
                              name: c.name,
                              data_type: c.data_type,
                              is_nullable: !!c.is_nullable,
                              length: c.length ? String(c.length) : "",
                              allow_negative: !!c.allow_negative,
                              allow_zero: !!c.allow_zero,
                              enum_values_text: (c.enum_values || []).join(", ")
                            });
                            setEditOpen(true);
                          }} className="inline-flex items-center gap-1.5 rounded-md bg-amber-500 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-amber-600"><Pencil className="h-3.5 w-3.5" /></button>
                          <button 
                            type="button"
                            onClick={() => confirm('¿Eliminar?') && router.delete(`/campos/${c.id}`)} 
                            disabled={c.document_types_count > 0} 
                            className="inline-flex items-center gap-1.5 rounded-md bg-red-600 px-3 py-1.5 text-xs font-semibold text-white disabled:opacity-50 disabled:cursor-not-allowed transition hover:bg-red-700"
                            title={c.document_types_count > 0 ? "No se puede eliminar porque está siendo utilizado en uno o más tipos de documentos" : ""}
                          >
                            <Trash2 className="h-3.5 w-3.5" />
                          </button>
                        </div>
                      </td>
                    </tr>
                  ))
                ) : (
                  <tr>
                    <td colSpan={5} className="px-4 py-16 text-center">
                      <div className="flex flex-col items-center justify-center max-w-md mx-auto">
                        <div className="flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-500/10 text-indigo-600 mb-4 animate-bounce">
                          <ListChecks className="h-8 w-8" />
                        </div>
                        <h3 className="text-lg font-semibold text-foreground">No se encontraron campos</h3>
                        <p className="mt-1 text-sm text-muted-foreground">
                          No hay campos dinámicos registrados en la base de datos o ningún registro coincide con la búsqueda.
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
          {...campos}
          onPageChange={(page: number) => router.get('/campos', { ...filters, page }, { preserveState: true })}
          label="campos"
        />

        <Modal open={createOpen || editOpen} title={editOpen ? "Editar Campo" : "Crear Nuevo Campo"} onClose={() => { setCreateOpen(false); setEditOpen(false); }}>
          <form onSubmit={handleSubmit}>
            <CampoFormFields form={form} setForm={setForm} error={error} submitLabel={editOpen ? "Actualizar" : "Guardar"} isSubmitting={isSubmitting} />
          </form>
        </Modal>
      </div>
    </DashboardLayout>
  );
}
