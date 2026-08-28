import React, { useState, FormEvent } from 'react';
import DashboardLayout from '@/Layouts/DashboardLayout';
import { Modal } from '@/components/Modal';
import { router, Head } from '@inertiajs/react';
import { 
  Pencil, Plus, Trash2, FolderOpen, AlertCircle
} from 'lucide-react';
import documentary_series from '@/routes/documentary_series';
import { usePermissions } from '@/hooks/use-permissions';
import Pagination from '@/components/ui/Pagination';

interface DocumentarySeriesProps {
  documentarySeries: any[];
  pagination: any;
  filters: any;
}

type DocumentarySeriesForm = {
  codigo: string;
  nombre: string;
};

const emptyForm: DocumentarySeriesForm = { codigo: "", nombre: "" };

export default function Index({ documentarySeries, pagination, filters }: DocumentarySeriesProps) {
  const { can, is } = usePermissions();
  const isAdmin = is("ADMINISTRADOR");

  const [f, setF] = useState({ 
    codigo: filters?.codigo || "", 
    nombre: filters?.nombre || "" 
  });

  const [createOpen, setCreateOpen] = useState(false);
  const [editOpen, setEditOpen] = useState(false);
  const [selected, setSelected] = useState<any>(null);
  const [form, setForm] = useState<DocumentarySeriesForm>(emptyForm);
  const [error, setError] = useState("");
  const [isSubmitting, setIsSubmitting] = useState(false);

  const handleFilter = () => {
    router.get(documentary_series.index.url(), f, { preserveState: true });
  };

  const handleClearFilters = () => {
    const cleared = { codigo: "", nombre: "" };
    setF(cleared);
    router.get(documentary_series.index.url(), cleared, { preserveState: true });
  };

  const handleSubmit = (e: FormEvent) => {
    e.preventDefault();
    setError("");

    if (!form.codigo.trim()) return setError("Ingrese el código de la serie.");
    if (!form.nombre.trim()) return setError("Ingrese el nombre de la serie.");

    setIsSubmitting(true);
    const url = editOpen 
      ? documentary_series.update.url({ documentary_series: selected.id }) 
      : documentary_series.store.url();

    const data = editOpen 
      ? { ...form, _method: 'PUT' } 
      : form;

    router.post(url, data, {
      onSuccess: () => { 
        setCreateOpen(false); 
        setEditOpen(false); 
        setForm(emptyForm);
        setError(""); 
      },
      onError: (err: any) => {
        setError(Object.values(err)[0] as string);
      },
      onFinish: () => setIsSubmitting(false)
    });
  };

  const openEdit = (series: any) => {
    setSelected(series);
    setForm({
      codigo: series.codigo,
      nombre: series.nombre,
    });
    setError("");
    setEditOpen(true);
  };

  const handleDelete = (series: any) => {
    if (series.blocks_count > 0) {
      alert("No se puede eliminar la serie documental porque tiene bloques físicos asociados.");
      return;
    }

    if (confirm("¿Estás seguro de que deseas eliminar esta serie documental?")) {
      router.delete(documentary_series.destroy.url({ documentary_series: series.id }), {
        onError: (err: any) => {
          alert(Object.values(err)[0] as string);
        }
      });
    }
  };

  if (!isAdmin) {
    return (
      <DashboardLayout title="Acceso Denegado">
        <Head title="Acceso Denegado" />
        <div className="flex h-[60vh] flex-col items-center justify-center text-center p-6">
          <div className="flex h-16 w-16 items-center justify-center rounded-full bg-red-100 text-red-600 mb-4">
            <AlertCircle className="h-8 w-8" />
          </div>
          <h3 className="text-xl font-bold text-foreground">Acceso Denegado</h3>
          <p className="mt-2 text-sm text-muted-foreground max-w-md">
            No tienes autorización para acceder a esta sección. Solo los usuarios con el rol de Administrador pueden gestionar las Series Documentales.
          </p>
        </div>
      </DashboardLayout>
    );
  }

  return (
    <DashboardLayout title="Series Documentales">
      <Head title="Series Documentales" />
      <div className="space-y-5">
        <header className="rounded-2xl border border-border bg-gradient-to-r from-slate-900 to-indigo-700 p-5 text-white shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
          <div>
            <p className="text-xs uppercase tracking-[0.24em] text-white/60">Administración / Configuración</p>
            <h2 className="mt-2 text-2xl font-semibold">Series Documentales</h2>
            <p className="mt-1 text-sm text-white/75">Registra y administra las series documentales del archivo.</p>
          </div>
          {can('documentary-series.create') && (
            <button 
              type="button" 
              onClick={() => { setForm(emptyForm); setError(""); setCreateOpen(true); }} 
              className="inline-flex h-10 items-center gap-2 rounded-lg bg-white px-5 text-sm font-bold text-indigo-700 transition hover:bg-indigo-50 shadow-md"
            >
              <Plus className="h-4 w-4" /> Crear Serie
            </button>
          )}
        </header>

        {/* Filtros */}
        <div className="rounded-xl border border-border bg-card p-4 shadow-sm">
          <div className="flex flex-col gap-3 sm:flex-row">
            <input 
              value={f.codigo} 
              onChange={e => setF({ ...f, codigo: e.target.value })} 
              onKeyDown={e => e.key === 'Enter' && handleFilter()}
              placeholder="Buscar por código..." 
              className="h-10 w-full sm:w-64 rounded-lg border border-border bg-background px-3 text-sm focus:ring-2 focus:ring-primary/20 outline-none" 
            />
            <input 
              value={f.nombre} 
              onChange={e => setF({ ...f, nombre: e.target.value })} 
              onKeyDown={e => e.key === 'Enter' && handleFilter()}
              placeholder="Buscar por nombre..." 
              className="h-10 flex-1 rounded-lg border border-border bg-background px-3 text-sm focus:ring-2 focus:ring-primary/20 outline-none" 
            />
            <div className="flex gap-2">
              <button 
                type="button" 
                onClick={handleFilter} 
                className="h-10 px-5 rounded-lg bg-primary text-sm font-semibold text-primary-foreground transition hover:opacity-90 shadow-sm"
              >
                Filtrar
              </button>
              <button 
                type="button" 
                onClick={handleClearFilters} 
                className="h-10 px-5 rounded-lg border border-border bg-background text-sm font-semibold text-foreground hover:bg-muted transition"
              >
                Limpiar
              </button>
            </div>
          </div>
        </div>

        {/* Tabla */}
        <div className="overflow-hidden rounded-xl border border-border bg-card shadow-sm">
          <div className="overflow-x-auto">
            <table className="w-full text-sm">
              <thead className="bg-muted/60">
                <tr className="text-left text-xs uppercase tracking-wide text-muted-foreground">
                  <th className="px-4 py-3 w-32">Código</th>
                  <th className="px-4 py-3">Nombre</th>
                  <th className="px-4 py-3 text-center w-40">Bloques Asociados</th>
                  <th className="px-4 py-3 text-right w-32">Acciones</th>
                </tr>
              </thead>
              <tbody>
                {documentarySeries.map((series: any) => (
                  <tr key={series.id} className="border-t border-border hover:bg-muted/30 transition-colors">
                    <td className="px-4 py-3 font-mono font-bold text-indigo-600">{series.codigo}</td>
                    <td className="px-4 py-3 font-semibold text-foreground">{series.nombre}</td>
                    <td className="px-4 py-3 text-center">
                      <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold ${
                        series.blocks_count > 0 
                          ? 'bg-blue-100 text-blue-800' 
                          : 'bg-slate-100 text-slate-800'
                      }`}>
                        {series.blocks_count || 0}
                      </span>
                    </td>
                    <td className="px-4 py-3 text-right">
                      <div className="flex justify-end gap-2">
                        {can('documentary-series.update') && (
                          <button 
                            type="button" 
                            onClick={() => openEdit(series)} 
                            className="p-1.5 rounded-md bg-amber-500 text-white hover:bg-amber-600 transition-colors"
                            title="Editar Serie"
                          >
                            <Pencil className="h-4 w-4" />
                          </button>
                        )}
                        {can('documentary-series.delete') && (
                          <button 
                            type="button" 
                            onClick={() => handleDelete(series)} 
                            disabled={series.blocks_count > 0}
                            className="p-1.5 rounded-md bg-red-600 text-white disabled:opacity-40 transition-colors"
                            title={series.blocks_count > 0 ? "No se puede eliminar porque tiene bloques asociados" : "Eliminar Serie"}
                          >
                            <Trash2 className="h-4 w-4" />
                          </button>
                        )}
                      </div>
                    </td>
                  </tr>
                ))}
                {documentarySeries.length === 0 && (
                  <tr>
                    <td colSpan={4} className="px-4 py-16 text-center">
                      <div className="flex flex-col items-center justify-center max-w-md mx-auto">
                        <div className="flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-500/10 text-indigo-600 mb-4">
                          <FolderOpen className="h-8 w-8" />
                        </div>
                        <h3 className="text-lg font-semibold text-foreground">No se encontraron series documentales</h3>
                        <p className="mt-1 text-sm text-muted-foreground">
                          Registra tu primera serie documental usando el botón "Crear Serie" superior.
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
          onPageChange={(page) => router.get(documentary_series.index.url(), { ...f, page }, { preserveState: true })}
          label="series"
        />

        {/* Modal Crear / Editar */}
        <Modal 
          open={createOpen || editOpen} 
          title={editOpen ? "Editar Serie Documental" : "Crear Serie Documental"} 
          onClose={() => { setCreateOpen(false); setEditOpen(false); }} 
          maxWidth="max-w-xl"
        >
          <form onSubmit={handleSubmit} className="space-y-4">
            {error && (
              <div className="rounded-lg border border-red-300 bg-red-50 px-3 py-2 text-xs text-red-700 flex items-center gap-2">
                <AlertCircle className="h-4 w-4 shrink-0" />
                {error}
              </div>
            )}
            <div className="space-y-1">
              <label htmlFor="codigo" className="text-xs font-bold uppercase text-muted-foreground">Código de la Serie</label>
              <input 
                id="codigo"
                value={form.codigo} 
                onChange={e => setForm(p => ({ ...p, codigo: e.target.value }))} 
                className="h-10 w-full rounded-lg border border-border bg-background px-3 text-sm focus:ring-2 focus:ring-primary/20 outline-none" 
                placeholder="Ej. 1.1 o ACT-DIR" 
                required
              />
            </div>
            <div className="space-y-1">
              <label htmlFor="nombre" className="text-xs font-bold uppercase text-muted-foreground">Nombre / Descripción</label>
              <input 
                id="nombre"
                value={form.nombre} 
                onChange={e => setForm(p => ({ ...p, nombre: e.target.value }))} 
                className="h-10 w-full rounded-lg border border-border bg-background px-3 text-sm focus:ring-2 focus:ring-primary/20 outline-none" 
                placeholder="Ej. Actas de Directorio" 
                required
              />
            </div>

            <div className="flex justify-end pt-4">
              <button 
                type="submit" 
                disabled={isSubmitting} 
                className="rounded-lg bg-primary px-6 py-2 text-sm font-bold text-primary-foreground disabled:opacity-50 shadow-md shadow-primary/10"
              >
                {isSubmitting ? "Guardando..." : "Guardar Cambios"}
              </button>
            </div>
          </form>
        </Modal>
      </div>
    </DashboardLayout>
  );
}
