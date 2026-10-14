import React, { useState, useMemo, FormEvent } from 'react';
import DashboardLayout from '@/Layouts/DashboardLayout';
import { Modal } from '@/components/Modal';
import { router, usePage, Link } from '@inertiajs/react';
import { 
  Boxes, Eye, FileArchive, Layers, LayoutGrid, Pencil, Trash2, ChevronLeft, ChevronRight, Folder, Plus, AlertCircle, AlertTriangle, Printer, CheckCircle2, Loader2, FileDown, FileUp, FileText, Calendar, Upload 
} from 'lucide-react';
import Pagination from '@/components/ui/Pagination';
import sectionsRoutes from '@/routes/sections';
import andamiosRoutes from '@/routes/andamios';
import boxesRoutes from '@/routes/boxes';
import archivosRoutes from '@/routes/archivos';
import blocksRoutes from '@/routes/blocks';

export type BlockPeriodItem = {
  id?: number;
  rango_inicial: string;
  rango_final: string;
  periodo: string;
};

type BlockEditForm = {
  n_bloque: string;
  asunto: string;
  folios: string;
  fecha: string;
  documentary_series_id: string;
  periods: BlockPeriodItem[];
};

const emptyBlockForm: BlockEditForm = {
  n_bloque: "",
  asunto: "",
  folios: "",
  fecha: "",
  documentary_series_id: "",
  periods: [{ rango_inicial: "", rango_final: "", periodo: "" }],
};

interface StorageIndexProps {
  level: 'sections' | 'andamios' | 'boxes' | 'archivos';
  sections: any[];
  andamios: any[];
  boxes: any[];
  archivos: any[];
  activeSection: any;
  activeAndamio: any;
  activeBox: any;
  filters: {
    search?: string;
    periodo?: string | number;
  };
  years?: number[];
  counts: any; 
  searchedBlocks?: any[];
  documentarySeries?: any[];
  pagination?: any;
  stats?: {
    totalBlocks: number;
    archivedBlocks: number;
    indexedBlocks: number;
    bothArchivedAndIndexed: number;
    totalBoxes: number;
    filledBoxes: number;
    emptyBoxes: number;
  };
}

