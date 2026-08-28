import React, { useState, useMemo, FormEvent } from 'react';
import DashboardLayout from '@/Layouts/DashboardLayout';
import { Modal } from '@/components/Modal';
import { router } from '@inertiajs/react';
import { Eye, FileDown, FileText, Filter, Pencil, Plus, Trash2, Search, Calendar, MapPin, Tag, ChevronRight, CheckCircle2, Clock3 } from 'lucide-react';
import { DocumentFormFields, DocumentForm } from '@/components/documents/DocumentFormFields';
import docsRoutes from '@/routes/documents';
import { usePermissions } from '@/hooks/use-permissions';
import Pagination from '@/components/ui/Pagination';
import { Document, DocumentType, Area, Group, Subgroup, PaginationData } from '@/types/models';

interface DocumentsIndexProps {
  documents: Document[];
  documentTypes: DocumentType[];
  areas: Area[];
  groups: Group[];
  subgroups: Subgroup[];
  years: number[];
  stats: {
    totalDocuments: number;
    attendedCount: number;
    unattendedCount: number;
  };
  filters: {
    asunto?: string;
    document_type_id?: string;
    area_id?: string;
    group_id?: string;
    subgroup_id?: string;
    year?: string;
    month?: string;
  };
  pagination: PaginationData;
}

const emptyForm: DocumentForm = {
  asunto: '',
  n_documento: '',
  folios: 1,
  document_type_id: '',
  root: null,
  campos: {}
};

