import React, { useState, useMemo, FormEvent } from 'react';
import DashboardLayout from '@/Layouts/DashboardLayout';
import { Modal } from '@/components/Modal';
import { router } from '@inertiajs/react';
import { 
  CheckCircle2, Clock3, Inbox as InboxIcon, FileText, ChevronRight 
} from 'lucide-react';
import inboxRoutes from '@/routes/inbox';
import blocksRoutes from '@/routes/blocks';
import { MONTHS } from '@/constants';
import { usePermissions } from '@/hooks/use-permissions';
import Pagination from '@/components/ui/Pagination';
import { Area, PaginationData, Document } from '@/types/models';

interface InboxIndexProps {
  documents: any[]; // Usamos any[] porque son bloques pendientes, no Documentos finales aún
  pagination: PaginationData;
  areas: Area[];
  sections: any[];
  andamios: any[];
  boxes: any[];
  attendedBlocksCount: number;
  unattendedBlocksCount: number;
  filters: {
    search?: string;
    area_id?: string;
    periodo?: string;
  };
  periodos: string[];
}

type StorageForm = { section_id: string; andamio_id: string; box_id: string; };
const emptyStorageForm: StorageForm = { section_id: "", andamio_id: "", box_id: "" };

function formatDateLabel(dateText: string) {
  if (!dateText) return "-";
  try {
    const date = new Date(dateText);
    return `${MONTHS[date.getMonth()]} ${date.getFullYear()}`;
  } catch { return "-"; }
}

