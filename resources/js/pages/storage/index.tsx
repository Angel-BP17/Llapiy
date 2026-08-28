import React, { useState, useMemo, FormEvent } from 'react';
import DashboardLayout from '@/Layouts/DashboardLayout';
import { Modal } from '@/components/Modal';
import { router, usePage, Link } from '@inertiajs/react';
import { 
  Boxes, Eye, FileArchive, Layers, LayoutGrid, Pencil, Trash2, ChevronLeft, ChevronRight, Folder, Plus, AlertCircle 
} from 'lucide-react';
import sectionsRoutes from '@/routes/sections';
import andamiosRoutes from '@/routes/andamios';
import boxesRoutes from '@/routes/boxes';
import archivosRoutes from '@/routes/archivos';

interface StorageIndexProps {
  level: 'sections' | 'andamios' | 'boxes' | 'archivos';
  sections: any[];
  andamios: any[];
  boxes: any[];
  archivos: any[];
  activeSection: any;
  activeAndamio: any;
  activeBox: any;
  filters: any;
  counts: any; 
  searchedBlocks?: any[];
}

export default function Index({ 
  level, sections, andamios, boxes, archivos, 
  activeSection, activeAndamio, activeBox, 
  filters, counts, searchedBlocks = []
}: StorageIndexProps) {
  const { auth } = usePage().props as any;
  const [search, setSearch] = useState(filters?.search || "");
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [error, setError] = useState("");

  const [sectionForm, setSectionForm] = useState({ n_section: "", descripcion: "" });
  const [andamioForm, setAndamioForm] = useState({ n_andamio: "", descripcion: "" });
  const [boxForm, setBoxForm] = useState({ n_box: "", descripcion: "" });

  const [fileModalOpen, setFileModalOpen] = useState(false);
  const [selectedArchivo, setSelectedArchivo] = useState<any>(null);

  const can = (p: string) => auth.permissions?.includes(p) || auth.roles?.some((r: any) => (typeof r === 'string' ? r : r?.name || '').toUpperCase() === 'ADMINISTRADOR');

  const handleSearch = () => {
    router.get(window.location.pathname, { search }, { preserveState: true });
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
      onSuccess: () => {
        setSectionForm({ n_section: "", descripcion: "" });
        setAndamioForm({ n_andamio: "", descripcion: "" });
        setBoxForm({ n_box: "", descripcion: "" });
      },
      onFinish: () => setIsSubmitting(false)
    });
  };

  const handleDelete = (id: number) => {
    if (!confirm('¿Seguro de eliminar este elemento?')) return;
    let url = "";
    if (level === 'sections') url = sectionsRoutes.destroy.url({ section: id });
    else if (level === 'andamios') url = andamiosRoutes.destroy.url({ section: activeSection.id, andamio: id });
    else if (level === 'boxes') url = boxesRoutes.delete.url({ section: activeSection.id, andamio: activeAndamio.id, box: id });
    
    router.delete(url);
  };

  const removeArchivo = (archivoId: number) => {
    if (confirm('¿Seguro de retirar este archivo del almacén?')) {
      router.post(archivosRoutes.move.url({ 
        section: activeSection.id, 
        andamio: activeAndamio.id, 
        box: activeBox.id, 
        block: archivoId 
      }));
    }
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
          <div className="grid gap-3 md:grid-cols-2">
            <input 
              id="storage-search"
              value={search} 
              onChange={(e) => setSearch(e.target.value)} 
              className="h-10 rounded-lg border border-border bg-background px-3 text-sm" 
              placeholder={`Buscar en ${level}... (o por código de bloque)`} 
            />
            <div className="flex gap-2">
              <button onClick={handleSearch} className="h-10 flex-1 rounded-lg bg-primary text-sm font-semibold text-primary-foreground">Filtrar</button>
              <button onClick={() => { setSearch(""); router.get(window.location.pathname); }} className="h-10 rounded-lg border border-border bg-card px-4 text-sm font-semibold text-foreground">Limpiar</button>
            </div>
          </div>
        </div>

        {/* BLOQUES ENCONTRADOS */}
        {searchedBlocks.length > 0 && (
          <div className="rounded-xl border border-border bg-indigo-50/10 dark:bg-indigo-950/10 p-4 shadow-sm space-y-3">
            <h3 className="text-sm font-bold text-indigo-600 dark:text-indigo-400 flex items-center gap-1.5">
              <FileArchive className="h-4 w-4" /> Bloques encontrados en el almacén ({searchedBlocks.length})
            </h3>
            <div className="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
              {searchedBlocks.map((b: any) => (
                <div key={b.id} className="rounded-lg border border-indigo-200/50 dark:border-indigo-900/50 bg-background p-3 shadow-sm space-y-2">
                  <div>
                    <h4 className="text-[10px] font-black uppercase tracking-wider text-muted-foreground">Código de Bloque / Asunto</h4>
                    <p className="text-sm font-semibold text-foreground mt-0.5">{b.n_bloque} - {b.asunto}</p>
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
              ))}
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
                      <button className="inline-flex items-center gap-1.5 rounded-md bg-amber-500 px-3 py-1.5 text-xs font-semibold text-white hover:bg-amber-600 transition">
                        <Pencil className="h-3.5 w-3.5" /> Editar
                      </button>
                      <button 
                        onClick={() => handleDelete(item.id)}
                        disabled={childCount > 0}
                        title={childCount > 0 ? "No se puede eliminar porque tiene elementos asociados" : ""}
                        className="inline-flex items-center gap-1.5 rounded-md bg-red-600 px-3 py-1.5 text-xs font-semibold text-white disabled:opacity-50 disabled:cursor-not-allowed hover:bg-red-700 transition"
                      >
                        <Trash2 className="h-3.5 w-3.5" /> Eliminar
                      </button>
                    </div>
                  </article>
                );
              })}
            </div>
          )
        ) : (
          <div className="overflow-hidden rounded-xl border border-border bg-card shadow-sm">
            <div className="overflow-x-auto">
              <table className="w-full text-sm">
                <thead className="bg-muted/60">
                  <tr className="text-left text-xs uppercase tracking-wide text-muted-foreground">
                    <th className="px-4 py-3">ID</th>
                    <th className="px-4 py-3">Asunto / Contenido</th>
                    <th className="px-4 py-3">Folios</th>
                    <th className="px-4 py-3 text-right">Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  {archivos.map((a: any) => (
                    <tr key={a.id} className="border-t border-border hover:bg-muted/30">
                      <td className="px-4 py-3 font-mono text-xs text-muted-foreground">{a.id}</td>
                      <td className="px-4 py-3 font-medium text-foreground">{a.asunto}</td>
                      <td className="px-4 py-3">{a.folios}</td>
                      <td className="px-4 py-3 text-right">
                        <div className="flex justify-end gap-2">
                          {a.root && (
                            <button 
                              onClick={() => {
                                setSelectedArchivo(a);
                                setFileModalOpen(true);
                              }}
                              className="inline-flex items-center gap-1.5 rounded-md bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-indigo-700"
                              title="Ver documento original"
                            >
                              <Eye className="h-3.5 w-3.5" /> Ver
                            </button>
                          )}
                          <button 
                            onClick={() => removeArchivo(a.id)}
                            className="inline-flex items-center gap-1.5 rounded-md bg-rose-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-rose-700"
                          >
                            <Trash2 className="h-3.5 w-3.5" /> Retirar
                          </button>
                        </div>
                      </td>
                    </tr>
                  ))}
                  {archivos.length === 0 && (
                    <tr>
                      <td colSpan={4} className="px-4 py-16 text-center">
                        <div className="flex flex-col items-center justify-center max-w-md mx-auto">
                          <div className="flex h-16 w-16 items-center justify-center rounded-2xl bg-emerald-500/10 text-emerald-600 mb-4 animate-bounce">
                            <FileArchive className="h-8 w-8" />
                          </div>
                          <h3 className="text-lg font-semibold text-foreground">No hay archivos en la caja</h3>
                          <p className="mt-1 text-sm text-muted-foreground">
                            Esta caja de almacenamiento no tiene archivos físicos registrados en este momento.
                          </p>
                        </div>
                      </td>
                    </tr>
                  )}
                </tbody>
              </table>
            </div>
          </div>
        )}

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

      </div>
    </DashboardLayout>
  );
}