export default function Index({ 
  documents, documentTypes, areas, groups, subgroups, years, stats, filters, pagination 
}: DocumentsIndexProps) {
  const { can } = usePermissions();
  const [isCreateOpen, setIsCreateOpen] = useState(false);
  const [isEditOpen, setIsEditOpen] = useState(false);
  const [isDetailsOpen, setIsDetailsOpen] = useState(false);
  const [selectedDocument, setSelectedDocument] = useState<Document | null>(null);
  
  const [form, setForm] = useState<DocumentForm>(emptyForm);
  const [f, setF] = useState(filters);
  const [isSubmitting, setIsSubmitting] = useState(false);

  const handleSearch = () => {
    router.get('/documentos', f as any, { preserveState: true });
  };

  const handleCreateSubmit = (e: FormEvent) => {
    e.preventDefault();
    setIsSubmitting(true);
    router.post('/documentos', form as any, {
      forceFormData: true,
      onSuccess: () => {
        setIsCreateOpen(false);
        setForm(emptyForm);
      },
      onFinish: () => setIsSubmitting(false)
    });
  };

  const handleEditSubmit = (e: FormEvent) => {
    e.preventDefault();
    if (!selectedDocument) return;
    setIsSubmitting(true);
    router.post(`/documentos/${selectedDocument.id}`, {
      ...form,
      _method: 'put'
    } as any, {
      forceFormData: true,
      onSuccess: () => {
        setIsEditOpen(false);
        setSelectedDocument(null);
      },
      onFinish: () => setIsSubmitting(false)
    });
  };

  const handlePageChange = (page: number) => {
    router.get('/documentos', { ...filters, page } as any, { preserveState: true });
  };

  return (
    <DashboardLayout title="Documentos">
      <div className="space-y-5">
        <header className="rounded-2xl border border-border bg-gradient-to-r from-slate-900 to-indigo-700 p-5 text-white shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
          <div>
            <p className="text-xs uppercase tracking-[0.24em] text-white/60">Archivo Central / Digital</p>
            <h2 className="mt-2 text-2xl font-semibold">Gestión de Documentos</h2>
            <p className="mt-1 text-sm text-white/75">Busca, registra y organiza documentos digitales en el repositorio central.</p>
          </div>
          <div className="flex h-10 items-center gap-2 rounded-xl border border-white/20 bg-white/10 px-4 py-2 backdrop-blur-md self-start sm:self-center">
            <FileText className="h-4 w-4 text-indigo-300" />
            <span className="text-xs font-black uppercase tracking-wider text-white">
              {pagination.total} <span className="text-white/60 ml-1">Documentos</span>
            </span>
          </div>
        </header>

        <div className="grid gap-3 sm:grid-cols-3">
          <article className="rounded-xl border border-border bg-card p-4 shadow-sm flex items-center gap-3">
            <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-primary/10 text-primary">
              <FileText className="h-5 w-5" />
            </div>
            <div>
              <p className="text-xs text-muted-foreground">Total de documentos</p>
              <p className="mt-1 text-2xl font-semibold text-foreground">{stats.totalDocuments}</p>
            </div>
          </article>
          <article className="rounded-xl border border-border bg-card p-4 shadow-sm flex items-center gap-3">
            <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">
              <CheckCircle2 className="h-5 w-5" />
            </div>
            <div>
              <p className="text-xs text-muted-foreground">Con archivo digital</p>
              <p className="mt-1 text-2xl font-semibold text-foreground">{stats.attendedCount}</p>
            </div>
          </article>
          <article className="rounded-xl border border-border bg-card p-4 shadow-sm flex items-center gap-3">
            <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-100 text-amber-600">
              <Clock3 className="h-5 w-5" />
            </div>
            <div>
              <p className="text-xs text-muted-foreground">Sin archivo digital</p>
              <p className="mt-1 text-2xl font-semibold text-foreground">{stats.unattendedCount}</p>
            </div>
          </article>
        </div>

        {/* Filter Section */}
        <div className="rounded-xl border border-border bg-card p-4 shadow-sm">
          <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-[minmax(0,1.5fr)_minmax(0,1fr)_minmax(0,1fr)_auto] items-center">
            <div className="relative w-full">
              <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
              <input value={f.asunto} onChange={e => setF({...f, asunto: e.target.value})} className="h-10 w-full rounded-lg border border-border bg-background pl-9 pr-3 text-sm outline-none focus:ring-2 focus:ring-primary/20" placeholder="Buscar por asunto..." />
            </div>
            <select value={f.document_type_id} onChange={e => setF({...f, document_type_id: e.target.value})} className="h-10 w-full rounded-lg border border-border bg-background px-3 text-sm outline-none focus:ring-2 focus:ring-primary/20">
              <option value="">Todos los tipos</option>
              {documentTypes.map(t => <option key={t.id} value={t.id}>{t.name}</option>)}
            </select>
            <select value={f.area_id} onChange={e => setF({...f, area_id: e.target.value})} className="h-10 w-full rounded-lg border border-border bg-background px-3 text-sm outline-none focus:ring-2 focus:ring-primary/20">
              <option value="">Todas las áreas</option>
              {areas.map(a => <option key={a.id} value={a.id}>{a.descripcion}</option>)}
            </select>
            <div className="flex flex-nowrap items-center gap-2">
              <button onClick={handleSearch} className="flex h-10 px-4 items-center justify-center gap-2 rounded-lg bg-primary text-sm font-semibold text-primary-foreground transition hover:opacity-90 whitespace-nowrap">
                <Filter className="h-4 w-4" /> Filtrar
              </button>
              {can('documents.create') && (
                <button 
                  onClick={() => { setForm(emptyForm); setIsCreateOpen(true); }} 
                  className="inline-flex h-10 px-4 items-center justify-center gap-2 rounded-lg bg-emerald-600 text-sm font-semibold text-white transition hover:bg-emerald-700 whitespace-nowrap"
                >
                  <Plus className="h-4 w-4" /> Nuevo Documento
                </button>
              )}
              <button 
                onClick={() => window.open(docsRoutes.pdf.url(f as any), '_blank')} 
                disabled={stats.totalDocuments === 0}
                className="flex h-10 w-12 shrink-0 items-center justify-center rounded-lg bg-red-600 text-white hover:bg-red-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" 
                title="Exportar PDF"
              >
                <FileDown className="h-5 w-5" />
              </button>
            </div>
          </div>
        </div>

        {/* Table Section */}
        <div className="overflow-hidden rounded-xl border border-border bg-card shadow-sm mb-4">
          <div className="overflow-x-auto">
            <table className="w-full text-sm">
              <thead className="bg-muted/60">
                <tr className="text-left text-xs uppercase tracking-wide text-muted-foreground">
                  <th className="px-4 py-3">Documento</th>
                  <th className="px-4 py-3">Origen / Destino</th>
                  <th className="px-4 py-3">Metadata</th>
                  <th className="px-4 py-3 text-right">Acciones</th>
                </tr>
              </thead>
              <tbody>
                {documents.map((doc: Document) => (
                  <tr key={doc.id} className="border-t border-border hover:bg-muted/30 transition-colors">
                    <td className="px-4 py-3">
                      <div className="flex items-center gap-3">
                        <div className="flex h-9 w-9 items-center justify-center rounded-lg bg-primary/10 text-primary shrink-0"><FileText className="h-5 w-5" /></div>
                        <div className="min-w-0">
                          <p className="font-bold text-foreground line-clamp-1">{doc.asunto}</p>
                          <div className="flex items-center gap-2 mt-0.5">
                            <span className="text-[10px] font-black uppercase text-muted-foreground bg-muted px-1.5 py-0.5 rounded tracking-tighter">{doc.n_documento}</span>
                            <span className="text-[10px] font-medium text-indigo-600">{doc.document_type?.name}</span>
                          </div>
                        </div>
                      </div>
                    </td>
                    <td className="px-4 py-3">
                      <div className="space-y-1">
                        <div className="flex items-center gap-1.5 text-xs text-muted-foreground">
                          <Building2 className="h-3 w-3" />
                          <span className="line-clamp-1">{doc.group?.area_group_type?.area?.descripcion}</span>
                        </div>
                        <div className="flex items-center gap-1.5 text-[10px] font-medium text-slate-500">
                          <ChevronRight className="h-3 w-3" />
                          <span className="line-clamp-1">{doc.group?.descripcion}</span>
                        </div>
                      </div>
                    </td>
                    <td className="px-4 py-3">
                      <div className="flex flex-wrap gap-1">
                        {doc.campos?.slice(0, 2).map(c => (
                          <span key={c.id} className="inline-flex items-center rounded-md bg-muted px-1.5 py-0.5 text-[10px] text-muted-foreground" title={c.campo_type?.name}>{c.valor}</span>
                        ))}
                        {(doc.campos?.length ?? 0) > 2 && <span className="text-[10px] text-muted-foreground font-bold ml-1">+{doc.campos!.length - 2}</span>}
                      </div>
                    </td>
                    <td className="px-4 py-3 text-right">
                      <div className="flex justify-end gap-2">
                        <button onClick={() => { setSelectedDocument(doc); setIsDetailsOpen(true); }} className="p-1.5 rounded-md bg-primary/10 text-primary hover:bg-primary/20 transition-colors"><Eye className="h-4 w-4" /></button>
                        {doc.can?.update && (
                          <button onClick={() => { 
                            setSelectedDocument(doc); 
                            setForm({
                              asunto: doc.asunto,
                              n_documento: doc.n_documento,
                              folios: doc.folios,
                              document_type_id: doc.document_type_id.toString(),
                              root: null,
                              campos: doc.campos?.reduce((acc, c) => ({...acc, [c.campo_type_id]: c.valor}), {}) || {}
                            });
                            setIsEditOpen(true); 
                          }} className="p-1.5 rounded-md bg-amber-500 text-white hover:bg-amber-600 transition-colors"><Pencil className="h-4 w-4" /></button>
                        )}
                        {doc.can?.delete && (
                          <button onClick={() => confirm('¿Eliminar documento?') && router.delete(`/documentos/${doc.id}`)} className="p-1.5 rounded-md bg-red-600 text-white hover:bg-red-700 transition-colors"><Trash2 className="h-4 w-4" /></button>
                        )}
                      </div>
                    </td>
                  </tr>
                ))}
                {documents.length === 0 && (
                  <tr>
                    <td colSpan={4} className="px-4 py-16 text-center">
                      <div className="flex flex-col items-center justify-center max-w-md mx-auto">
                        <div className="flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-500/10 text-indigo-600 mb-4 animate-bounce">
                          <FileText className="h-8 w-8" />
                        </div>
                        <h3 className="text-lg font-semibold text-foreground">No se encontraron documentos</h3>
                        <p className="mt-1 text-sm text-muted-foreground">
                          No hay registros de documentos en la base de datos o ningún registro coincide con los filtros aplicados.
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
          label="documentos"
        />

        <div className="h-8" />

        {/* MODAL CREAR */}
        <Modal open={isCreateOpen} title="Registrar Nuevo Documento" onClose={() => setIsCreateOpen(false)} maxWidth="max-w-5xl">
          <form onSubmit={handleCreateSubmit} className="py-2">
            <DocumentFormFields form={form} setForm={setForm} documentTypes={documentTypes} areas={areas} isSubmitting={isSubmitting} submitLabel="Registrar Documento" />
          </form>
        </Modal>

        {/* MODAL EDITAR */}
        <Modal open={isEditOpen} title={`Editar Documento: ${selectedDocument?.asunto}`} onClose={() => setIsEditOpen(false)} maxWidth="max-w-5xl">
          <form onSubmit={handleEditSubmit} className="py-2">
            <DocumentFormFields form={form} setForm={setForm} documentTypes={documentTypes} areas={areas} isSubmitting={isSubmitting} isEdit submitLabel="Actualizar Información" />
          </form>
        </Modal>

        {/* MODAL DETALLES */}
        <Modal open={isDetailsOpen} title="Detalles del Documento" onClose={() => setIsDetailsOpen(false)} maxWidth="max-w-3xl">
          {selectedDocument && (
            <div className="space-y-8 py-2">
              <div className="flex items-start gap-5 pb-6 border-b border-border">
                <div className="flex h-16 w-16 items-center justify-center rounded-2xl bg-primary text-white shadow-xl shadow-primary/20 shrink-0"><FileText className="h-8 w-8" /></div>
                <div className="space-y-1">
                  <h3 className="text-xl font-bold text-foreground">{selectedDocument.asunto}</h3>
                  <div className="flex flex-wrap gap-2 pt-1">
                    <span className="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-indigo-50 text-indigo-700 text-[10px] font-black uppercase border border-indigo-100">{selectedDocument.document_type?.name}</span>
                    <span className="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 text-[10px] font-black uppercase border border-slate-200">{selectedDocument.n_documento}</span>
                  </div>
                </div>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div className="space-y-6">
                  <div className="space-y-4">
                    <h4 className="text-[10px] font-black uppercase text-muted-foreground tracking-widest flex items-center gap-2"><MapPin className="h-3.5 w-3.5" /> Ubicación Organizativa</h4>
                    <div className="space-y-3 pl-1">
                      <div><p className="text-[10px] font-black uppercase text-muted-foreground opacity-60">Área</p><p className="text-sm font-semibold">{selectedDocument.group?.area_group_type?.area?.descripcion}</p></div>
                      <div><p className="text-[10px] font-black uppercase text-muted-foreground opacity-60">Grupo / Oficina</p><p className="text-sm font-semibold">{selectedDocument.group?.descripcion}</p></div>
                    </div>
                  </div>
                  <div className="space-y-4">
                    <h4 className="text-[10px] font-black uppercase text-muted-foreground tracking-widest flex items-center gap-2"><Calendar className="h-3.5 w-3.5" /> Información de Registro</h4>
                    <div className="space-y-3 pl-1">
                      <div><p className="text-[10px] font-black uppercase text-muted-foreground opacity-60">Registrado por</p><p className="text-sm font-semibold">{selectedDocument.user?.name} {selectedDocument.user?.last_name}</p></div>
                      <div><p className="text-[10px] font-black uppercase text-muted-foreground opacity-60">Fecha de Registro</p><p className="text-sm font-semibold">{new Date(selectedDocument.created_at).toLocaleDateString('es-ES', { day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' })}</p></div>
                    </div>
                  </div>
                </div>

                <div className="space-y-6">
                  <div className="space-y-4">
                    <h4 className="text-[10px] font-black uppercase text-muted-foreground tracking-widest flex items-center gap-2"><Tag className="h-3.5 w-3.5" /> Metadatos Específicos</h4>
                    <div className="bg-muted/30 rounded-xl p-4 space-y-3 border border-border/50">
                      {selectedDocument.campos?.length ? selectedDocument.campos.map(c => (
                        <div key={c.id} className="flex justify-between items-center gap-4 border-b border-border/50 last:border-0 pb-2 last:pb-0">
                          <span className="text-[10px] font-bold text-muted-foreground uppercase">{c.campo_type?.name}</span>
                          <span className="text-xs font-semibold text-foreground text-right">{c.valor}</span>
                        </div>
                      )) : <p className="text-xs text-muted-foreground italic">No hay información adicional registrada.</p>}
                    </div>
                  </div>
                  <div className="pt-4">
                    {selectedDocument.root ? (
                      <button onClick={() => window.open(`/documentos/${selectedDocument.id}/file`, '_blank')} className="w-full flex items-center justify-center gap-2 rounded-xl bg-indigo-600 py-3 text-sm font-bold text-white hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-600/20"><FileDown className="h-5 w-5" /> Ver Documento Original</button>
                    ) : (
                      <div className="rounded-xl border border-dashed border-border bg-muted/20 p-4 text-center text-xs text-muted-foreground italic">Archivo digital no disponible</div>
                    )}
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
