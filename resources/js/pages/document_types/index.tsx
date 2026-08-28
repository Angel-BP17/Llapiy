import React, { useState, useMemo, FormEvent, Dispatch, SetStateAction } from 'react';
import DashboardLayout from '@/Layouts/DashboardLayout';
import { Modal } from '@/components/Modal';
import { router, Head, Link } from '@inertiajs/react';
import { 
  Eye, Pencil, Plus, Trash2, ChevronRight, Layers 
} from 'lucide-react';
import docsRoutes from '@/routes/document_types';
import { usePermissions } from '@/hooks/use-permissions';
import Pagination from '@/components/ui/Pagination';

interface DocumentTypesIndexProps {
  documentTypes: any[];
  areas: any[];
  campoTypes: any[];
  paginationData: any;
  filters: any;
}

type DocumentTypeForm = {
  name: string;
  campoTypeIds: number[];
  groupIds: number[];
  subgroupIds: number[];
};

const emptyForm: DocumentTypeForm = { name: "", campoTypeIds: [], groupIds: [], subgroupIds: [] };

const flattenSubgroups = (subgroups: any[], parentGroupId?: number): any[] => {
  const all: any[] = [];
  subgroups.forEach(sub => {
    all.push({ ...sub, group_id: sub.group_id ?? parentGroupId });
    if (sub.subgroups?.length) all.push(...flattenSubgroups(sub.subgroups, sub.group_id ?? parentGroupId));
  });
  return all;
};

const getAreaGroups = (area: any): any[] => (area.area_group_types || []).flatMap((agt: any) => 
  (agt.groups || []).map((g: any) => ({ ...g, area_id: area.id }))
);

function DocumentTypeFormFields({
  form, setForm, error, submitLabel, areas, campoTypes, isSubmitting = false
}: {
  form: DocumentTypeForm; setForm: Dispatch<SetStateAction<DocumentTypeForm>>;
  error: string; submitLabel: string; areas: any[]; campoTypes: any[]; isSubmitting?: boolean;
}) {
  const [treeSearch, setTreeSearch] = useState("");
  const [campoSearch, setCampoSearch] = useState("");

  const filteredAreas = useMemo(() => {
    const q = treeSearch.trim().toLowerCase();
    if (!q) return areas;
    return areas.filter((a: any) => 
      a.descripcion.toLowerCase().includes(q) || 
      getAreaGroups(a).some(g => g.descripcion.toLowerCase().includes(q) || 
      flattenSubgroups(g.subgroups || [], g.id).some(s => s.descripcion.toLowerCase().includes(q)))
    );
  }, [treeSearch, areas]);

  const toggleSelection = (ids: number[], setIds: (ids: number[]) => void, targetIds: number[], checked: boolean) => {
    const set = new Set(ids);
    targetIds.forEach(id => checked ? set.add(id) : set.delete(id));
    setIds([...set]);
  };

  return (
    <div className="space-y-4">
      {error && <div className="rounded-lg border border-red-300 bg-red-50 px-3 py-2 text-sm text-red-700">{error}</div>}
      <div className="space-y-1">
        <label htmlFor="doc_type_name" className="text-sm font-semibold text-foreground">Nombre del tipo de documento</label>
        <input id="doc_type_name" value={form.name} onChange={e => setForm(p => ({...p, name: e.target.value}))} className="h-10 w-full rounded-lg border border-border bg-background px-3 text-sm" placeholder="Ej. Informe técnico" />
      </div>

      <div className="grid gap-4 lg:grid-cols-2">
        <div className="rounded-xl border border-border bg-background p-4">
          <h4 className="text-sm font-semibold text-foreground mb-3">Jerarquía Organizativa</h4>
          <input value={treeSearch} onChange={e => setTreeSearch(e.target.value)} className="mb-3 h-9 w-full rounded-lg border border-border bg-card px-3 text-sm" placeholder="Buscar área o grupo..." />
          <div className="max-h-72 space-y-2 overflow-y-auto pr-1">
            {filteredAreas.map((area: any) => (
              <div key={area.id} className="rounded-lg border border-border bg-card p-3">
                <label className="flex items-center gap-2 text-sm font-bold text-foreground">
                  <input type="checkbox" checked={getAreaGroups(area).every(g => form.groupIds.includes(g.id))} onChange={e => {
                    const groups = getAreaGroups(area);
                    toggleSelection(form.groupIds, (ids) => setForm(p => ({...p, groupIds: ids})), groups.map(g => g.id), e.target.checked);
                    toggleSelection(form.subgroupIds, (ids) => setForm(p => ({...p, subgroupIds: ids})), groups.flatMap(g => flattenSubgroups(g.subgroups || [], g.id).map(s => s.id)), e.target.checked);
                  }} className="h-4 w-4 rounded border-border" />
                  {area.descripcion}
                </label>
                <div className="mt-2 pl-6 space-y-2">
                  {getAreaGroups(area).map((group: any) => (
                    <div key={group.id}>
                      <label className="flex items-center gap-2 text-sm text-foreground">
                        <input type="checkbox" checked={form.groupIds.includes(group.id)} onChange={e => {
                          toggleSelection(form.groupIds, (ids) => setForm(p => ({...p, groupIds: ids})), [group.id], e.target.checked);
                          toggleSelection(form.subgroupIds, (ids) => setForm(p => ({...p, subgroupIds: ids})), flattenSubgroups(group.subgroups || [], group.id).map(s => s.id), e.target.checked);
                        }} className="h-4 w-4 rounded border-border" />
                        {group.descripcion}
                      </label>
                    </div>
                  ))}
                </div>
              </div>
            ))}
          </div>
        </div>

        <div className="rounded-xl border border-border bg-background p-4">
          <h4 className="text-sm font-semibold text-foreground mb-3">Campos de Metadatos</h4>
          <input value={campoSearch} onChange={e => setCampoSearch(e.target.value)} className="mb-3 h-9 w-full rounded-lg border border-border bg-card px-3 text-sm" placeholder="Buscar campo..." />
          <div className="max-h-72 space-y-2 overflow-y-auto rounded-lg border border-border bg-card p-3">
            {campoTypes.filter(c => c.name.toLowerCase().includes(campoSearch.toLowerCase())).map((c: any) => (
              <label key={c.id} className="flex items-center gap-2 text-sm text-foreground">
                <input type="checkbox" checked={form.campoTypeIds.includes(c.id)} onChange={e => toggleSelection(form.campoTypeIds, (ids) => setForm(p => ({...p, campoTypeIds: ids})), [c.id], e.target.checked)} className="h-4 w-4 rounded border-border" />
                {c.name} <span className="ml-auto text-[10px] uppercase text-muted-foreground">{c.data_type}</span>
              </label>
            ))}
          </div>
        </div>
      </div>

      <div className="flex justify-end pt-4">
        <button type="submit" disabled={isSubmitting} className="rounded-lg bg-primary px-6 py-2 text-sm font-bold text-primary-foreground disabled:opacity-50">{submitLabel}</button>
      </div>
    </div>
  );
}

