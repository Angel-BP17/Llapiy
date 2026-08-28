import React, { useState, useMemo, FormEvent } from 'react';
import DashboardLayout from '@/Layouts/DashboardLayout';
import { Modal } from '@/components/Modal';
import { router } from '@inertiajs/react';
import { 
  Building2, Pencil, Plus, Trash2, Eye, ChevronRight, Check, X 
} from 'lucide-react';
import areasRoutes from '@/routes/areas';
import { usePermissions } from '@/hooks/use-permissions';
import Pagination from '@/components/ui/Pagination';
import { Area, Group, Subgroup, GroupType, PaginationData } from '@/types/models';

interface AreasIndexProps {
  areas: Area[];
  groups: Group[];
  subgroups: Subgroup[];
  groupTypes: GroupType[];
  pagination: PaginationData;
  filters: {
    search?: string;
  };
}

function RecursiveSubgroup({ 
  subgroup, 
  allSubgroups, 
  subgroupForm, 
  setSubgroupForm, 
  onAddSubgroup,
  onEditSubgroup,
  isSubmitting 
}: { 
  subgroup: Subgroup; 
  allSubgroups: Subgroup[]; 
  subgroupForm: Record<number, string>;
  setSubgroupForm: (val: any) => void;
  onAddSubgroup: (parentId: number) => void;
  onEditSubgroup: (sub: Subgroup) => void;
  isSubmitting: boolean;
}) {
  const children = useMemo(() => 
    allSubgroups.filter(s => s.parent_subgroup_id === subgroup.id),
  [allSubgroups, subgroup.id]);

  return (
    <div className="ml-2 sm:ml-4 mt-2 space-y-2 border-l border-dashed border-border pl-3 sm:pl-4">
      <div className="flex items-center justify-between group/sub px-2 py-1.5 rounded-md hover:bg-muted/50 transition-colors">
        <div className="flex items-center gap-2 min-w-0">
          <ChevronRight className="h-3 w-3 text-primary shrink-0" />
          <span className="text-[11px] sm:text-xs text-foreground font-medium leading-tight truncate">{subgroup.descripcion}</span>
        </div>
        <div className="flex items-center gap-1 opacity-0 group-hover/sub:opacity-100 transition-all shrink-0">
          <button 
            type="button" 
            onClick={() => onEditSubgroup(subgroup)}
            className="text-muted-foreground hover:text-amber-600 p-1"
            title="Editar subgrupo"
          >
            <Pencil className="h-3 w-3" />
          </button>
          <button 
            type="button" 
            onClick={() => confirm(`¿Eliminar subgrupo "${subgroup.descripcion}"? Esto eliminará también todos sus subgrupos hijos. Los documentos y usuarios relacionados quedarán sin subgrupo.`) && router.delete(`/subgroups/${subgroup.id}`)} 
            className="text-muted-foreground hover:text-red-600 p-1 cursor-pointer"
            title="Eliminar subgrupo"
          >
            <Trash2 className="h-3 w-3" />
          </button>
        </div>
      </div>

      {children.map(child => (
        <RecursiveSubgroup 
          key={child.id} 
          subgroup={child} 
          allSubgroups={allSubgroups}
          subgroupForm={subgroupForm}
          setSubgroupForm={setSubgroupForm}
          onAddSubgroup={onAddSubgroup}
          onEditSubgroup={onEditSubgroup}
          isSubmitting={isSubmitting}
        />
      ))}

      <div className="mt-2 flex gap-2 pl-3 sm:pl-4">
        <input 
          type="text" 
          value={subgroupForm[subgroup.id] || ""} 
          onChange={e => setSubgroupForm((prev: any) => ({...prev, [subgroup.id]: e.target.value}))} 
          className="h-7 flex-1 rounded-md border border-border bg-background px-2 text-[10px] min-w-0" 
          placeholder="Nuevo..." 
        />
        <button 
          type="button" 
          disabled={isSubmitting || !subgroupForm[subgroup.id]}
          onClick={() => onAddSubgroup(subgroup.id)} 
          className="rounded-md bg-slate-800 px-2.5 text-[10px] font-bold text-white hover:bg-slate-700 disabled:opacity-50 shrink-0"
        >
          +
        </button>
      </div>
    </div>
  );
}