export default function Index({ 
  level, sections, andamios, boxes, archivos, 
  activeSection, activeAndamio, activeBox, 
  filters, counts, searchedBlocks = [],
  stats, years = [], documentarySeries = [], pagination
}: StorageIndexProps) {
  const { auth } = usePage().props as any;
  const [search, setSearch] = useState(filters?.search || "");
  const [periodo, setPeriodo] = useState(filters?.periodo ? String(filters.periodo) : "");
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [isFiltering, setIsFiltering] = useState(false);
  const [error, setError] = useState("");

  const [sectionForm, setSectionForm] = useState({ n_section: "", descripcion: "" });
  const [andamioForm, setAndamioForm] = useState({ n_andamio: "", descripcion: "" });
  const [boxForm, setBoxForm] = useState({ n_box: "", descripcion: "" });

  const [fileModalOpen, setFileModalOpen] = useState(false);
  const [selectedArchivo, setSelectedArchivo] = useState<any>(null);

  const [deleteModalOpen, setDeleteModalOpen] = useState(false);
  const [itemToDelete, setItemToDelete] = useState<{ id: number; title: string; type: 'item' | 'archivo' } | null>(null);
  const [isDeleting, setIsDeleting] = useState(false);

  const [pdfModalOpen, setPdfModalOpen] = useState(false);
  const [pdfUrl, setPdfUrl] = useState("");
  const [pdfTitle, setPdfTitle] = useState("");

  // Estado y formularios para edición de bloque
  const [editBlockModalOpen, setEditBlockModalOpen] = useState(false);
  const [selectedBlockToEdit, setSelectedBlockToEdit] = useState<any>(null);
  const [blockForm, setBlockForm] = useState<BlockEditForm>(emptyBlockForm);
  const [isEditingBlock, setIsEditingBlock] = useState(false);
  const [editBlockError, setEditBlockError] = useState("");

  // Estado y formulario para actualizar archivo PDF
  const [uploadPdfModalOpen, setUploadPdfModalOpen] = useState(false);
  const [selectedBlockToUpload, setSelectedBlockToUpload] = useState<any>(null);
  const [uploadFile, setUploadFile] = useState<File | null>(null);
  const [uploadFileName, setUploadFileName] = useState("");
  const [uploadError, setUploadError] = useState("");
  const [isUploadingPdf, setIsUploadingPdf] = useState(false);

  const can = (p: string) => auth.permissions?.includes(p) || auth.roles?.some((r: any) => (typeof r === 'string' ? r : r?.name || '').toUpperCase() === 'ADMINISTRADOR');

  const handleSearch = (overridePeriodo?: string, overrideSearch?: string) => {
    setIsFiltering(true);
    const sVal = overrideSearch !== undefined ? overrideSearch : search;
    const pVal = overridePeriodo !== undefined ? overridePeriodo : periodo;
    const queryParams: Record<string, string> = {};
    if (sVal.trim()) queryParams.search = sVal.trim();
    if (pVal) queryParams.periodo = pVal;

    router.get(window.location.pathname, queryParams, {
      preserveState: true,
      preserveScroll: true,
      onFinish: () => setIsFiltering(false),
    });
  };

  const handleClearSearch = () => {
    setSearch("");
    setPeriodo("");
    setIsFiltering(true);
    router.get(window.location.pathname, {}, {
      preserveState: true,
      preserveScroll: true,
      onFinish: () => setIsFiltering(false),
    });
  };

  const handleQuickCreate = (e: FormEvent) => {
    e.preventDefault();
    setIsSubmitting(true);
    let url = "";
    let data = {};

    if (level === 'sections') { url = sectionsRoutes.store.url(); data = sectionForm; }
    else if (level === 'andamios') { url = andamiosRoutes.store.url({ section: activeSection.id }); data = andamioForm; }
    else if (level === 'boxes') { url = boxesRoutes.store.url({ section: activeSection.id, andamio: activeAndamio.id }); data = boxForm; }

    router.post(url, data, {
      preserveScroll: true,
      onSuccess: () => {
        setSectionForm({ n_section: "", descripcion: "" });
        setAndamioForm({ n_andamio: "", descripcion: "" });
        setBoxForm({ n_box: "", descripcion: "" });
      },
      onFinish: () => setIsSubmitting(false)
    });
  };

  const openDeleteModal = (id: number, title: string, type: 'item' | 'archivo' = 'item') => {
    setItemToDelete({ id, title, type });
    setDeleteModalOpen(true);
  };

  const handleConfirmDelete = () => {
    if (!itemToDelete) return;
    setIsDeleting(true);

    if (itemToDelete.type === 'archivo') {
      router.post(archivosRoutes.move.url({ 
        section: activeSection.id, 
        andamio: activeAndamio.id, 
        box: activeBox.id, 
        block: itemToDelete.id 
      }), {}, {
        preserveScroll: true,
        onSuccess: () => setDeleteModalOpen(false),
        onFinish: () => setIsDeleting(false),
      });
    } else {
      let url = "";
      if (level === 'sections') url = sectionsRoutes.destroy.url({ section: itemToDelete.id });
      else if (level === 'andamios') url = andamiosRoutes.destroy.url({ section: activeSection.id, andamio: itemToDelete.id });
      else if (level === 'boxes') url = boxesRoutes.delete.url({ section: activeSection.id, andamio: activeAndamio.id, box: itemToDelete.id });

      router.delete(url, {
        preserveScroll: true,
        onSuccess: () => setDeleteModalOpen(false),
        onFinish: () => setIsDeleting(false),
      });
    }
  };

  const extractISODate = (str: string) => {
    if (!str) return "";
    const match = str.match(/(\d{4})-(\d{2})-(\d{2})/);
    return match ? `${match[1]}-${match[2]}-${match[3]}` : "";
  };

  const calcFolios = (periodsList: BlockPeriodItem[]): string => {
    let total = 0;
    let hasValid = false;
    for (const p of periodsList) {
      const s = parseInt(p.rango_inicial, 10);
      const e = parseInt(p.rango_final, 10);
      if (!isNaN(s) && !isNaN(e) && e >= s) {
        total += (e - s + 1);
        hasValid = true;
      }
    }
    return hasValid ? String(total) : "";
  };

  const openEditBlock = (block: any) => {
    const ds = block.documentary_series || block.documentarySeries;
    setSelectedBlockToEdit(block);
    setEditBlockError("");

    const existingPeriods = (block.periods && block.periods.length > 0)
      ? block.periods.map((p: any) => ({
          id: p.id,
          rango_inicial: String(p.rango_inicial ?? ""),
          rango_final: String(p.rango_final ?? ""),
          periodo: String(p.periodo ?? ""),
        }))
      : [{
          rango_inicial: "",
          rango_final: "",
          periodo: String(block.periodo || (block.fecha ? extractISODate(block.fecha).split("-")[0] : "")),
        }];

    setBlockForm({
      n_bloque: block.n_bloque !== undefined && block.n_bloque !== null ? String(block.n_bloque) : "",
      asunto: block.asunto || "",
      folios: block.folios !== undefined && block.folios !== null ? String(block.folios) : "",
      fecha: extractISODate(block.fecha),
      documentary_series_id: ds ? String(ds.id) : (block.documentary_series_id ? String(block.documentary_series_id) : ""),
      periods: existingPeriods,
    });
    setEditBlockModalOpen(true);
  };

  const handleAddPeriod = () => {
    setBlockForm((prev) => {
      const last = prev.periods[prev.periods.length - 1];
      const nextStart = last && !isNaN(parseInt(last.rango_final, 10))
        ? String(parseInt(last.rango_final, 10) + 1)
        : "";
      const nextYear = last && !isNaN(parseInt(last.periodo, 10))
        ? String(parseInt(last.periodo, 10) + 1)
        : (prev.fecha ? prev.fecha.split("-")[0] : "");

      return {
        ...prev,
        periods: [
          ...prev.periods,
          {
            rango_inicial: nextStart,
            rango_final: "",
            periodo: nextYear,
          },
        ],
      };
    });
  };

  const handleRemovePeriod = (index: number) => {
    setBlockForm((prev) => {
      const nextPeriods = prev.periods.filter((_, i) => i !== index);
      const autoFolios = calcFolios(nextPeriods);
      return {
        ...prev,
        periods: nextPeriods,
        folios: autoFolios || prev.folios,
      };
    });
  };

  const handlePeriodChange = (
    index: number,
    field: "rango_inicial" | "rango_final" | "periodo",
    value: string
  ) => {
    setBlockForm((prev) => {
      const nextPeriods = [...prev.periods];
      nextPeriods[index] = { ...nextPeriods[index], [field]: value };
      const autoFolios = calcFolios(nextPeriods);
      return {
        ...prev,
        periods: nextPeriods,
        folios: autoFolios || prev.folios,
      };
    });
  };

  const handleSubmitEditBlock = (e: FormEvent) => {
    e.preventDefault();
    if (!selectedBlockToEdit) return;
    setIsEditingBlock(true);
    setEditBlockError("");

    if (!blockForm.n_bloque || blockForm.n_bloque.trim() === "") {
      setEditBlockError("El número de bloque es obligatorio.");
      setIsEditingBlock(false);
      return;
    }

    const data: any = { ...blockForm };
    data.n_bloque = parseInt(blockForm.n_bloque, 10);

    if (blockForm.periods && blockForm.periods.length > 0) {
      for (let i = 0; i < blockForm.periods.length; i++) {
        const p = blockForm.periods[i];
        if (!p.rango_inicial || !p.rango_final || !p.periodo) {
          setEditBlockError(`Completa el rango y año del periodo #${i + 1}`);
          setIsEditingBlock(false);
          return;
        }
        if (parseInt(p.rango_final, 10) < parseInt(p.rango_inicial, 10)) {
          setEditBlockError(`En el periodo #${i + 1}, el folio final (${p.rango_final}) no puede ser menor al inicial (${p.rango_inicial})`);
          setIsEditingBlock(false);
          return;
        }
      }
      data.periods = blockForm.periods.map((p) => ({
        rango_inicial: parseInt(p.rango_inicial, 10),
        rango_final: parseInt(p.rango_final, 10),
        periodo: parseInt(p.periodo, 10),
      }));
    }

    const targetUrl = blocksRoutes?.update
      ? blocksRoutes.update.url({ block: selectedBlockToEdit.id })
      : `/bloques/${selectedBlockToEdit.id}`;

    router.post(
      targetUrl,
      { ...data, _method: "PUT" },
      {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
          setEditBlockModalOpen(false);
          setBlockForm(emptyBlockForm);
        },
        onError: (err: any) =>
          setEditBlockError(Object.values(err)[0] as string),
        onFinish: () => setIsEditingBlock(false),
      }
    );
  };

  const openUploadPdf = (block: any) => {
    setSelectedBlockToUpload(block);
    setUploadFile(null);
    setUploadFileName("");
    setUploadError("");
    setUploadPdfModalOpen(true);
  };

  const handleSubmitUploadPdf = (e: FormEvent) => {
    e.preventDefault();
    if (!selectedBlockToUpload) return;
    if (!uploadFile) {
      setUploadError("Por favor selecciona un archivo PDF.");
      return;
    }

    setIsUploadingPdf(true);
    setUploadError("");

    const targetUrl = blocksRoutes?.upload
      ? blocksRoutes.upload.url({ block: selectedBlockToUpload.id })
      : `/bloques/${selectedBlockToUpload.id}/upload`;

    router.post(
      targetUrl,
      { root: uploadFile, _method: "PUT" },
      {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
          setUploadPdfModalOpen(false);
          setUploadFile(null);
          setUploadFileName("");
          if (selectedArchivo && selectedArchivo.id === selectedBlockToUpload.id) {
            setSelectedArchivo({
              ...selectedArchivo,
              root: 'updated',
            });
          }
        },
        onError: (err: any) => {
          setUploadError(err.root || (Object.values(err)[0] as string) || "Error al subir el archivo.");
        },
        onFinish: () => setIsUploadingPdf(false),
      }
    );
  };

  const titleByLevel = {
    sections: "Gestión de Secciones",
    andamios: `Andamios - Sección ${activeSection?.n_section}`,
    boxes: `Cajas - Andamio ${activeAndamio?.n_andamio}`,
    archivos: `Archivos - Caja ${activeBox?.n_box}`
  };

  const iconByLevel = {
    sections: <LayoutGrid className="h-5 w-5" />,
    andamios: <Layers className="h-5 w-5" />,
    boxes: <Boxes className="h-5 w-5" />,
    archivos: <FileArchive className="h-5 w-5" />
  };

  const dataList = level === 'sections' ? sections : (level === 'andamios' ? andamios : (level === 'boxes' ? boxes : archivos));

  return (
    <DashboardLayout title="Almacenamiento">
      <div className="space-y-5">
        <header className="rounded-2xl border border-border bg-gradient-to-r from-slate-900 to-indigo-700 p-5 text-white shadow-sm">
          <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
              <p className="text-xs uppercase tracking-[0.24em] text-white/60">Estructura de Almacén</p>
              <h2 className="mt-2 text-2xl font-semibold">{titleByLevel[level]}</h2>
              <p className="mt-1 text-sm text-white/75">Administra las ubicaciones físicas y organiza el archivo documental.</p>
            </div>
            <span className="rounded-full border border-white/25 bg-white/10 px-3 py-1 text-xs font-semibold">
              {dataList.length} registros
            </span>
          </div>
        </header>

        {/* METRICAS DE CONTADORES DE ALMACENAMIENTO */}
        {stats && (
          <div className="grid gap-3 sm:grid-cols-3">
            <article className="rounded-xl border border-border bg-card p-4 shadow-sm flex items-center gap-3">
              <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600">
                <FileArchive className="h-5 w-5" />
              </div>
              <div>
                <p className="text-xs text-muted-foreground">Bloques archivados</p>
                <p className="mt-1 text-2xl font-bold text-foreground">{stats.archivedBlocks}</p>
              </div>
            </article>

            <article className="rounded-xl border border-border bg-card p-4 shadow-sm flex items-center gap-3">
              <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">
                <CheckCircle2 className="h-5 w-5" />
              </div>
              <div>
                <p className="text-xs text-muted-foreground">Bloques digitalizados</p>
                <p className="mt-1 text-2xl font-bold text-foreground">{stats.indexedBlocks}</p>
              </div>
            </article>

            <article className="rounded-xl border border-border bg-card p-4 shadow-sm flex items-center gap-3">
              <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-100 text-amber-600">
                <Boxes className="h-5 w-5" />
              </div>
              <div>
                <p className="text-xs text-muted-foreground">Cajas ocupadas</p>
                <p className="mt-1 text-2xl font-bold text-foreground">{stats.filledBoxes}</p>
              </div>
            </article>
          </div>
        )}

        {/* BREADCRUMBS DE NAVEGACIÓN */}
        <nav className="flex items-center gap-2 rounded-xl border border-border bg-card p-3 shadow-sm text-xs font-semibold text-muted-foreground overflow-x-auto whitespace-nowrap">
          <Link href={sectionsRoutes.index.url()} className="hover:text-primary transition flex items-center gap-1">
            <LayoutGrid className="h-3.5 w-3.5" />
            <span>Almacén (Secciones)</span>
          </Link>
          {activeSection && (
            <>
              <ChevronRight className="h-3 w-3 shrink-0" />
              <Link href={andamiosRoutes.index.url({ section: activeSection.id })} className="hover:text-primary transition flex items-center gap-1 text-foreground">
                <Layers className="h-3.5 w-3.5" />
                <span>Sección {activeSection.n_section}</span>
              </Link>
            </>
          )}
          {activeAndamio && (
            <>
              <ChevronRight className="h-3 w-3 shrink-0" />
              <Link href={boxesRoutes.index.url({ section: activeSection.id, andamio: activeAndamio.id })} className="hover:text-primary transition flex items-center gap-1 text-foreground">
                <Boxes className="h-3.5 w-3.5" />
                <span>Andamio {activeAndamio.n_andamio}</span>
              </Link>
            </>
          )}
          {activeBox && (
            <>
              <ChevronRight className="h-3 w-3 shrink-0" />
              <span className="flex items-center gap-1 text-primary">
                <FileArchive className="h-3.5 w-3.5" />
                <span>Caja {activeBox.n_box}</span>
              </span>
            </>
          )}
        </nav>

        <div className="rounded-xl border border-border bg-card p-4 shadow-sm">
          <div className="grid gap-3 sm:grid-cols-12 items-center">
            {/* Buscador de texto */}
            <div className="sm:col-span-6 lg:col-span-5">
              <input 
                id="storage-search"
                value={search} 
                onChange={(e) => setSearch(e.target.value)} 
                className="h-10 w-full rounded-lg border border-border bg-background px-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20" 
                placeholder="Buscar por código, serie, asunto, rango o periodo..." 
              />
            </div>

            {/* Filtro manual de periodo */}
            <div className="sm:col-span-3 lg:col-span-3">
              <input
                id="storage-periodo"
                type="number"
                list="storage-years-list"
                value={periodo}
                onChange={(e) => setPeriodo(e.target.value)}
                onKeyDown={(e) => { if (e.key === 'Enter') handleSearch(); }}
                className="h-10 w-full rounded-lg border border-border bg-background px-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20"
                placeholder="Periodo (año ej. 2026)..."
                min="1900"
                max="2100"
              />
              {years && years.length > 0 && (
                <datalist id="storage-years-list">
                  {years.map((y) => (
                    <option key={y} value={y}>
                      Periodo {y}
                    </option>
                  ))}
                </datalist>
              )}
            </div>

            {/* Acciones */}
            <div className="sm:col-span-3 lg:col-span-4 flex items-center gap-2">
              <button 
                onClick={() => handleSearch()} 
                disabled={isFiltering}
                className="h-10 flex-1 rounded-lg bg-primary text-sm font-semibold text-primary-foreground hover:bg-primary/90 transition flex items-center justify-center gap-1.5"
              >
                {isFiltering ? <Loader2 className="h-4 w-4 animate-spin" /> : null}
                <span>Filtrar</span>
              </button>
              {(search || periodo) && (
                <button 
                  onClick={handleClearSearch} 
                  className="h-10 rounded-lg border border-border bg-card px-3 text-sm font-semibold text-foreground hover:bg-muted transition"
                  title="Limpiar filtros"
                >
                  Limpiar
                </button>
              )}
              {level === 'sections' && (
                <a
                  href={sectionsRoutes.report ? sectionsRoutes.report.url() : "/sections/report"}
                  target="_blank"
                  rel="noreferrer"
                  className="inline-flex h-10 px-3 items-center justify-center gap-1.5 rounded-lg bg-red-600 text-sm font-semibold text-white transition hover:bg-red-700 whitespace-nowrap shadow-sm shrink-0"
                  title="Generar Reporte PDF"
                >
                  <Printer className="h-4 w-4" />
                  <span className="hidden lg:inline">Reporte PDF</span>
                </a>
              )}
            </div>
          </div>

          {/* Badges de filtros activos */}
          {(search || periodo) && (
            <div className="mt-3 pt-3 border-t border-border flex flex-wrap items-center gap-2 text-xs">
              <span className="text-muted-foreground font-medium">Filtros activos:</span>
              {search && (
                <span className="inline-flex items-center gap-1 rounded-md bg-primary/10 text-primary px-2 py-0.5 font-semibold">
                  Texto: "{search}"
                  <button 
                    onClick={() => { setSearch(""); handleSearch(undefined, ""); }}
                    className="ml-1 hover:text-red-500 font-bold"
                  >
                    ×
                  </button>
                </span>
              )}
              {periodo && (
                <span className="inline-flex items-center gap-1 rounded-md bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 px-2 py-0.5 font-semibold">
                  Periodo: {periodo}
                  <button 
                    onClick={() => { setPeriodo(""); handleSearch("", undefined); }}
                    className="ml-1 hover:text-red-500 font-bold"
                  >
                    ×
                  </button>
                </span>
              )}
            </div>
          )}
        </div>

        {/* BLOQUES ENCONTRADOS */}
        {searchedBlocks.length > 0 && (
          <div className="rounded-xl border border-border bg-indigo-50/10 dark:bg-indigo-950/10 p-4 shadow-sm space-y-3">
            <h3 className="text-sm font-bold text-indigo-600 dark:text-indigo-400 flex items-center gap-1.5">
              <FileArchive className="h-4 w-4" /> Bloques encontrados en el almacén ({searchedBlocks.length})
            </h3>
            <div className="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
              {searchedBlocks.map((b: any) => {
                const ds = b.documentary_series || b.documentarySeries;
                return (
                  <div key={b.id} className="rounded-lg border border-indigo-200/50 dark:border-indigo-900/50 bg-background p-3 shadow-sm space-y-2">
                    <div>
                      <div className="flex items-start justify-between gap-2">
                        <h4 className="text-[10px] font-black uppercase tracking-wider text-muted-foreground">Código de Bloque / Asunto</h4>
                        {ds && (
                          <span className="inline-flex items-center rounded-md bg-purple-50 dark:bg-purple-950/80 px-1.5 py-0.5 text-[10px] font-semibold text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800 shrink-0" title={`Serie: ${ds.codigo} - ${ds.nombre}`}>
                            {ds.codigo} - {ds.nombre}
                          </span>
                        )}
                      </div>
                      <p className="text-sm font-semibold text-foreground mt-0.5">{b.n_bloque} - {b.asunto}</p>
                      {/* Periodos del bloque */}
                      <div className="mt-1 flex flex-wrap gap-1">
                        {b.periods && b.periods.length > 0 ? (
                          b.periods.map((p: any) => (
                            <span key={p.id || `${p.rango_inicial}-${p.periodo}`} className="inline-flex items-center rounded bg-indigo-50 dark:bg-indigo-950/80 px-1.5 py-0.5 text-[10px] font-semibold text-indigo-800 dark:text-indigo-200 border border-indigo-200/80 dark:border-indigo-800">
                              {p.rango_inicial} - {p.rango_final} (Año {p.periodo})
                            </span>
                          ))
                        ) : b.periodo ? (
                          <span className="inline-flex items-center rounded bg-slate-100 dark:slate-800 px-1.5 py-0.5 text-[10px] font-semibold text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700">
                            Año {b.periodo}
                          </span>
                        ) : null}
                      </div>
                    </div>
                    <div>
                      <h4 className="text-[10px] font-black uppercase tracking-wider text-muted-foreground">Ubicación Física</h4>
                      <div className="flex flex-wrap items-center gap-1 mt-1 text-xs font-medium text-indigo-600 dark:text-indigo-400">
                        <span>Sec. {b.path.section?.n_section}</span>
                        <ChevronRight className="h-3 w-3 text-muted-foreground" />
                        <span>And. {b.path.andamio?.n_andamio}</span>
                        <ChevronRight className="h-3 w-3 text-muted-foreground" />
                        <Link 
                          href={archivosRoutes.index.url({ 
                            section: b.path.section?.id, 
                            andamio: b.path.andamio?.id, 
                            box: b.path.box?.id 
                          })}
                          className="underline hover:text-indigo-800 dark:hover:text-indigo-300 font-bold"
                        >
                          Caja {b.path.box?.n_box}
                        </Link>
                      </div>
                    </div>
                  </div>
                );
              })}
            </div>
          </div>
        )}

        {level !== 'archivos' && can('sections.create') && (
          <div className="rounded-xl border border-border bg-card p-4 shadow-sm">
            <h3 className="text-sm font-semibold text-foreground">Registrar nuevo/a {level.slice(0, -1)}</h3>
            <form onSubmit={handleQuickCreate} className="mt-3 flex flex-col gap-2 md:flex-row">
              <input 
                id="item_number"
                name="number"
                type="number" 
                min={1} 
                required
                value={level === 'sections' ? sectionForm.n_section : (level === 'andamios' ? andamioForm.n_andamio : boxForm.n_box)}
                onChange={(e) => {
                  if (level === 'sections') setSectionForm({...sectionForm, n_section: e.target.value});
                  else if (level === 'andamios') setAndamioForm({...andamioForm, n_andamio: e.target.value});
                  else setBoxForm({...boxForm, n_box: e.target.value});
                }}
                className="h-10 rounded-lg border border-border bg-background px-3 text-sm md:w-40" 
                placeholder="Número" 
              />
              <input 
                id="item_description"
                name="description"
                value={level === 'sections' ? sectionForm.descripcion : (level === 'andamios' ? andamioForm.descripcion : boxForm.descripcion)}
                onChange={(e) => {
                  if (level === 'sections') setSectionForm({...sectionForm, descripcion: e.target.value});
                  else if (level === 'andamios') setAndamioForm({...andamioForm, descripcion: e.target.value});
                  else setBoxForm({...boxForm, descripcion: e.target.value});
                }}
                className="h-10 flex-1 rounded-lg border border-border bg-background px-3 text-sm" 
                placeholder="Descripción / Observaciones" 
              />
              <button id="save-storage-item" type="submit" disabled={isSubmitting} className="h-10 rounded-lg bg-emerald-600 px-6 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-700">
                <Plus className="h-4 w-4 inline mr-1" /> Guardar
              </button>
            </form>
          </div>
        )}

        {level !== 'archivos' ? (
          dataList.length === 0 ? (
            <div className="rounded-xl sm:rounded-2xl border border-dashed border-border bg-card p-8 sm:p-16 text-center shadow-sm">
              <div className="flex flex-col items-center justify-center max-w-md mx-auto">
                <div className="flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-500/10 text-indigo-600 mb-4 animate-bounce">
                  {level === 'sections' ? (
                    <LayoutGrid className="h-8 w-8" />
                  ) : level === 'andamios' ? (
                    <Layers className="h-8 w-8" />
                  ) : (
                    <Boxes className="h-8 w-8" />
                  )}
                </div>
                <h3 className="text-lg sm:text-xl font-bold text-foreground">
                  No se encontraron {level === 'sections' ? 'secciones' : level === 'andamios' ? 'andamios' : 'cajas'}
                </h3>
                <p className="mt-2 text-sm text-muted-foreground">
                  No hay registros creados en este nivel de almacenamiento o ningún registro coincide con la búsqueda.
                </p>
              </div>
            </div>
          ) : (
            <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
              {dataList.map((item: any) => {
                const childCount = item.andamios_count ?? item.boxes_count ?? item.blocks_count ?? 0;
                const maxCapacity = level === 'sections' ? 10 : (level === 'andamios' ? 20 : 15);
                const percent = Math.min(Math.round((childCount / maxCapacity) * 100), 100);
                const barColor = percent >= 90 
                  ? "bg-rose-500" 
                  : percent >= 60 
                  ? "bg-amber-500" 
                  : "bg-emerald-500";
                return (
                  <article key={item.id} className="rounded-xl border border-border bg-card p-4 shadow-sm transition-all hover:border-primary/30 flex flex-col justify-between">
                    <div>
                      <div className="flex items-start justify-between gap-2">
                        <div>
                          <h4 className="text-base font-bold text-foreground">
                            {level === 'sections' ? `Sección ${item.n_section}` : (level === 'andamios' ? `Andamio ${item.n_andamio}` : `Caja ${item.n_box}`)}
                          </h4>
                          <p className="mt-1 text-sm text-muted-foreground line-clamp-2">{item.descripcion || "Sin descripción adicional"}</p>
                        </div>
                        <div className="rounded-lg bg-muted/50 px-2 py-1 text-[10px] font-black uppercase text-muted-foreground shrink-0">
                          {childCount} {level === 'sections' ? 'andamios' : (level === 'andamios' ? 'cajas' : 'archivos')}
                        </div>
                      </div>

                      {/* Barra de progreso de capacidad */}
                      <div className="mt-4 space-y-1">
                        <div className="flex items-center justify-between text-[10px] font-bold text-muted-foreground uppercase">
                          <span>Ocupación</span>
                          <span>{childCount} / {maxCapacity} ({percent}%)</span>
                        </div>
                        <div className="h-1.5 w-full rounded-full bg-muted overflow-hidden">
                          <div className={`h-full rounded-full ${barColor} transition-all duration-300`} style={{ width: `${percent}%` }}></div>
                        </div>
                      </div>
                    </div>
                               <div className="mt-5 flex flex-wrap gap-2 pt-3 border-t border-border/40">
                      <Link 
                        href={level === 'sections' ? andamiosRoutes.index.url({ section: item.id }) : (level === 'andamios' ? boxesRoutes.index.url({ section: activeSection.id, andamio: item.id }) : archivosRoutes.index.url({ section: activeSection.id, andamio: activeAndamio.id, box: item.id }))}
                        className="inline-flex items-center gap-1.5 rounded-md bg-sky-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-sky-700 transition"
                      >
                        <Eye className="h-3.5 w-3.5" /> Ver contenido
                      </Link>
                      {can('sections.delete') && (
                        <button 
                          onClick={() => openDeleteModal(item.id, `Código ${item.n_section || item.n_andamio || item.n_box}`, 'item')} 
                          className="p-1.5 rounded-lg text-muted-foreground hover:bg-red-50 hover:text-red-600 transition"
                          title="Eliminar"
                        >
                          <Trash2 className="h-4 w-4" />
                        </button>
                      )}
                    </div>
                  </article>
                );
              })}
            </div>
          )
        ) : (
          /* LEVEL === ARCHIVOS TABLE */
          <div className="overflow-hidden rounded-xl border border-border bg-card shadow-sm">
            <div className="flex flex-wrap items-center justify-between gap-2 border-b border-border bg-muted/40 px-4 py-2.5 text-xs">
              <span className="font-semibold text-foreground">
                Archivos en esta caja: <span className="font-bold text-primary">{pagination?.total ?? archivos.length}</span>
              </span>
              {(search || periodo) && (
                <span className="inline-flex items-center gap-1 rounded bg-primary/10 px-2 py-0.5 text-[11px] font-medium text-primary">
                  Filtrando por criterios activos
                </span>
              )}
            </div>
            <div className="overflow-x-auto">
              <table className="w-full text-sm">
                <thead className="bg-muted/60">
                  <tr className="text-left text-xs uppercase tracking-wide text-muted-foreground">
                    <th className="px-4 py-3">#</th>
                    <th className="px-4 py-3">Bloque</th>
                    <th className="px-4 py-3">Serie / Folios</th>
                    <th className="px-4 py-3">Documento Digital</th>
                    <th className="px-4 py-3 text-right">Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  {archivos.length === 0 ? (
                    <tr>
                      <td colSpan={5} className="px-6 py-12 text-center text-muted-foreground">
                        <FileArchive className="h-8 w-8 mx-auto mb-2 opacity-30" />
                        <p className="text-sm font-medium">
                          {search || periodo 
                            ? "No se encontraron archivos que coincidan con los criterios de búsqueda." 
                            : "No hay archivos en esta caja."}
                        </p>
                      </td>
                    </tr>
                  ) : (
                    archivos.map((a, i) => (
                      <tr key={a.id} className="border-t border-border transition hover:bg-muted/30">
                        <td className="px-4 py-3 text-muted-foreground">
                          {pagination?.from ? pagination.from + i : i + 1}
                        </td>
                        <td className="px-4 py-3">
                          <p className="font-bold text-foreground">{a.asunto}</p>
                          <div className="flex flex-wrap items-center gap-2 mt-0.5">
                            <p className="text-[10px] text-muted-foreground">
                              Nº Bloque: <span className="font-mono font-bold text-foreground">{a.n_bloque}</span>
                            </p>
                            {a.periods && a.periods.length > 0 ? (
                              <div className="flex flex-wrap gap-1">
                                {a.periods.map((p: any, pIdx: number) => (
                                  <span key={pIdx} className="rounded bg-indigo-50 dark:bg-indigo-950/80 px-1.5 py-0.5 text-[9px] font-mono font-semibold text-indigo-800 dark:text-indigo-200 border border-indigo-200/80 dark:border-indigo-800" title={`Folios ${p.rango_inicial}-${p.rango_final}`}>
                                    {p.rango_inicial} - {p.rango_final} (Año {p.periodo})
                                  </span>
                                ))}
                              </div>
                            ) : a.periodo ? (
                              <span className="rounded bg-muted px-1.5 py-0.5 text-[9px] font-mono text-muted-foreground border border-border">
                                Año {a.periodo}
                              </span>
                            ) : null}
                          </div>
                        </td>
                        <td className="px-4 py-3">
                          {(() => {
                            const ds = a.documentary_series || a.documentarySeries;
                            return (
                              <p className="text-xs font-medium text-foreground" title={ds ? `${ds.codigo} - ${ds.nombre}` : undefined}>
                                {ds ? `${ds.codigo} - ${ds.nombre}` : <span className="text-muted-foreground italic text-xs">Sin serie</span>}
                              </p>
                            );
                          })()}
                          <p className="text-[10px] text-muted-foreground">{a.folios || 0} folios</p>
                        </td>
                        <td className="px-4 py-3">
                          {a.root ? (
                            <span className="inline-flex items-center gap-1.5 rounded-full border border-emerald-200 bg-emerald-50 dark:bg-emerald-950/40 dark:border-emerald-800 px-2.5 py-0.5 text-xs font-semibold text-emerald-700 dark:text-emerald-300">
                              <CheckCircle2 className="h-3.5 w-3.5" /> Digitalizado
                            </span>
                          ) : (
                            <span className="inline-flex items-center gap-1.5 rounded-full border border-amber-200 bg-amber-50 dark:bg-amber-950/40 dark:border-amber-800 px-2.5 py-0.5 text-xs font-semibold text-amber-700 dark:text-amber-300">
                              <AlertCircle className="h-3.5 w-3.5" /> Sin PDF
                            </span>
                          )}
                        </td>
                        <td className="px-4 py-3 text-right">
                          <div className="flex justify-end gap-1.5 flex-wrap">
                            {a.root && (
                              <button
                                type="button"
                                onClick={() => {
                                  setSelectedArchivo(a);
                                  setFileModalOpen(true);
                                }}
                                className="inline-flex items-center gap-1 rounded-md bg-indigo-600 px-2.5 py-1.5 text-xs font-semibold text-white hover:bg-indigo-700 transition shadow-sm"
                                title="Ver documento PDF original"
                              >
                                <Eye className="h-3.5 w-3.5" />
                                <span>Ver</span>
                              </button>
                            )}

                            <button
                              type="button"
                              onClick={() => openEditBlock(a)}
                              className="inline-flex items-center gap-1 rounded-md bg-amber-500 px-2.5 py-1.5 text-xs font-semibold text-white hover:bg-amber-600 transition shadow-sm"
                              title="Editar información del bloque"
                            >
                              <Pencil className="h-3.5 w-3.5" />
                              <span>Editar</span>
                            </button>

                            <button
                              type="button"
                              onClick={() => openUploadPdf(a)}
                              className="inline-flex items-center gap-1 rounded-md bg-emerald-600 px-2.5 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700 transition shadow-sm"
                              title={a.root ? "Actualizar / Reemplazar archivo PDF" : "Subir archivo PDF"}
                            >
                              <FileUp className="h-3.5 w-3.5" />
                              <span>{a.root ? "Actualizar PDF" : "Subir PDF"}</span>
                            </button>

                            <button 
                              type="button"
                              onClick={() => openDeleteModal(a.id, `Bloque ${a.n_bloque}`, 'archivo')}
                              className="inline-flex items-center gap-1 rounded-md bg-rose-600 px-2.5 py-1.5 text-xs font-semibold text-white hover:bg-rose-700 transition shadow-sm"
                              title="Retirar de esta caja"
                            >
                              <Trash2 className="h-3.5 w-3.5" />
                              <span>Retirar</span>
                            </button>
                          </div>
                        </td>
                      </tr>
                    ))
                  )}
                </tbody>
              </table>
            </div>

            {pagination && pagination.total > (pagination.per_page || 10) && (
              <div className="p-3 border-t border-border">
                <Pagination
                  {...pagination}
                  onPageChange={(page) => {
                    const queryParams: Record<string, string> = {};
                    if (search.trim()) queryParams.search = search.trim();
                    if (periodo) queryParams.periodo = periodo;
                    queryParams.page = String(page);
                    router.get(window.location.pathname, queryParams, {
                      preserveState: true,
                      preserveScroll: true,
                    });
                  }}
                  label="archivos"
                />
              </div>
            )}
          </div>
        )}

        {/* MODAL CONFIRMAR ELIMINACIÓN */}
        <Modal 
          open={deleteModalOpen} 
          title="Confirmar Eliminación" 
          onClose={() => { if (!isDeleting) setDeleteModalOpen(false); }} 
          maxWidth="max-w-md"
        >
          <div className="space-y-4 text-center py-2">
            <div className="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-red-100 text-red-600 dark:bg-red-950/50 dark:text-red-400">
              <AlertTriangle className="h-7 w-7" />
            </div>
            <div>
              <h4 className="text-base font-semibold text-foreground">
                ¿Eliminar / Retirar {itemToDelete?.title}?
              </h4>
              <p className="mt-1 text-xs text-muted-foreground">
                {itemToDelete?.type === 'archivo'
                  ? "Esta acción retirará la asignación física del archivo de esta caja de almacenamiento."
                  : "Esta acción eliminará el elemento seleccionado de la estructura de almacén."}
              </p>
            </div>
            <div className="flex justify-center gap-3 pt-2">
              <button
                type="button"
                onClick={() => setDeleteModalOpen(false)}
                disabled={isDeleting}
                className="rounded-lg border border-border bg-background px-4 py-2 text-sm font-medium text-foreground hover:bg-muted disabled:opacity-50"
              >
                Cancelar
              </button>
              <button
                type="button"
                onClick={handleConfirmDelete}
                disabled={isDeleting}
                className="inline-flex items-center gap-2 rounded-lg bg-red-600 px-5 py-2 text-sm font-bold text-white hover:bg-red-700 disabled:opacity-50 transition"
              >
                {isDeleting ? (
                  <>
                    <Loader2 className="h-4 w-4 animate-spin" /> Procesando...
                  </>
                ) : (
                  <>
                    <Trash2 className="h-4 w-4" /> Confirmar
                  </>
                )}
              </button>
            </div>
          </div>
        </Modal>

        {/* MODAL PREVISUALIZADOR DE REPORTE PDF IN-APP */}
        <Modal
          open={pdfModalOpen}
          title={pdfTitle || "Previsualizador de Reporte PDF"}
          onClose={() => setPdfModalOpen(false)}
          maxWidth="max-w-5xl"
        >
          <div className="space-y-3">
            <div className="flex justify-end gap-2">
              <a
                href={pdfUrl}
                target="_blank"
                rel="noreferrer"
                className="inline-flex items-center gap-1.5 rounded-lg border border-border bg-card px-3.5 py-1.5 text-xs font-semibold text-foreground hover:bg-muted transition"
              >
                <FileDown className="h-3.5 w-3.5" /> Abrir en pestaña nueva
              </a>
            </div>
            <div className="h-[70vh] w-full overflow-hidden rounded-xl border border-border bg-slate-900 shadow-inner">
              <iframe
                src={pdfUrl}
                className="h-full w-full border-0"
                title="Reporte PDF Almacén"
              />
            </div>
          </div>
        </Modal>

        {/* MODAL VISOR DE PDF CON DETALLES */}
        <Modal 
            open={fileModalOpen} 
            title={`Visor de Documento: Bloque ${selectedArchivo?.n_bloque || ''}`} 
            onClose={() => {
                setFileModalOpen(false);
                setSelectedArchivo(null);
            }}
            maxWidth="max-w-6xl"
        >
            <div className="flex flex-col lg:flex-row gap-6 mt-4">
                {/* Panel de Detalles */}
                {selectedArchivo && (
                  <div className="w-full lg:w-1/3 shrink-0 space-y-6">
                    <div className="rounded-xl border border-border bg-muted/20 p-5">
                      <h3 className="font-semibold text-foreground border-b border-border pb-3 mb-4">Información del Expediente</h3>
                      <div className="space-y-4">
                        <div>
                          <p className="text-[10px] font-black uppercase text-muted-foreground">ID del Sistema</p>
                          <p className="text-sm font-mono mt-0.5">{selectedArchivo.id}</p>
                        </div>
                        <div>
                          <p className="text-[10px] font-black uppercase text-muted-foreground">Número de Bloque</p>
                          <p className="text-sm font-medium mt-0.5">{selectedArchivo.n_bloque}</p>
                        </div>
                        <div>
                          <p className="text-[10px] font-black uppercase text-muted-foreground">Asunto / Contenido</p>
                          <p className="text-sm font-medium mt-0.5">{selectedArchivo.asunto}</p>
                        </div>
                        <div className="grid grid-cols-2 gap-4">
                          <div>
                            <p className="text-[10px] font-black uppercase text-muted-foreground">Nº Folios</p>
                            <p className="text-sm font-medium mt-0.5">{selectedArchivo.folios}</p>
                          </div>
                          <div>
                            <p className="text-[10px] font-black uppercase text-muted-foreground">Periodo</p>
                            <p className="text-sm font-medium mt-0.5">{selectedArchivo.periodo}</p>
                          </div>
                        </div>
                        <div>
                          <p className="text-[10px] font-black uppercase text-muted-foreground">Fecha de Registro</p>
                          <p className="text-sm font-medium mt-0.5">
                            {new Date(selectedArchivo.created_at).toLocaleDateString('es-ES', { year: 'numeric', month: 'long', day: 'numeric' })}
                          </p>
                        </div>

                        {/* Botones de acción rápida dentro del visor */}
                        <div className="pt-3 border-t border-border flex flex-col gap-2">
                          <button
                            type="button"
                            onClick={() => {
                              const target = selectedArchivo;
                              setFileModalOpen(false);
                              openEditBlock(target);
                            }}
                            className="w-full inline-flex items-center justify-center gap-1.5 rounded-lg bg-amber-500 px-3 py-2 text-xs font-bold text-white hover:bg-amber-600 transition shadow-sm"
                          >
                            <Pencil className="h-3.5 w-3.5" /> Editar Información
                          </button>
                          <button
                            type="button"
                            onClick={() => {
                              const target = selectedArchivo;
                              setFileModalOpen(false);
                              openUploadPdf(target);
                            }}
                            className="w-full inline-flex items-center justify-center gap-1.5 rounded-lg bg-emerald-600 px-3 py-2 text-xs font-bold text-white hover:bg-emerald-700 transition shadow-sm"
                          >
                            <FileUp className="h-3.5 w-3.5" /> Actualizar Archivo PDF
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                )}

                {/* Visor Iframe */}
                <div className="h-[70vh] w-full lg:w-2/3 rounded-xl overflow-hidden border border-border">
                    {selectedArchivo?.root ? (
                        <iframe src={`/bloques/${selectedArchivo.id}/file`} className="w-full h-full" title="Visor PDF" />
                    ) : (
                        <div className="flex flex-col items-center justify-center h-full bg-muted/10 text-muted-foreground p-6 text-center">
                            <AlertCircle className="h-10 w-10 mb-3 opacity-20" />
                            <p>Cargando documento o archivo no disponible...</p>
                        </div>
                    )}
                </div>
            </div>
        </Modal>

        {/* MODAL EDITAR INFORMACIÓN DEL BLOQUE */}
        <Modal
          open={editBlockModalOpen}
          title={`Editar Información del Bloque ${selectedBlockToEdit?.n_bloque ? `Nº ${selectedBlockToEdit.n_bloque}` : ''}`}
          onClose={() => {
            if (!isEditingBlock) {
              setEditBlockModalOpen(false);
              setBlockForm(emptyBlockForm);
            }
          }}
          maxWidth="max-w-2xl"
        >
          <form onSubmit={handleSubmitEditBlock} className="space-y-4 pt-2">
            {editBlockError && (
              <div className="rounded-lg border border-red-300 bg-red-50 dark:bg-red-950/40 p-3 text-xs text-red-700 dark:text-red-300 flex items-center gap-2">
                <AlertCircle className="h-4 w-4 shrink-0" />
                <span>{editBlockError}</span>
              </div>
            )}

            <div className="grid gap-3 md:grid-cols-2">
              <div className="space-y-1">
                <label className="text-xs font-semibold text-muted-foreground flex items-center justify-between">
                  <span>Número de Bloque <span className="text-rose-500">*</span></span>
                </label>
                <input
                  type="number"
                  min="1"
                  value={blockForm.n_bloque}
                  onChange={(e) => setBlockForm({ ...blockForm, n_bloque: e.target.value })}
                  placeholder="Ej. 45"
                  className="h-10 w-full rounded-lg border border-border bg-background px-3 text-sm outline-none focus:ring-2 focus:ring-primary/20"
                  required
                />
              </div>

              <div className="space-y-1">
                <label className="text-xs font-semibold text-muted-foreground">Serie Documental</label>
                <select
                  value={blockForm.documentary_series_id}
                  onChange={(e) => setBlockForm({ ...blockForm, documentary_series_id: e.target.value })}
                  className="h-10 w-full rounded-lg border border-border bg-background px-3 text-sm outline-none focus:ring-2 focus:ring-primary/20"
                >
                  <option value="">Seleccione Serie Documental (Opcional)...</option>
                  {documentarySeries.map((ds: any) => (
                    <option key={ds.id} value={String(ds.id)}>
                      {ds.codigo} - {ds.nombre}
                    </option>
                  ))}
                </select>
              </div>

              <div className="space-y-1">
                <label className="text-xs font-semibold text-muted-foreground">Folios <span className="text-rose-500">*</span></label>
                <input
                  value={blockForm.folios}
                  onChange={(e) => setBlockForm({ ...blockForm, folios: e.target.value })}
                  placeholder="Ej. 120"
                  className="h-10 w-full rounded-lg border border-border bg-background px-3 text-sm outline-none focus:ring-2 focus:ring-primary/20"
                  required
                />
              </div>

              <div className="space-y-1">
                <label className="text-xs font-semibold text-muted-foreground">Fecha del Bloque <span className="text-rose-500">*</span></label>
                <input
                  type="date"
                  value={blockForm.fecha}
                  onChange={(e) => {
                    const newDate = e.target.value;
                    const newYear = newDate ? newDate.split("-")[0] : "";
                    setBlockForm((prev) => {
                      const updatedPeriods = (prev.periods.length <= 1 && (!prev.periods[0]?.periodo || prev.periods[0]?.periodo === prev.fecha?.split("-")[0]))
                        ? [{
                            rango_inicial: prev.periods[0]?.rango_inicial || "",
                            rango_final: prev.periods[0]?.rango_final || "",
                            periodo: newYear,
                          }]
                        : prev.periods;
                      return { ...prev, fecha: newDate, periods: updatedPeriods };
                    });
                  }}
                  className="h-10 w-full rounded-lg border border-border bg-background px-3 text-sm outline-none focus:ring-2 focus:ring-primary/20"
                  required
                />
              </div>

              <div className="space-y-1 md:col-span-2">
                <label className="text-xs font-semibold text-muted-foreground">Asunto / Descripción <span className="text-rose-500">*</span></label>
                <input
                  value={blockForm.asunto}
                  onChange={(e) => setBlockForm({ ...blockForm, asunto: e.target.value })}
                  placeholder="Asunto descriptivo del bloque"
                  className="h-10 w-full rounded-lg border border-border bg-background px-3 text-sm outline-none focus:ring-2 focus:ring-primary/20"
                  required
                />
              </div>

              {/* SECCIÓN DE PERIODOS Y SUB-RANGOS */}
              <div className="space-y-3 md:col-span-2 rounded-xl border border-border/80 bg-muted/20 p-3.5">
                <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                  <div>
                    <h4 className="text-xs font-bold text-foreground flex items-center gap-1.5">
                      <Calendar className="h-3.5 w-3.5 text-primary" />
                      Distribución de Documentos por Periodo (Años)
                    </h4>
                    <p className="text-[11px] text-muted-foreground">
                      Si el bloque contiene documentos de distintos periodos, organízalos por rango aquí.
                    </p>
                  </div>
                  <button
                    type="button"
                    onClick={handleAddPeriod}
                    className="inline-flex items-center gap-1.5 rounded-lg border border-primary/30 bg-primary/10 px-2.5 py-1 text-xs font-semibold text-primary hover:bg-primary/20 transition self-start sm:self-auto"
                  >
                    <Plus className="h-3.5 w-3.5" /> Dividir / Agregar periodo
                  </button>
                </div>

                <div className="space-y-2 pt-1">
                  {blockForm.periods.map((p, pIdx) => (
                    <div
                      key={pIdx}
                      className="grid grid-cols-12 gap-2 items-center rounded-lg border border-border bg-background p-2.5 shadow-sm"
                    >
                      <div className="col-span-4 sm:col-span-3 space-y-1">
                        <label className="text-[10px] font-bold text-muted-foreground uppercase">Desde</label>
                        <input
                          type="number"
                          value={p.rango_inicial}
                          onChange={(e) => handlePeriodChange(pIdx, "rango_inicial", e.target.value)}
                          placeholder="Ej. 1"
                          className="h-8 w-full rounded-md border border-border bg-background px-2 text-xs font-mono"
                          required
                        />
                      </div>
                      <div className="col-span-4 sm:col-span-3 space-y-1">
                        <label className="text-[10px] font-bold text-muted-foreground uppercase">Hasta</label>
                        <input
                          type="number"
                          value={p.rango_final}
                          onChange={(e) => handlePeriodChange(pIdx, "rango_final", e.target.value)}
                          placeholder="Ej. 60"
                          className="h-8 w-full rounded-md border border-border bg-background px-2 text-xs font-mono"
                          required
                        />
                      </div>
                      <div className="col-span-4 sm:col-span-4 space-y-1">
                        <label className="text-[10px] font-bold text-muted-foreground uppercase">Periodo (Año)</label>
                        <input
                          type="number"
                          value={p.periodo}
                          onChange={(e) => handlePeriodChange(pIdx, "periodo", e.target.value)}
                          placeholder="Ej. 2026"
                          className="h-8 w-full rounded-md border border-border bg-background px-2 text-xs font-mono font-bold"
                          required
                        />
                      </div>
                      <div className="col-span-12 sm:col-span-2 flex justify-end items-end pt-2 sm:pt-0">
                        {blockForm.periods.length > 1 ? (
                          <button
                            type="button"
                            onClick={() => handleRemovePeriod(pIdx)}
                            className="inline-flex items-center gap-1 rounded-md p-1.5 text-xs text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition"
                            title="Eliminar este periodo"
                          >
                            <Trash2 className="h-4 w-4" />
                            <span className="sm:hidden text-xs">Quitar</span>
                          </button>
                        ) : (
                          <span className="text-[10px] text-muted-foreground italic hidden sm:inline">Único</span>
                        )}
                      </div>
                    </div>
                  ))}
                </div>
              </div>
            </div>

            <div className="flex justify-end gap-2 pt-3 border-t border-border">
              <button
                type="button"
                onClick={() => {
                  setEditBlockModalOpen(false);
                  setBlockForm(emptyBlockForm);
                }}
                disabled={isEditingBlock}
                className="rounded-lg border border-border bg-background px-4 py-2 text-sm font-medium text-foreground hover:bg-muted disabled:opacity-50 transition"
              >
                Cancelar
              </button>
              <button
                type="submit"
                disabled={isEditingBlock}
                className="inline-flex items-center gap-2 rounded-lg bg-amber-500 px-6 py-2 text-sm font-bold text-white hover:bg-amber-600 disabled:opacity-50 transition shadow-sm"
              >
                {isEditingBlock ? (
                  <>
                    <Loader2 className="h-4 w-4 animate-spin" /> Guardando...
                  </>
                ) : (
                  "Guardar Cambios"
                )}
              </button>
            </div>
          </form>
        </Modal>

        {/* MODAL ACTUALIZAR ARCHIVO PDF */}
        <Modal
          open={uploadPdfModalOpen}
          title={`Actualizar Archivo PDF - Bloque Nº ${selectedBlockToUpload?.n_bloque || ''}`}
          onClose={() => {
            if (!isUploadingPdf) {
              setUploadPdfModalOpen(false);
              setUploadFile(null);
              setUploadFileName("");
              setUploadError("");
            }
          }}
          maxWidth="max-w-lg"
        >
          <form onSubmit={handleSubmitUploadPdf} className="space-y-4 pt-2">
            {selectedBlockToUpload && (
              <div className="rounded-xl border border-border bg-muted/20 p-3.5 space-y-1">
                <p className="text-[10px] font-black uppercase text-muted-foreground tracking-wider">Asunto del Bloque</p>
                <p className="text-sm font-semibold text-foreground line-clamp-2">{selectedBlockToUpload.asunto}</p>
                {selectedBlockToUpload.root ? (
                  <div className="mt-2 flex items-center gap-2 rounded-lg border border-emerald-200 bg-emerald-50 dark:bg-emerald-950/40 dark:border-emerald-800 p-2 text-xs text-emerald-800 dark:text-emerald-300">
                    <CheckCircle2 className="h-4 w-4 shrink-0 text-emerald-600" />
                    <span>Este bloque ya cuenta con un documento digital. Seleccionar un nuevo archivo reemplazará el PDF existente.</span>
                  </div>
                ) : (
                  <div className="mt-2 flex items-center gap-2 rounded-lg border border-amber-200 bg-amber-50 dark:bg-amber-950/40 dark:border-amber-800 p-2 text-xs text-amber-800 dark:text-amber-300">
                    <AlertCircle className="h-4 w-4 shrink-0 text-amber-600" />
                    <span>Este bloque aún no tiene archivo digital adjunto.</span>
                  </div>
                )}
              </div>
            )}

            {uploadError && (
              <div className="rounded-lg border border-red-300 bg-red-50 dark:bg-red-950/40 p-3 text-xs text-red-700 dark:text-red-300 flex items-center gap-2">
                <AlertCircle className="h-4 w-4 shrink-0" />
                <span>{uploadError}</span>
              </div>
            )}

            <div className="space-y-2">
              <label className="text-xs font-semibold text-muted-foreground flex items-center justify-between">
                <span>Seleccionar Documento PDF <span className="text-rose-500">*</span></span>
                <span className="text-[10px] font-normal text-muted-foreground">Máximo 50 MB</span>
              </label>

              <div className="relative group/file">
                <input
                  type="file"
                  accept=".pdf,application/pdf"
                  required
                  onChange={(e) => {
                    const file = e.target.files?.[0] || null;
                    if (file && file.size > 50 * 1024 * 1024) {
                      setUploadError(`El archivo supera el tamaño máximo permitido de 50 MB (peso: ${(file.size / (1024 * 1024)).toFixed(1)} MB).`);
                      e.target.value = "";
                      setUploadFile(null);
                      setUploadFileName("");
                      return;
                    }
                    setUploadError("");
                    setUploadFile(file);
                    setUploadFileName(file?.name || "");
                  }}
                  className="block w-full rounded-xl border border-border bg-muted/20 px-4 py-8 text-xs focus:ring-2 focus:ring-primary/20 outline-none border-dashed group-hover/file:bg-muted/40 transition-all cursor-pointer"
                />
                {!uploadFile && (
                  <div className="absolute inset-0 flex flex-col items-center justify-center pointer-events-none text-muted-foreground">
                    <Upload className="h-8 w-8 mb-2 opacity-40 text-emerald-600" />
                    <p className="text-xs font-semibold text-foreground">Haz clic o arrastra un archivo PDF aquí</p>
                    <p className="text-[10px] text-muted-foreground mt-0.5">Solo formato PDF (.pdf)</p>
                  </div>
                )}
              </div>

              {uploadFileName && (
                <div className="flex items-center gap-2 px-3 py-2 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-100 dark:border-emerald-800">
                  <CheckCircle2 className="h-4 w-4 shrink-0 text-emerald-600" />
                  <span className="text-xs font-bold truncate flex-1">{uploadFileName}</span>
                  {uploadFile && (
                    <span className="text-[10px] font-mono text-muted-foreground">
                      {(uploadFile.size / (1024 * 1024)).toFixed(2)} MB
                    </span>
                  )}
                </div>
              )}
            </div>

            <div className="flex justify-end gap-2 pt-3 border-t border-border">
              <button
                type="button"
                onClick={() => {
                  setUploadPdfModalOpen(false);
                  setUploadFile(null);
                  setUploadFileName("");
                  setUploadError("");
                }}
                disabled={isUploadingPdf}
                className="rounded-lg border border-border bg-background px-4 py-2 text-sm font-medium text-foreground hover:bg-muted disabled:opacity-50 transition"
              >
                Cancelar
              </button>
              <button
                type="submit"
                disabled={isUploadingPdf || !uploadFile}
                className="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-6 py-2 text-sm font-bold text-white hover:bg-emerald-700 disabled:opacity-50 transition shadow-sm"
              >
                {isUploadingPdf ? (
                  <>
                    <Loader2 className="h-4 w-4 animate-spin" /> Subiendo archivo...
                  </>
                ) : (
                  <>
                    <FileUp className="h-4 w-4" /> Subir y Actualizar PDF
                  </>
                )}
              </button>
            </div>
          </form>
        </Modal>

      </div>
    </DashboardLayout>
  );
}