export default function Index({ documentTypes, areas, campoTypes, filters, paginationData }: DocumentTypesIndexProps) {
  const { can } = usePermissions();
  const [f, setF] = useState({ name: filters?.name || "" });
  const [createOpen, setCreateOpen] = useState(false);
  const [editOpen, setEditOpen] = useState(false);
  const [listOpen, setListOpen] = useState(false);
  const [listTitle, setListTitle] = useState("");
  const [listItems, setListItems] = useState<string[]>([]);
  const [selected, setSelected] = useState<any>(null);
  const [form, setForm] = useState<DocumentTypeForm>(emptyForm);
  const [error, setError] = useState("");
  const [isSubmitting, setIsSubmitting] = useState(false);

  const handleSubmit = (e: FormEvent) => {
    e.preventDefault();
    if (!form.name.trim()) return setError("Ingrese el nombre del tipo.");
    if (form.campoTypeIds.length === 0) return setError("Seleccione al menos un campo.");
    
    setIsSubmitting(true);
    const url = editOpen ? docsRoutes.update.url({ documentType: selected.id }) : docsRoutes.store.url();
    const method = editOpen ? 'put' : 'post';

    router[method](url, {
      name: form.name,
      campos: JSON.stringify(form.campoTypeIds),
      groups: JSON.stringify(form.groupIds),
      subgroups: JSON.stringify(form.subgroupIds)
    }, {
      onSuccess: () => { setCreateOpen(false); setEditOpen(false); setError(""); },
      onFinish: () => setIsSubmitting(false)
    });
  };

  return (
    <DashboardLayout title="Tipos de Documento">
      <Head title="Tipos de Documentos" />
      <div className="space-y-5">
        <header className="rounded-2xl border border-border bg-gradient-to-r from-slate-900 to-indigo-700 p-5 text-white shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
          <div>
            <p className="text-xs uppercase tracking-[0.24em] text-white/60">Configuración Documental</p>
            <h2 className="mt-2 text-2xl font-semibold">Tipos de Documentos</h2>
          </div>
          {can('document-types.create') && (
            <button type="button" onClick={() => { setForm(emptyForm); setError(""); setCreateOpen(true); }} className="inline-flex h-9 items-center gap-2 rounded-lg bg-white px-4 text-xs font-bold text-indigo-700 transition hover:bg-indigo-50"><Plus className="h-4 w-4" /> Crear Nuevo</button>
          )}
        </header>

        <div className="rounded-xl border border-border bg-card p-4 shadow-sm">
          <div className="flex flex-col gap-3 md:flex-row">
            <input id="doc-search" value={f.name} onChange={e => setF({name: e.target.value})} placeholder="Buscar por nombre..." className="h-10 flex-1 rounded-lg border border-border bg-background px-3 text-sm" />
            <button type="button" onClick={() => router.get(docsRoutes.index.url(), f, { preserveState: true })} className="h-10 px-6 rounded-lg bg-primary text-sm font-semibold text-primary-foreground transition hover:opacity-90">Filtrar</button>
          </div>
        </div>

        <div className="overflow-hidden rounded-xl border border-border bg-card shadow-sm">
          <div className="overflow-x-auto">
            <table className="w-full text-sm">
              <thead className="bg-muted/60">
                <tr className="text-left text-xs uppercase tracking-wide text-muted-foreground">
                  <th className="px-4 py-3">Nombre</th>
                  <th className="px-4 py-3 text-center">Metadatos</th>
                  <th className="px-4 py-3 text-center">Grupos</th>
                  <th className="px-4 py-3 text-right">Acciones</th>
                </tr>
              </thead>
              <tbody>
                {documentTypes.map((dt: any) => (
                  <tr key={dt.id} className="border-t border-border hover:bg-muted/30 transition-colors">
                    <td className="px-4 py-3 font-semibold text-foreground">{dt.name}</td>
                    <td className="px-4 py-3 text-center">
                      <button type="button" onClick={() => { setListTitle(`Campos: ${dt.name}`); setListItems(dt.campo_types.map((c: any) => c.name)); setListOpen(true); }} className="text-xs font-bold text-sky-600 hover:underline">Ver {dt.campo_types?.length || 0} campos</button>
                    </td>
                    <td className="px-4 py-3 text-center">
                      <button type="button" onClick={() => { setListTitle(`Grupos: ${dt.name}`); setListItems(dt.groups.map((g: any) => g.descripcion)); setListOpen(true); }} className="text-xs font-bold text-indigo-600 hover:underline">Ver {dt.groups?.length || 0} grupos</button>
                    </td>
                    <td className="px-4 py-3 text-right">
                      <div className="flex justify-end gap-2">
                        <Link href={docsRoutes.show.url({ documentType: dt.id })} className="p-1.5 rounded-md bg-primary/10 text-primary hover:bg-primary/20 transition-colors"><Eye className="h-4 w-4" /></Link>
                        <button type="button" onClick={() => { setSelected(dt); setForm({ name: dt.name, campoTypeIds: dt.campo_types.map((c: any) => c.id), groupIds: dt.groups.map((g: any) => g.id), subgroupIds: dt.subgroups.map((s: any) => s.id) }); setEditOpen(true); }} className="p-1.5 rounded-md bg-amber-500 text-white hover:bg-amber-600 transition-colors"><Pencil className="h-4 w-4" /></button>
                        <button type="button" onClick={() => confirm('¿Eliminar?') && router.delete(docsRoutes.destroy.url({ documentType: dt.id }))} disabled={dt.documents_count > 0} className="p-1.5 rounded-md bg-red-600 text-white disabled:opacity-50 transition-colors"><Trash2 className="h-4 w-4" /></button>
                      </div>
                    </td>
                  </tr>
                ))}
                {documentTypes.length === 0 && (
                  <tr>
                    <td colSpan={4} className="px-4 py-16 text-center">
                      <div className="flex flex-col items-center justify-center max-w-md mx-auto">
                        <div className="flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-500/10 text-indigo-600 mb-4 animate-bounce">
                          <Layers className="h-8 w-8" />
                        </div>
                        <h3 className="text-lg font-semibold text-foreground">No se encontraron tipos de documentos</h3>
                        <p className="mt-1 text-sm text-muted-foreground">
                          No hay tipos de documentos registrados en la base de datos o ningún registro coincide con la búsqueda.
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
          {...paginationData}
          onPageChange={(page) => router.get(docsRoutes.index.url(), { ...f, page }, { preserveState: true })}
          label="tipos"
        />

        <Modal open={createOpen || editOpen} title={editOpen ? "Editar Tipo" : "Crear Tipo de Documento"} onClose={() => { setCreateOpen(false); setEditOpen(false); }} maxWidth="max-w-6xl">
          <form onSubmit={handleSubmit}>
            <DocumentTypeFormFields form={form} setForm={setForm} error={error} submitLabel={editOpen ? "Actualizar" : "Guardar"} areas={areas} campoTypes={campoTypes} isSubmitting={isSubmitting} />
          </form>
        </Modal>

        <Modal open={listOpen} title={listTitle} onClose={() => setListOpen(false)} maxWidth="max-w-xl">
          <ul className="space-y-2 py-2">
            {listItems.length ? listItems.map((item, i) => (
              <li key={i} className="flex items-center gap-2 text-sm bg-muted/30 px-3 py-2 rounded-lg"><ChevronRight className="h-3 w-3 text-primary" /> {item}</li>
            )) : <li className="text-sm text-muted-foreground italic px-3">No hay elementos asociados.</li>}
          </ul>
        </Modal>
      </div>
    </DashboardLayout>
  );
}