export default function Index({ areas, groups, subgroups, groupTypes, pagination, filters }: AreasIndexProps) {
  const { can } = usePermissions();
  const [createOpen, setCreateOpen] = useState(false);
  const [editOpen, setEditOpen] = useState(false);
  const [detailsOpen, setDetailsOpen] = useState(false);
  const [selectedArea, setSelectedArea] = useState<Area | null>(null);

  // Estados para edición rápida
  const [editingGroup, setEditingGroup] = useState<Group | null>(null);
  const [groupEditName, setGroupEditName] = useState("");
  const [editingSubgroup, setEditingSubgroup] = useState<Subgroup | null>(null);
  const [subgroupEditName, setSubgroupEditName] = useState("");

  const [areaForm, setAreaForm] = useState({ descripcion: "", abreviacion: "" });
  const [groupForm, setGroupForm] = useState({ descripcion: "", abreviacion: "", group_type_id: "" });
  const [subgroupForm, setSubgroupForm] = useState<Record<number, string>>({});
  const [isSubmitting, setIsSubmitting] = useState(false);

  const groupsByArea = useMemo(() => {
    const map = new Map<number, Group[]>();
    groups.forEach(g => {
      const list = map.get(g.area_id!) || [];
      list.push(g);
      map.set(g.area_id!, list);
    });
    return map;
  }, [groups]);

  const groupsByType = useMemo(() => {
    const areaGroups = groupsByArea.get(selectedArea?.id ?? -1) || [];
    const map = new Map<number | string, Group[]>();
    
    areaGroups.forEach((g: Group) => {
      const typeId = g.group_type_id ? Number(g.group_type_id) : 'untyped';
      const list = map.get(typeId) || [];
      list.push(g);
      map.set(typeId, list);
    });
    
    return map;
  }, [groupsByArea, selectedArea]);

  const handleAreaSubmit = (e: FormEvent) => {
    e.preventDefault();
    if (!selectedArea && editOpen) return;
    setIsSubmitting(true);
    const url = editOpen 
        ? areasRoutes.update.url({ area: selectedArea!.id }) 
        : areasRoutes.store.url();
    const method = editOpen ? 'put' : 'post';

    router[method](url, areaForm, {
      onSuccess: () => { setCreateOpen(false); setEditOpen(false); setAreaForm({ descripcion: "", abreviacion: "" }); },
      onFinish: () => setIsSubmitting(false)
    });
  };

  const handleGroupSubmit = (e: FormEvent) => {
    e.preventDefault();
    if (!selectedArea) return;
    setIsSubmitting(true);
    router.post('/groups', { ...groupForm, area_id: selectedArea.id }, {
      onSuccess: () => setGroupForm({ descripcion: "", abreviacion: "", group_type_id: "" }),
      onFinish: () => setIsSubmitting(false)
    });
  };

  const handleQuickGroupEdit = (e: FormEvent) => {
    e.preventDefault();
    if (!editingGroup) return;
    setIsSubmitting(true);
    router.put(`/groups/${editingGroup.id}`, { descripcion: groupEditName }, {
      onSuccess: () => { setEditingGroup(null); setGroupEditName(""); },
      onFinish: () => setIsSubmitting(false)
    });
  };

  const handleQuickSubgroupEdit = (e: FormEvent) => {
    e.preventDefault();
    if (!editingSubgroup) return;
    setIsSubmitting(true);
    router.put(`/subgroups/${editingSubgroup.id}`, { descripcion: subgroupEditName }, {
      onSuccess: () => { setEditingSubgroup(null); setSubgroupEditName(""); },
      onFinish: () => setIsSubmitting(false)
    });
  };

  const handleAddSubgroup = (groupId: number, parentSubgroupId?: number) => {
    const key = parentSubgroupId || groupId;
    if (!subgroupForm[key]) return;
    
    setIsSubmitting(true);
    router.post('/subgroups', { 
      descripcion: subgroupForm[key], 
      group_id: groupId,
      parent_subgroup_id: parentSubgroupId || null
    }, {
      onSuccess: () => setSubgroupForm({ ...subgroupForm, [key]: "" }),
      onFinish: () => setIsSubmitting(false)
    });
  };

  const getTypeStyles = (typeId: number) => {
    const colors: Record<number, { bg: string, text: string, border: string, accent: string, gradient: string, btn: string }> = {
      1: { bg: 'bg-indigo-50', text: 'text-indigo-700', border: 'border-indigo-100', accent: 'bg-indigo-500', gradient: 'from-indigo-50/50', btn: 'bg-indigo-600 hover:bg-indigo-700' },
      2: { bg: 'bg-emerald-50', text: 'text-emerald-700', border: 'border-emerald-100', accent: 'bg-emerald-500', gradient: 'from-emerald-50/50', btn: 'bg-emerald-600 hover:bg-emerald-700' },
      3: { bg: 'bg-amber-50', text: 'text-amber-700', border: 'border-amber-100', accent: 'bg-amber-500', gradient: 'from-amber-50/50', btn: 'bg-amber-600 hover:bg-amber-700' },
      4: { bg: 'bg-rose-50', text: 'text-rose-700', border: 'border-rose-100', accent: 'bg-rose-500', gradient: 'from-rose-50/50', btn: 'bg-rose-600 hover:bg-rose-700' },
      5: { bg: 'bg-sky-50', text: 'text-sky-700', border: 'border-sky-100', accent: 'bg-sky-500', gradient: 'from-sky-50/50', btn: 'bg-sky-600 hover:bg-sky-700' },
      6: { bg: 'bg-violet-50', text: 'text-violet-700', border: 'border-violet-100', accent: 'bg-violet-500', gradient: 'from-violet-50/50', btn: 'bg-violet-600 hover:bg-violet-700' },
    };
    return colors[typeId] || { bg: 'bg-slate-50', text: 'text-slate-700', border: 'border-slate-100', accent: 'bg-slate-500', gradient: 'from-slate-50/50', btn: 'bg-slate-600 hover:bg-slate-700' };
  };

  return (
    <DashboardLayout title="Áreas">
      <div className="space-y-4 sm:space-y-5">
        <header className="rounded-xl sm:rounded-2xl border border-border bg-gradient-to-r from-slate-900 to-indigo-700 p-4 sm:p-5 text-white shadow-sm">
          <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
              <p className="text-[10px] uppercase tracking-[0.24em] text-white/60">Estructura Organizativa</p>
              <h2 className="mt-1 sm:mt-2 text-xl sm:text-2xl font-semibold">Áreas y Oficinas</h2>
              <p className="mt-1 text-xs text-white/75 line-clamp-2 sm:line-clamp-none">Gestiona la jerarquía de áreas, grupos de trabajo y sus respectivos subgrupos.</p>
            </div>
            <div className="flex items-center gap-3 self-start sm:self-center">
              <div className="flex h-10 items-center gap-2 rounded-xl border border-white/20 bg-white/10 px-4 py-2 backdrop-blur-md">
                <Building2 className="h-4 w-4 text-indigo-300" />
                <span className="text-xs font-black uppercase tracking-wider text-white">
                  {areas.length} <span className="text-white/60 ml-1">Áreas</span>
                </span>
              </div>
              {can("areas.create") && (
                <button
                  type="button"
                  onClick={() => {
                    setAreaForm({ descripcion: "", abreviacion: "" });
                    setCreateOpen(true);
                  }}
                  className="inline-flex h-10 items-center gap-1.5 rounded-xl bg-white px-4 text-xs font-bold text-slate-900 shadow-md transition-transform hover:scale-[1.02] active:scale-[0.98] cursor-pointer"
                >
                  Nueva Área
                </button>
              )}
            </div>
          </div>
        </header>

        {areas.length === 0 ? (
          <div className="rounded-xl sm:rounded-2xl border border-dashed border-border bg-card p-8 sm:p-16 text-center shadow-sm">
            <div className="flex flex-col items-center justify-center max-w-md mx-auto">
              <div className="flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-500/10 text-indigo-600 mb-4 animate-bounce">
                <Building2 className="h-8 w-8" />
              </div>
              <h3 className="text-lg sm:text-xl font-bold text-foreground">No hay áreas registradas</h3>
              <p className="mt-2 text-sm text-muted-foreground">
                La estructura organizativa está vacía. Para comenzar a organizar los documentos y las oficinas, registra la primera área del sistema.
              </p>
              {can("areas.create") && (
                <button
                  type="button"
                  onClick={() => {
                    setAreaForm({ descripcion: "", abreviacion: "" });
                    setCreateOpen(true);
                  }}
                  className="mt-6 inline-flex h-10 items-center gap-1.5 rounded-xl bg-indigo-600 px-6 text-xs font-bold text-white shadow-md hover:bg-indigo-700 active:scale-95 transition-all cursor-pointer"
                >
                  <Plus className="h-4 w-4" /> Registrar Nueva Área
                </button>
              )}
            </div>
          </div>
        ) : (
          <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            {areas.map((area) => (
              <article key={area.id} className="rounded-xl border border-border bg-card p-4 sm:p-5 shadow-sm hover:border-primary/30 transition-all group">
                <div className="flex items-start justify-between">
                  <div className="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10 text-primary transition-transform group-hover:scale-110">
                    <Building2 className="h-5 w-5" />
                  </div>
                  <div className="flex flex-col items-end">
                    <span className="text-xs font-black text-muted-foreground uppercase">{area.abreviacion || "S/A"}</span>
                    <span className="text-[10px] font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full mt-1">ID: {area.id}</span>
                  </div>
                </div>
                <h3 className="mt-4 text-base font-bold text-foreground line-clamp-1">{area.descripcion}</h3>
                <p className="mt-1 text-xs text-muted-foreground">{groupsByArea.get(area.id)?.length || 0} grupos asociados</p>
                
                <div className="mt-5 flex flex-wrap gap-2 pt-4 border-t border-border">
                  <button 
                    type="button"
                    onClick={() => { setSelectedArea(area); setDetailsOpen(true); }}
                    className="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 rounded-md bg-sky-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-sky-700 transition-colors"
                  >
                    <Eye className="h-3.5 w-3.5" /> Estructura
                  </button>
                  {can("areas.update") && (
                    <button 
                      type="button"
                      onClick={() => { setSelectedArea(area); setAreaForm({ descripcion: area.descripcion, abreviacion: area.abreviacion || "" }); setEditOpen(true); }}
                      className="inline-flex items-center justify-center rounded-md bg-amber-500 px-3 py-1.5 text-xs font-semibold text-white transition-colors"
                    >
                      <Pencil className="h-3.5 w-3.5" />
                    </button>
                  )}
                  {can("areas.delete") && (
                    <button 
                      type="button"
                      title="Eliminar Área"
                      onClick={() => confirm(`¿Eliminar área "${area.descripcion}"? Esto eliminará también todos los grupos y subgrupos asociados. Los documentos y usuarios relacionados quedarán sin área asociada.`) && router.delete(areasRoutes.destroy.url({ area: area.id }))}
                      className="inline-flex items-center justify-center rounded-md bg-red-600 px-3 py-1.5 text-xs font-semibold text-white transition-colors cursor-pointer"
                    >
                      <Trash2 className="h-3.5 w-3.5" />
                    </button>
                  )}
                </div>
              </article>
            ))}
          </div>
        )}

        <Pagination 
          {...pagination}
          onPageChange={(page) => router.get('/areas', { ...filters, page }, { preserveState: true })}
          label="áreas"
        />

        <Modal open={createOpen || editOpen} title={editOpen ? "Editar Área" : "Registrar Nueva Área"} onClose={() => { setCreateOpen(false); setEditOpen(false); }}>
          <form onSubmit={handleAreaSubmit} className="space-y-4">
            <div className="space-y-1">
              <label htmlFor="area_descripcion" className="text-sm font-bold uppercase text-muted-foreground">Descripción</label>
              <input id="area_descripcion" value={areaForm.descripcion} onChange={e => setAreaForm({...areaForm, descripcion: e.target.value})} className="h-10 w-full rounded-lg border border-border bg-background px-3 text-sm focus:ring-2 focus:ring-primary/20 outline-none transition-all" placeholder="Ej. Gerencia de Infraestructura" required />
            </div>
            <div className="space-y-1">
              <label htmlFor="area_abreviacion" className="text-sm font-bold uppercase text-muted-foreground">Abreviación</label>
              <input id="area_abreviacion" value={areaForm.abreviacion} onChange={e => setAreaForm({...areaForm, abreviacion: e.target.value.toUpperCase()})} className="h-10 w-full rounded-lg border border-border bg-background px-3 text-sm focus:ring-2 focus:ring-primary/20 outline-none transition-all" placeholder="Ej. GINF" />
            </div>
            <div className="flex justify-end pt-2">
              <button id="save-area-button" type="submit" disabled={isSubmitting} className="w-full sm:w-auto rounded-lg bg-primary px-6 py-2.5 text-sm font-bold text-primary-foreground shadow-lg shadow-primary/20 transition-all hover:scale-[1.02]">Guardar Área</button>
            </div>
          </form>
        </Modal>

        <Modal open={detailsOpen} title={selectedArea?.descripcion || "Detalles"} onClose={() => setDetailsOpen(false)} maxWidth="max-w-7xl">
          <div className="space-y-6 sm:space-y-8 p-1 sm:p-2">
            <div className="rounded-xl sm:rounded-2xl border border-border bg-muted/20 p-4 sm:p-6">
              <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 sm:mb-8 pb-4 border-b border-border gap-4">
                <div>
                  <h4 className="text-sm font-black uppercase tracking-widest text-foreground">Mapa Estructural del Área</h4>
                  <p className="text-[10px] sm:text-xs text-muted-foreground mt-1">Jerarquía de grupos y subgrupos organizados por tipo.</p>
                </div>
                <div className="flex gap-2 text-[10px] font-bold uppercase self-start">
                  <span className="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-100 shadow-sm">
                    <div className="w-1.5 h-1.5 rounded-full bg-indigo-500 animate-pulse" /> {groupsByArea.get(selectedArea?.id ?? -1)?.length || 0} Grupos
                  </span>
                </div>
              </div>

              <div className="grid grid-cols-1 gap-8 sm:gap-10">
                <div className="rounded-2xl sm:rounded-3xl border-2 border-dashed border-indigo-200 p-4 sm:p-8 bg-indigo-50/20 hover:bg-indigo-50/30 transition-all duration-500 group/add">
                  <div className="max-w-4xl mx-auto flex flex-col lg:flex-row items-center gap-6 sm:gap-8">
                    <div className="text-center lg:text-left flex-shrink-0">
                      <div className="inline-flex h-10 w-10 sm:h-12 sm:w-12 items-center justify-center rounded-xl sm:rounded-2xl bg-white text-indigo-600 mb-3 shadow-lg shadow-indigo-200/50 group-hover/add:scale-110 transition-transform duration-500">
                        <Plus className="h-6 w-6" />
                      </div>
                      <h5 className="text-sm font-extrabold text-slate-900 uppercase tracking-tight">Expandir Estructura</h5>
                      <p className="text-[10px] text-slate-500 mt-1 max-w-[200px] mx-auto lg:mx-0">Incorpora un nuevo grupo organizativo al área.</p>
                    </div>
                    
                    <form onSubmit={handleGroupSubmit} className="flex-1 w-full flex flex-col sm:flex-row items-end gap-3 sm:gap-4 bg-white p-4 sm:p-5 rounded-2xl shadow-sm border border-indigo-100">
                      <div className="flex-1 space-y-1.5 w-full">
                        <label className="text-[10px] font-black uppercase text-slate-500 pl-1">Nombre del Grupo</label>
                        <input value={groupForm.descripcion} onChange={e => setGroupForm({...groupForm, descripcion: e.target.value})} className="h-10 w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 text-xs focus:ring-2 focus:ring-indigo-500/20 focus:bg-white outline-none transition-all" placeholder="Ej. Oficina de Personal" required />
                      </div>
                      <div className="w-full sm:w-48 space-y-1.5">
                        <label className="text-[10px] font-black uppercase text-slate-500 pl-1">Tipo</label>
                        <select value={groupForm.group_type_id} onChange={e => setGroupForm({...groupForm, group_type_id: e.target.value})} className="h-10 w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 text-xs focus:ring-2 focus:ring-indigo-500/20 focus:bg-white outline-none transition-all" required>
                          <option value="">Tipo...</option>
                          {groupTypes.map(gt => <option key={gt.id} value={String(gt.id)}>{gt.descripcion}</option>)}
                        </select>
                      </div>
                      <button type="submit" disabled={isSubmitting} className="h-10 w-full sm:w-auto px-6 rounded-xl bg-slate-900 text-xs font-bold text-white hover:bg-slate-800 transition-all shadow-lg shadow-slate-900/20 flex items-center justify-center gap-2 flex-shrink-0">
                        <Plus className="h-3.5 w-3.5" /> Crear Grupo
                      </button>
                    </form>
                  </div>
                </div>

                {groupTypes.map((type) => {
                  const groupsInType = groupsByType.get(type.id) || [];
                  if (groupsInType.length === 0) return null;

                  const styles = getTypeStyles(type.id);

                  return (
                    <div key={type.id} className="space-y-4 sm:space-y-6">
                      <div className="flex items-center gap-3 sm:gap-4">
                        <span className={`text-[10px] sm:text-[11px] font-black uppercase tracking-[0.2em] ${styles.text} ${styles.bg} px-3 sm:px-4 py-1.5 rounded-full border ${styles.border} shadow-sm flex items-center gap-2 whitespace-nowrap`}>
                          <div className={`w-1.5 h-1.5 sm:w-2 sm:h-2 rounded-full ${styles.accent}`} />
                          {type.descripcion}
                        </span>
                        <div className={`h-px flex-1 bg-gradient-to-r ${styles.bg} to-transparent opacity-50`} />
                      </div>

                      <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-6">
                        {groupsInType.map((group: Group) => {
                          const rootSubgroups = subgroups.filter(s => s.group_id === group.id && !s.parent_subgroup_id);
                          
                          return (
                            <div key={group.id} className={`group relative flex flex-col rounded-2xl border ${styles.border} bg-card shadow-sm hover:shadow-xl hover:border-transparent transition-all duration-500 overflow-hidden`}>
                              <div className={`absolute top-0 left-0 w-1 h-full ${styles.accent} opacity-0 group-hover:opacity-100 transition-opacity`} />
                              
                              <div className={`p-4 sm:p-5 border-b ${styles.border} bg-gradient-to-br ${styles.gradient} to-transparent`}>
                                <div className="flex items-start justify-between gap-4">
                                  <div className="space-y-1 min-w-0">
                                    <div className="flex items-center gap-2 flex-wrap sm:flex-nowrap">
                                      <h5 className="text-sm font-extrabold text-foreground group-hover:text-indigo-600 transition-colors line-clamp-1">{group.descripcion}</h5>
                                      <span className={`text-[9px] font-black ${styles.text} ${styles.bg} px-1.5 py-0.5 rounded tracking-tighter border ${styles.border} shrink-0`}>
                                        {group.abreviacion}
                                      </span>
                                    </div>
                                    <p className="text-[10px] text-muted-foreground font-medium uppercase tracking-tight opacity-70">ID: {group.id}</p>
                                  </div>
                                  <div className="flex items-center gap-1 shrink-0">
                                    <button 
                                      type="button" 
                                      onClick={() => { setEditingGroup(group); setGroupEditName(group.descripcion); }}
                                      className="p-1.5 rounded-lg text-muted-foreground hover:text-amber-600 hover:bg-amber-50 transition-all"
                                      title="Editar Grupo"
                                    >
                                      <Pencil className="h-4 w-4" />
                                    </button>
                                    <button 
                                      type="button" 
                                      title="Eliminar Grupo"
                                      onClick={() => confirm(`¿Eliminar grupo "${group.descripcion}"? Esto eliminará también todos sus subgrupos. Los documentos y usuarios relacionados quedarán sin grupo.`) && router.delete(`/groups/${group.id}`)} 
                                      className="p-1.5 rounded-lg text-muted-foreground hover:text-red-600 hover:bg-red-50 transition-all cursor-pointer"
                                    >
                                      <Trash2 className="h-4 w-4" />
                                    </button>
                                  </div>
                                </div>
                              </div>
                              
                              <div className="flex-1 p-4 sm:p-5 space-y-4 sm:space-y-5">
                                <div className="space-y-3">
                                  <p className={`text-[10px] font-black uppercase ${styles.text} tracking-widest pl-1 opacity-80`}>Subgrupos</p>
                                  <div className="max-h-[250px] sm:max-h-[300px] overflow-y-auto pr-2 custom-scrollbar space-y-1">
                                    {rootSubgroups.map((sub: Subgroup) => (
                                      <RecursiveSubgroup 
                                        key={sub.id} 
                                        subgroup={sub} 
                                        allSubgroups={subgroups}
                                        subgroupForm={subgroupForm}
                                        setSubgroupForm={setSubgroupForm}
                                        onAddSubgroup={(parentId) => handleAddSubgroup(group.id, parentId)}
                                        onEditSubgroup={(s) => { setEditingSubgroup(s); setSubgroupEditName(s.descripcion); }}
                                        isSubmitting={isSubmitting}
                                      />
                                    ))}
                                    
                                    {rootSubgroups.length === 0 && (
                                      <div className={`flex flex-col items-center justify-center py-6 text-center border border-dashed ${styles.border} rounded-xl bg-muted/5`}>
                                        <p className="text-[11px] text-muted-foreground italic">No hay subgrupos</p>
                                      </div>
                                    )}
                                  </div>
                                </div>

                                <div className={`pt-4 border-t border-dashed ${styles.border}`}>
                                  <div className="relative group/input">
                                    <input 
                                      type="text" 
                                      value={subgroupForm[group.id] || ""} 
                                      onChange={e => setSubgroupForm({...subgroupForm, [group.id]: e.target.value})} 
                                      className="h-9 w-full rounded-xl border border-border bg-background pl-3 pr-20 text-xs focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all" 
                                      placeholder="Nuevo sub..." 
                                    />
                                    <button 
                                      type="button" 
                                      disabled={isSubmitting || !subgroupForm[group.id]}
                                      onClick={() => handleAddSubgroup(group.id)} 
                                      className={`absolute right-1 top-1 bottom-1 px-3 rounded-lg ${styles.btn} text-[10px] font-bold text-white disabled:opacity-50 transition-all shadow-sm`}
                                    >
                                      Añadir
                                    </button>
                                  </div>
                                </div>
                              </div>
                            </div>
                          );
                        })}
                      </div>
                    </div>
                  );
                })}
              </div>
            </div>
          </div>
        </Modal>

        {/* Modal de Edición Rápida de Grupo */}
        <Modal open={!!editingGroup} title="Editar Nombre del Grupo" onClose={() => setEditingGroup(null)} maxWidth="max-w-md">
          <form onSubmit={handleQuickGroupEdit} className="space-y-4 py-2">
            <div className="space-y-1.5">
              <label className="text-[10px] font-black uppercase text-muted-foreground pl-1">Descripción del Grupo</label>
              <input 
                autoFocus
                value={groupEditName} 
                onChange={e => setGroupEditName(e.target.value.toUpperCase())} 
                className="h-11 w-full rounded-xl border border-border bg-background px-4 text-sm focus:ring-2 focus:ring-indigo-500/20 outline-none"
                required 
              />
            </div>
            <div className="flex justify-end gap-3 pt-2">
              <button type="button" onClick={() => setEditingGroup(null)} className="h-10 px-4 rounded-lg text-xs font-bold text-muted-foreground hover:bg-muted transition-colors">Cancelar</button>
              <button type="submit" disabled={isSubmitting} className="h-10 px-6 rounded-lg bg-indigo-600 text-xs font-bold text-white hover:bg-indigo-700 shadow-lg shadow-indigo-600/20 flex items-center gap-2">
                <Check className="h-4 w-4" /> Actualizar
              </button>
            </div>
          </form>
        </Modal>

        {/* Modal de Edición Rápida de Subgrupo */}
        <Modal open={!!editingSubgroup} title="Editar Subgrupo" onClose={() => setEditingSubgroup(null)} maxWidth="max-w-md">
          <form onSubmit={handleQuickSubgroupEdit} className="space-y-4 py-2">
            <div className="space-y-1.5">
              <label className="text-[10px] font-black uppercase text-muted-foreground pl-1">Nombre del Subgrupo</label>
              <input 
                autoFocus
                value={subgroupEditName} 
                onChange={e => setSubgroupEditName(e.target.value.toUpperCase())} 
                className="h-11 w-full rounded-xl border border-border bg-background px-4 text-sm focus:ring-2 focus:ring-indigo-500/20 outline-none"
                required 
              />
            </div>
            <div className="flex justify-end gap-3 pt-2">
              <button type="button" onClick={() => setEditingSubgroup(null)} className="h-10 px-4 rounded-lg text-xs font-bold text-muted-foreground hover:bg-muted transition-colors">Cancelar</button>
              <button type="submit" disabled={isSubmitting} className="h-10 px-6 rounded-lg bg-emerald-600 text-xs font-bold text-white hover:bg-emerald-700 shadow-lg shadow-emerald-600/20 flex items-center gap-2">
                <Check className="h-4 w-4" /> Guardar Cambios
              </button>
            </div>
          </form>
        </Modal>
      </div>
    </DashboardLayout>
  );
}