export default function Index({ 
  documents = [], 
  pagination,
  areas = [], 
  sections = [], 
  andamios = [], 
  boxes = [], 
  attendedBlocksCount = 0,
  unattendedBlocksCount = 0,
  filters = {}, 
  periodos = [] 
}: InboxIndexProps) {
  const { can } = usePermissions();
  const [f, setF] = useState({
    search: filters?.search || "",
    area_id: filters?.area_id || "",
    periodo: filters?.periodo || ""
  });

  const [storageOpen, setStorageOpen] = useState(false);
  const [uploadOpen, setUploadOpen] = useState(false);
  const [selectedBlock, setSelectedBlock] = useState<any>(null);
  const [storageForm, setStorageForm] = useState<StorageForm>(emptyStorageForm);
  const [uploadFile, setUploadFile] = useState<File | null>(null);
  const [uploadFileName, setUploadFileName] = useState("");
  const [isSubmitting, setIsSubmitting] = useState(false);

  const canUpload = can("blocks.upload");

  const filteredAndamios = useMemo(() => 
    andamios.filter(a => String(a.section_id) === storageForm.section_id), 
  [andamios, storageForm.section_id]);

  const filteredBoxes = useMemo(() => 
    boxes.filter(b => String(b.andamio_id) === storageForm.andamio_id), 
  [boxes, storageForm.andamio_id]);

  const handleFilter = () => router.get(inboxRoutes.index.url(), f, { preserveState: true });

  const handleStorageSubmit = (e: FormEvent) => {
    e.preventDefault();
    if (!storageForm.box_id || !selectedBlock) return;
    setIsSubmitting(true);
    
    const data: any = {
      n_box: storageForm.box_id,
      n_andamio: storageForm.andamio_id,
      n_section: storageForm.section_id,
      _method: 'PUT'
    };

    if (uploadFile) {
      data.root = uploadFile;
    }

    router.post(inboxRoutes.updateStorage.url({ id: selectedBlock.id }), data, {
      forceFormData: true,
      onSuccess: () => { 
        setStorageOpen(false); 
        setStorageForm(emptyStorageForm); 
        setUploadFile(null);
        setUploadFileName("");
      },
      onFinish: () => setIsSubmitting(false)
    });
  };

  return (
    <DashboardLayout title="Bandeja de Entrada">
      <div className="space-y-5">
        <header className="rounded-2xl border border-border bg-gradient-to-r from-slate-900 to-amber-700 p-5 text-white shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
          <div>
            <p className="text-xs uppercase tracking-[0.24em] text-white/60">Flujo de Entrada</p>
            <h2 className="mt-2 text-2xl font-semibold">Bandeja de Pendientes</h2>
            <p className="mt-1 text-sm text-white/75">Organiza y asigna ubicación física a los bloques registrados recientemente.</p>
          </div>
          <div className="flex h-10 items-center gap-2 rounded-xl border border-white/20 bg-white/10 px-4 py-2 backdrop-blur-md self-start sm:self-center">
            <InboxIcon className="h-4 w-4 text-amber-300" />
            <span className="text-xs font-black uppercase tracking-wider text-white">
              {pagination.total} <span className="text-white/60 ml-1">Pendientes</span>
            </span>
          </div>
        </header>

        <div className="grid gap-3 sm:grid-cols-2">
          <article className="rounded-xl border border-border bg-card p-4 shadow-sm flex items-center gap-3">
            <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">
              <CheckCircle2 className="h-5 w-5" />
            </div>
            <div>
              <p className="text-xs text-muted-foreground">Bloques digitalizados</p>
              <p className="mt-1 text-2xl font-bold text-foreground">{attendedBlocksCount}</p>
            </div>
          </article>
          <article className="rounded-xl border border-border bg-card p-4 shadow-sm flex items-center gap-3">
            <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-100 text-amber-600">
              <Clock3 className="h-5 w-5" />
            </div>
            <div>
              <p className="text-xs text-muted-foreground">Pendientes de ubicación</p>
              <p className="mt-1 text-2xl font-bold text-foreground">{unattendedBlocksCount}</p>
            </div>
          </article>
        </div>

        <div className="rounded-xl border border-border bg-card p-4 shadow-sm">
          <div className="grid gap-3 md:grid-cols-3">
            <input value={f.search} onChange={e => setF({...f, search: e.target.value})} placeholder="Nº bloque o asunto" className="h-10 rounded-lg border border-border bg-background px-3 text-sm focus:ring-2 focus:ring-primary/20 outline-none" />
            <select value={f.area_id} onChange={e => setF({...f, area_id: e.target.value})} className="h-10 rounded-lg border border-border bg-background px-3 text-sm focus:ring-2 focus:ring-primary/20 outline-none">
              <option value="">Todas las áreas</option>
              {areas.map(a => <option key={a.id} value={String(a.id)}>{a.descripcion}</option>)}
            </select>
            <select value={f.periodo} onChange={e => setF({...f, periodo: e.target.value})} className="h-10 rounded-lg border border-border bg-background px-3 text-sm focus:ring-2 focus:ring-primary/20 outline-none">
              <option value="">Todos los periodos</option>
              {periodos.map(p => <option key={p} value={p}>{p}</option>)}
            </select>
          </div>
          <div className="mt-4 flex gap-2">
            <button type="button" onClick={handleFilter} className="h-10 px-6 rounded-lg bg-primary text-sm font-semibold text-primary-foreground transition hover:opacity-90">Aplicar filtros</button>
            <button type="button" onClick={() => { setF({search:"", area_id:"", periodo:""}); router.get('/bandeja'); }} className="h-10 px-6 rounded-lg border border-border bg-card text-sm font-semibold text-foreground hover:bg-muted">Limpiar</button>
          </div>
        </div>

        <div className="overflow-hidden rounded-xl border border-border bg-card shadow-sm mb-4">
          <div className="overflow-x-auto">
            <table className="w-full text-sm">
              <thead className="bg-muted/60">
                <tr className="text-left text-xs uppercase tracking-wide text-muted-foreground">
                  <th className="px-4 py-3">#</th>
                  <th className="px-4 py-3">Bloque</th>
                  <th className="px-4 py-3">Origen</th>
                  <th className="px-4 py-3">Estatus</th>
                  <th className="px-4 py-3 text-right">Acciones</th>
                </tr>
              </thead>
              <tbody>
                {documents.map((b, i) => (
                  <tr key={b.id} className="border-t border-border transition-colors hover:bg-muted/30">
                    <td className="px-4 py-3 text-muted-foreground">{pagination.from! + i}</td>
                    <td className="px-4 py-3">
                      <div className="min-w-0">
                        <p className="font-bold text-foreground line-clamp-1">{b.asunto}</p>
                        <div className="flex items-center gap-2 mt-0.5">
                          <span className="text-[10px] font-black uppercase text-muted-foreground bg-muted px-1.5 py-0.5 rounded tracking-tighter">{b.n_bloque}</span>
                          <span className="text-[10px] text-muted-foreground">{formatDateLabel(b.fecha)}</span>
                        </div>
                      </div>
                    </td>
                    <td className="px-4 py-3">
                      <div className="flex flex-col">
                        <span className="text-xs font-semibold text-slate-700">{b.user?.group?.area_group_type?.area?.descripcion || 'Sin área'}</span>
                        <span className="text-[10px] text-muted-foreground">{b.user?.group?.descripcion || 'Sin oficina'}</span>
                      </div>
                    </td>
                    <td className="px-4 py-3">
                      <div className="flex flex-col gap-1">
                        <span className={`inline-flex items-center gap-1 text-[9px] font-black uppercase ${b.root ? 'text-emerald-600' : 'text-amber-600'}`}>
                          <div className={`h-1.5 w-1.5 rounded-full ${b.root ? 'bg-emerald-600' : 'bg-amber-600'}`} />
                          {b.root ? 'Digitalizado' : 'Solo Físico'}
                        </span>
                        <span className={`inline-flex items-center gap-1 text-[9px] font-black uppercase ${b.box_id ? 'text-indigo-600' : 'text-slate-400'}`}>
                          <div className={`h-1.5 w-1.5 rounded-full ${b.box_id ? 'bg-indigo-600' : 'bg-slate-400'}`} />
                          {b.box_id ? 'Archivado' : 'Sin Ubicación'}
                        </span>
                      </div>
                    </td>
                    <td className="px-4 py-3 text-right">
                      <div className="flex justify-end gap-2">
                        {!b.box_id && (
                          <button type="button" onClick={() => { setSelectedBlock(b); setStorageOpen(true); }} className="inline-flex items-center gap-1.5 rounded-md bg-indigo-600 px-3 py-1.5 text-xs font-bold text-white hover:bg-indigo-700 transition-colors">Archivar</button>
                        )}
                      </div>
                    </td>
                  </tr>
                ))}
                {documents.length === 0 && (
                  <tr>
                    <td colSpan={5} className="px-4 py-16 text-center">
                      <div className="flex flex-col items-center justify-center max-w-md mx-auto">
                        <div className="flex h-16 w-16 items-center justify-center rounded-2xl bg-amber-500/10 text-amber-500 mb-4 animate-bounce">
                          <InboxIcon className="h-8 w-8" />
                        </div>
                        <h3 className="text-lg font-semibold text-foreground">Bandeja de entrada vacía</h3>
                        <p className="mt-1 text-sm text-muted-foreground">
                          No hay bloques de documentos pendientes de procesar en esta área.
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
          onPageChange={(page) => router.get(inboxRoutes.index.url(), { ...f, page }, { preserveState: true })}
          label="pendientes"
        />

        {/* MODAL ARCHIVAR Y DIGITALIZAR */}
        <Modal open={storageOpen} title="Completar y Archivar Bloque" onClose={() => setStorageOpen(false)}>
          <form onSubmit={handleStorageSubmit} className="space-y-4 py-2">
            <div className="grid gap-4">
              <div className="space-y-1.5">
                <label className="text-[10px] font-black uppercase text-muted-foreground pl-1">Sección de Archivo</label>
                <select value={storageForm.section_id} onChange={e => setStorageForm({...storageForm, section_id: e.target.value, andamio_id: "", box_id: ""})} className="h-11 w-full rounded-xl border border-border bg-background px-4 text-sm focus:ring-2 focus:ring-primary/20 outline-none" required>
                  <option value="">Seleccione sección...</option>
                  {sections.map(s => <option key={s.id} value={String(s.id)}>Sección {s.n_section}</option>)}
                </select>
              </div>
              <div className="space-y-1.5">
                <label className="text-[10px] font-black uppercase text-muted-foreground pl-1">Andamio / Estante</label>
                <select value={storageForm.andamio_id} onChange={e => setStorageForm({...storageForm, andamio_id: e.target.value, box_id: ""})} disabled={!storageForm.section_id} className="h-11 w-full rounded-xl border border-border bg-background px-4 text-sm focus:ring-2 focus:ring-primary/20 outline-none disabled:opacity-50" required>
                  <option value="">Seleccione andamio...</option>
                  {filteredAndamios.map(a => <option key={a.id} value={String(a.id)}>Andamio {a.n_andamio}</option>)}
                </select>
              </div>
              <div className="space-y-1.5">
                <label className="text-[10px] font-black uppercase text-muted-foreground pl-1">Caja de Destino</label>
                <select value={storageForm.box_id} onChange={e => setStorageForm({...storageForm, box_id: e.target.value})} disabled={!storageForm.andamio_id} className="h-11 w-full rounded-xl border border-border bg-background px-4 text-sm focus:ring-2 focus:ring-primary/20 outline-none disabled:opacity-50" required>
                  <option value="">Seleccione caja...</option>
                  {filteredBoxes.map(b => <option key={b.id} value={String(b.id)}>Caja {b.n_box}</option>)}
                </select>
              </div>
            </div>

            {(!selectedBlock?.root && canUpload) && (
              <div className="space-y-2 mt-4 pt-4 border-t border-border">
                <label className="text-[10px] font-black uppercase text-muted-foreground pl-1">Archivo PDF del Bloque (Opcional)</label>
                <div className="relative group/file">
                  <input type="file" accept=".pdf,application/pdf" onChange={e => {
                    const file = e.target.files?.[0] || null;
                    setUploadFile(file);
                    setUploadFileName(file?.name || "");
                  }} className="block w-full rounded-xl border border-border bg-muted/20 px-4 py-8 text-xs focus:ring-2 focus:ring-primary/20 outline-none border-dashed group-hover/file:bg-muted/40 transition-all cursor-pointer" />
                  {!uploadFile && <div className="absolute inset-0 flex flex-col items-center justify-center pointer-events-none text-muted-foreground"><FileText className="h-8 w-8 mb-2 opacity-30" /><p className="font-medium">Arrastra o selecciona un archivo PDF</p></div>}
                </div>
                {uploadFileName && (
                  <div className="flex items-center gap-2 px-3 py-2 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-100">
                    <CheckCircle2 className="h-4 w-4 shrink-0" />
                    <span className="text-[10px] font-bold truncate">{uploadFileName}</span>
                  </div>
                )}
              </div>
            )}

            <div className="flex justify-end pt-4">
              <button type="submit" disabled={isSubmitting} className="rounded-xl bg-primary px-8 py-3 text-sm font-bold text-primary-foreground shadow-lg shadow-primary/20 transition-all hover:scale-[1.02] disabled:opacity-50">Confirmar Ubicación</button>
            </div>
          </form>
        </Modal>
      </div>
    </DashboardLayout>
  );
}
