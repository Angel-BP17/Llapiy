import React, { useState, useMemo, FormEvent, useEffect } from "react";
import DashboardLayout from "@/Layouts/DashboardLayout";
import { Modal } from "@/components/Modal";
import { router } from "@inertiajs/react";
import {
    Archive,
    Building2,
    CheckCircle2,
    Clock3,
    Eye,
    FileDown,
    FileText,
    Pencil,
    Plus,
    Trash2,
    AlertCircle,
    AlertTriangle,
    Filter,
    X,
    Search,
    SlidersHorizontal,
    Loader2,
    Calendar,
} from "lucide-react";
import { MONTHS } from "@/constants";
import { usePermissions } from "@/hooks/use-permissions";
import Pagination from "@/components/ui/Pagination";

interface BlocksIndexProps {
    blocks: any[];
    areas: any[];
    groups: any[];
    subgroups: any[];
    stats: {
        totalBlocks: number;
        attendedCount: number;
        unattendedCount: number;
    };
    pagination: any;
    filters: any;
    years: number[];
    sections: any[];
    andamios: any[];
    boxes: any[];
    documentarySeries: any[];
}

export type BlockPeriodItem = {
    id?: number;
    rango_inicial: string;
    rango_final: string;
    periodo: string;
};

type BlockForm = {
    n_bloque: string;
    asunto: string;
    folios: string;
    rango_inicial: string;
    rango_final: string;
    fecha: string;
    documentary_series_id: string;
    periods: BlockPeriodItem[];
};

const emptyForm: BlockForm = {
    n_bloque: "",
    asunto: "",
    folios: "",
    rango_inicial: "",
    rango_final: "",
    fecha: "",
    documentary_series_id: "",
    periods: [{ rango_inicial: "", rango_final: "", periodo: "" }],
};

export default function Index({
    blocks,
    areas,
    groups,
    subgroups,
    stats,
    pagination,
    filters,
    years,
    sections = [],
    andamios = [],
    boxes = [],
    documentarySeries = [],
}: BlocksIndexProps) {
    const { can, is } = usePermissions();
    const isAllowedAdvanced = is("ADMINISTRADOR") || is("ARCHIVO_CENTRAL");
    const [f, setF] = useState({
        asunto: filters.asunto || "",
        n_bloque: filters.n_bloque || "",
        area_id: filters.area_id || "",
        group_id: filters.group_id || "",
        subgroup_id: filters.subgroup_id || "",
        year: filters.year || "",
        month: filters.month || "",
        section_id: filters.section_id || "",
        andamio_id: filters.andamio_id || "",
        box_id: filters.box_id || "",
    });

    const [isFiltering, setIsFiltering] = useState(false);

    const filteredGroups = useMemo(() => {
        if (!f.area_id) return [];
        return groups.filter((g) => String(g.area_id) === String(f.area_id));
    }, [f.area_id, groups]);

    const filteredSubgroups = useMemo(() => {
        if (!f.group_id) return [];
        return subgroups.filter(
            (s) => String(s.group_id) === String(f.group_id),
        );
    }, [f.group_id, subgroups]);

    const filteredAndamios = useMemo(() => {
        if (!f.section_id) return [];
        return andamios.filter(
            (a) => String(a.section_id) === String(f.section_id),
        );
    }, [f.section_id, andamios]);

    const filteredBoxes = useMemo(() => {
        if (!f.andamio_id) return [];
        return boxes.filter(
            (b) => String(b.andamio_id) === String(f.andamio_id),
        );
    }, [f.andamio_id, boxes]);

    const handleFilterChange = (field: string, value: string) => {
        setF((prev) => {
            const next = { ...prev, [field]: value };
            if (field === "area_id") {
                next.group_id = "";
                next.subgroup_id = "";
            }
            if (field === "group_id") {
                next.subgroup_id = "";
            }
            if (field === "section_id") {
                next.andamio_id = "";
                next.box_id = "";
            }
            if (field === "andamio_id") {
                next.box_id = "";
            }
            return next;
        });
    };

    const [showAdvanced, setShowAdvanced] = useState(
        !!(f.area_id || f.group_id || f.subgroup_id || f.year || f.month || f.section_id || f.andamio_id || f.box_id)
    );

    const activeFilters = useMemo(() => {
        const badges: { field: string; label: string; value: string }[] = [];
        if (f.n_bloque) {
            badges.push({ field: "n_bloque", label: "Código", value: f.n_bloque });
        }
        if (f.asunto) {
            badges.push({ field: "asunto", label: "Asunto", value: f.asunto });
        }
        if (f.area_id) {
            const area = areas.find((a) => String(a.id) === String(f.area_id));
            if (area) badges.push({ field: "area_id", label: "Área", value: area.descripcion });
        }
        if (f.group_id) {
            const group = groups.find((g) => String(g.id) === String(f.group_id));
            if (group) badges.push({ field: "group_id", label: "Grupo", value: group.descripcion });
        }
        if (f.subgroup_id) {
            const subgroup = subgroups.find((s) => String(s.id) === String(f.subgroup_id));
            if (subgroup) badges.push({ field: "subgroup_id", label: "Subgrupo", value: subgroup.descripcion });
        }
        if (f.year) {
            badges.push({ field: "year", label: "Periodo", value: f.year });
        }
        if (f.month) {
            badges.push({ field: "month", label: "Mes", value: MONTHS[parseInt(f.month) - 1] });
        }
        if (f.section_id) {
            const sec = sections.find((s) => String(s.id) === String(f.section_id));
            if (sec) badges.push({ field: "section_id", label: "Sección", value: sec.n_section });
        }
        if (f.andamio_id) {
            const and = andamios.find((a) => String(a.id) === String(f.andamio_id));
            if (and) badges.push({ field: "andamio_id", label: "Andamio", value: and.n_andamio });
        }
        if (f.box_id) {
            const bx = boxes.find((b) => String(b.id) === String(f.box_id));
            if (bx) badges.push({ field: "box_id", label: "Caja", value: bx.n_box });
        }
        return badges;
    }, [f, areas, groups, subgroups, sections, andamios, boxes]);

    const handleRemoveFilter = (field: string) => {
        setF((prev) => {
            const updated = { ...prev, [field]: "" };
            if (field === "area_id") {
                updated.group_id = "";
                updated.subgroup_id = "";
            }
            if (field === "group_id") {
                updated.subgroup_id = "";
            }
            if (field === "section_id") {
                updated.andamio_id = "";
                updated.box_id = "";
            }
            if (field === "andamio_id") {
                updated.box_id = "";
            }
            setIsFiltering(true);
            router.get("/bloques", updated, {
                preserveState: true,
                preserveScroll: true,
                only: ["blocks", "stats", "pagination", "filters"],
                onFinish: () => setIsFiltering(false),
            });
            return updated;
        });
    };

    const handleClearAllFilters = () => {
        const cleared = {
            asunto: "",
            n_bloque: "",
            area_id: "",
            group_id: "",
            subgroup_id: "",
            year: "",
            month: "",
            section_id: "",
            andamio_id: "",
            box_id: "",
        };
        setF(cleared);
        setIsFiltering(true);
        router.get("/bloques", cleared, {
            preserveState: true,
            preserveScroll: true,
            only: ["blocks", "stats", "pagination", "filters"],
            onFinish: () => setIsFiltering(false),
        });
    };

    const [createOpen, setCreateOpen] = useState(false);
    const [editOpen, setEditOpen] = useState(false);
    const [showOpen, setShowOpen] = useState(false);
    const [selectedBlock, setSelectedBlock] = useState<any>(null);

    const [deleteModalOpen, setDeleteModalOpen] = useState(false);
    const [blockToDelete, setBlockToDelete] = useState<any>(null);
    const [isDeleting, setIsDeleting] = useState(false);

    const [pdfModalOpen, setPdfModalOpen] = useState(false);
    const [pdfUrl, setPdfUrl] = useState("");
    const [pdfTitle, setPdfTitle] = useState("");

    const [form, setForm] = useState<BlockForm>(emptyForm);
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [error, setError] = useState("");

    const canCreate = can("blocks.create");
    const canUpload = can("blocks.upload");

    const extractISODate = (str: string) => {
        if (!str) return "";
        const match = str.match(/(\d{4})-(\d{2})-(\d{2})/);
        return match ? `${match[1]}-${match[2]}-${match[3]}` : "";
    };

    const formatDateLabel = (dateText: string) => {
        const iso = extractISODate(dateText);
        if (!iso) return "-";
        try {
            const [year, month, day] = iso.split("-");
            return `${day} de ${MONTHS[parseInt(month) - 1].toLowerCase()} de ${year}`;
        } catch {
            return dateText;
        }
    };

    const handleFilter = () => {
        setIsFiltering(true);
        router.get("/bloques", f, {
            preserveState: true,
            preserveScroll: true,
            only: ["blocks", "stats", "pagination", "filters"],
            onFinish: () => setIsFiltering(false),
        });
    };

    const handleSubmit = (e: FormEvent) => {
        e.preventDefault();
        setIsSubmitting(true);
        setError("");

        const data: any = { ...form };

        if (form.periods && form.periods.length > 0) {
            for (let i = 0; i < form.periods.length; i++) {
                const p = form.periods[i];
                if (!p.rango_inicial || !p.rango_final || !p.periodo) {
                    setError(`Completa el rango y año del periodo #${i + 1}`);
                    setIsSubmitting(false);
                    return;
                }
                if (parseInt(p.rango_final) < parseInt(p.rango_inicial)) {
                    setError(`En el periodo #${i + 1}, el folio final (${p.rango_final}) no puede ser menor al inicial (${p.rango_inicial})`);
                    setIsSubmitting(false);
                    return;
                }
            }
            data.periods = form.periods.map((p) => ({
                rango_inicial: parseInt(p.rango_inicial),
                rango_final: parseInt(p.rango_final),
                periodo: parseInt(p.periodo),
            }));
        }

        if (editOpen) {
            router.post(
                `/bloques/${selectedBlock.id}`,
                { ...data, _method: "PUT" },
                {
                    forceFormData: true,
                    preserveScroll: true,
                    onSuccess: () => {
                        setEditOpen(false);
                        setForm(emptyForm);
                    },
                    onError: (err: any) =>
                        setError(Object.values(err)[0] as string),
                    onFinish: () => setIsSubmitting(false),
                },
            );
        } else {
            router.post("/bloques", data, {
                preserveScroll: true,
                onSuccess: () => {
                    setCreateOpen(false);
                    setForm(emptyForm);
                },
                onError: (err: any) =>
                    setError(Object.values(err)[0] as string),
                onFinish: () => setIsSubmitting(false),
            });
        }
    };

    const handleDeleteBlock = () => {
        if (!blockToDelete) return;
        setIsDeleting(true);
        router.delete(`/bloques/${blockToDelete.id}`, {
            preserveScroll: true,
            onSuccess: () => {
                setDeleteModalOpen(false);
                setBlockToDelete(null);
            },
            onFinish: () => setIsDeleting(false),
        });
    };

    const handleViewFile = (block: any) => {
        setPdfUrl(`/bloques/${block.id}/file`);
        setPdfTitle(`Documento Digital - Bloque Nº ${block.n_bloque}`);
        setPdfModalOpen(true);
    };

    const handleAddPeriod = () => {
        setForm((prev) => {
            const last = prev.periods[prev.periods.length - 1];
            const nextStart = last && !isNaN(parseInt(last.rango_final))
                ? String(parseInt(last.rango_final) + 1)
                : "";
            const nextYear = last && !isNaN(parseInt(last.periodo))
                ? String(parseInt(last.periodo) + 1)
                : (prev.fecha ? prev.fecha.split("-")[0] : "");

            return {
                ...prev,
                periods: [
                    ...prev.periods,
                    {
                        rango_inicial: nextStart,
                        rango_final: prev.rango_final || "",
                        periodo: nextYear,
                    },
                ],
            };
        });
    };

    const handleRemovePeriod = (index: number) => {
        setForm((prev) => ({
            ...prev,
            periods: prev.periods.filter((_, i) => i !== index),
        }));
    };

    const handlePeriodChange = (
        index: number,
        field: "rango_inicial" | "rango_final" | "periodo",
        value: string,
    ) => {
        setForm((prev) => {
            const nextPeriods = [...prev.periods];
            nextPeriods[index] = { ...nextPeriods[index], [field]: value };
            return { ...prev, periods: nextPeriods };
        });
    };

    const openEdit = (block: any) => {
        const ds = block.documentary_series || block.documentarySeries;
        setSelectedBlock(block);

        const existingPeriods = (block.periods && block.periods.length > 0)
            ? block.periods.map((p: any) => ({
                id: p.id,
                rango_inicial: String(p.rango_inicial || ""),
                rango_final: String(p.rango_final || ""),
                periodo: String(p.periodo || ""),
            }))
            : [{
                rango_inicial: String(block.rango_inicial || ""),
                rango_final: String(block.rango_final || ""),
                periodo: String(block.periodo || (block.fecha ? extractISODate(block.fecha).split("-")[0] : "")),
            }];

        setForm({
            n_bloque: block.n_bloque,
            asunto: block.asunto,
            folios: block.folios || "",
            rango_inicial: String(block.rango_inicial || ""),
            rango_final: String(block.rango_final || ""),
            fecha: extractISODate(block.fecha),
            documentary_series_id: ds ? String(ds.id) : (block.documentary_series_id ? String(block.documentary_series_id) : ""),
            periods: existingPeriods,
        });
        setEditOpen(true);
    };

    return (
        <DashboardLayout title="Bloques Físicos">
            <div className="space-y-5">
                <header className="rounded-2xl border border-border bg-gradient-to-r from-slate-900 to-indigo-700 p-5 text-white shadow-sm">
                    <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p className="text-xs uppercase tracking-[0.24em] text-white/60">
                                Archivo Central / Fisico
                            </p>
                            <h2 className="mt-2 text-2xl font-semibold">
                                Gestión de Bloques
                            </h2>
                            <p className="mt-1 text-sm text-white/75">
                                Registra y organiza los bloques de documentos
                                físicos antes de su almacenamiento.
                            </p>
                        </div>
                        <div className="flex h-10 items-center gap-2 rounded-xl border border-white/20 bg-white/10 px-4 py-2 backdrop-blur-md self-start sm:self-center">
                            <Archive className="h-4 w-4 text-indigo-300" />
                            <span className="text-xs font-black uppercase tracking-wider text-white">
                                {pagination.total} <span className="text-white/60 ml-1">Registros</span>
                            </span>
                        </div>
                    </div>
                </header>

                <div className="grid gap-3 sm:grid-cols-3">
                    <article className="rounded-xl border border-border bg-card p-4 shadow-sm flex items-center gap-3">
                        <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-primary/10 text-primary">
                            <Archive className="h-5 w-5" />
                        </div>
                        <div>
                            <p className="text-xs text-muted-foreground">
                                Total de bloques
                            </p>
                            <p className="mt-1 text-2xl font-semibold text-foreground">
                                {stats.totalBlocks}
                            </p>
                        </div>
                    </article>
                    <article className="rounded-xl border border-border bg-card p-4 shadow-sm flex items-center gap-3">
                        <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">
                            <CheckCircle2 className="h-5 w-5" />
                        </div>
                        <div>
                            <p className="text-xs text-muted-foreground">
                                Bloques atendidos
                            </p>
                            <p className="mt-1 text-2xl font-semibold text-foreground">
                                {stats.attendedCount}
                            </p>
                        </div>
                    </article>
                    <article className="rounded-xl border border-border bg-card p-4 shadow-sm flex items-center gap-3">
                        <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-100 text-amber-600">
                            <Clock3 className="h-5 w-5" />
                        </div>
                        <div>
                            <p className="text-xs text-muted-foreground">
                                Pendientes
                            </p>
                            <p className="mt-1 text-2xl font-semibold text-foreground">
                                {stats.unattendedCount}
                            </p>
                        </div>
                    </article>
                </div>

                <div className="rounded-xl border border-border bg-card p-5 shadow-sm space-y-4">
                    {/* Fila principal de búsqueda */}
                    <div className="flex flex-col sm:flex-row gap-3">
                        <div className="relative flex-1">
                            <Search className="absolute left-3 top-2.5 h-4 w-4 text-muted-foreground" />
                            <input
                                value={f.n_bloque}
                                onChange={(e) => setF({ ...f, n_bloque: e.target.value })}
                                onKeyDown={(e) => e.key === "Enter" && handleFilter()}
                                placeholder="Buscar por código de bloque..."
                                className="h-10 w-full pl-9 rounded-lg border border-border bg-background px-3 text-sm focus:ring-2 focus:ring-primary/20 outline-none"
                            />
                        </div>
                        <div className="relative flex-[1.5]">
                            <Search className="absolute left-3 top-2.5 h-4 w-4 text-muted-foreground" />
                            <input
                                value={f.asunto}
                                onChange={(e) => setF({ ...f, asunto: e.target.value })}
                                onKeyDown={(e) => e.key === "Enter" && handleFilter()}
                                placeholder="Buscar por asunto del bloque..."
                                className="h-10 w-full pl-9 rounded-lg border border-border bg-background px-3 text-sm focus:ring-2 focus:ring-primary/20 outline-none"
                            />
                        </div>
                        <button
                            onClick={() => setShowAdvanced(!showAdvanced)}
                            type="button"
                            className={`h-10 px-4 rounded-lg border text-sm font-semibold flex items-center gap-2 transition ${
                                showAdvanced 
                                    ? "bg-primary/10 border-primary text-primary" 
                                    : "border-border bg-background text-foreground hover:bg-muted"
                            }`}
                        >
                            <SlidersHorizontal className="h-4 w-4" />
                            <span>Filtros</span>
                        </button>
                    </div>

                    {/* Panel de filtros avanzados (colapsable) */}
                    {showAdvanced && (
                        <div className="grid gap-4 border-t border-border pt-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 animate-in fade-in slide-in-from-top-1 duration-200">
                            {/* Filtro por Área */}
                            {isAllowedAdvanced && (
                                <div className="space-y-1.5">
                                    <label className="text-[10px] font-bold uppercase text-muted-foreground pl-1">Área</label>
                                    <select
                                        value={f.area_id}
                                        onChange={(e) => handleFilterChange("area_id", e.target.value)}
                                        className="h-10 w-full rounded-lg border border-border bg-background px-3 text-sm outline-none focus:ring-2 focus:ring-primary/20"
                                    >
                                        <option value="">Todas las áreas</option>
                                        {areas.map((a) => (
                                            <option key={a.id} value={String(a.id)}>{a.descripcion}</option>
                                        ))}
                                    </select>
                                </div>
                            )}

                            {/* Filtro por Oficina/Grupo */}
                            {isAllowedAdvanced && (
                                <div className="space-y-1.5">
                                    <label className="text-[10px] font-bold uppercase text-muted-foreground pl-1">Grupo / Oficina</label>
                                    <select
                                        value={f.group_id}
                                        onChange={(e) => handleFilterChange("group_id", e.target.value)}
                                        disabled={!f.area_id}
                                        className="h-10 w-full rounded-lg border border-border bg-background px-3 text-sm disabled:opacity-55 outline-none focus:ring-2 focus:ring-primary/20"
                                    >
                                        <option value="">Todos los grupos</option>
                                        {filteredGroups.map((x: any) => (
                                            <option key={x.id} value={String(x.id)}>{x.descripcion}</option>
                                        ))}
                                    </select>
                                </div>
                            )}

                            {/* Filtro por Subgrupo */}
                            {isAllowedAdvanced && (
                                <div className="space-y-1.5">
                                    <label className="text-[10px] font-bold uppercase text-muted-foreground pl-1">Subgrupo</label>
                                    <select
                                        value={f.subgroup_id}
                                        onChange={(e) => setF((p) => ({ ...p, subgroup_id: e.target.value }))}
                                        disabled={!f.group_id}
                                        className="h-10 w-full rounded-lg border border-border bg-background px-3 text-sm disabled:opacity-55 outline-none focus:ring-2 focus:ring-primary/20"
                                    >
                                        <option value="">Todos los subgrupos</option>
                                        {filteredSubgroups.map((x: any) => (
                                            <option key={x.id} value={String(x.id)}>{x.descripcion}</option>
                                        ))}
                                    </select>
                                </div>
                            )}

                            {/* Filtro por Periodo */}
                            <div className="space-y-1.5">
                                <label className="text-[10px] font-bold uppercase text-muted-foreground pl-1">Periodo (Año)</label>
                                <select
                                    value={f.year}
                                    onChange={(e) => setF({ ...f, year: e.target.value })}
                                    className="h-10 w-full rounded-lg border border-border bg-background px-3 text-sm outline-none focus:ring-2 focus:ring-primary/20"
                                >
                                    <option value="">Todos los años</option>
                                    {years.map((y) => (
                                        <option key={y} value={String(y)}>{y}</option>
                                    ))}
                                </select>
                            </div>

                            {/* Filtro por Mes */}
                            <div className="space-y-1.5">
                                <label className="text-[10px] font-bold uppercase text-muted-foreground pl-1">Mes</label>
                                <select
                                    value={f.month}
                                    onChange={(e) => setF({ ...f, month: e.target.value })}
                                    className="h-10 w-full rounded-lg border border-border bg-background px-3 text-sm outline-none focus:ring-2 focus:ring-primary/20"
                                >
                                    <option value="">Todos los meses</option>
                                    {MONTHS.map((m, i) => (
                                        <option key={m} value={String(i + 1)}>{m}</option>
                                    ))}
                                </select>
                            </div>

                            {/* Filtro por Sección */}
                            {isAllowedAdvanced && (
                                <div className="space-y-1.5">
                                    <label className="text-[10px] font-bold uppercase text-muted-foreground pl-1">Sección Almacén</label>
                                    <select
                                        value={f.section_id}
                                        onChange={(e) => handleFilterChange("section_id", e.target.value)}
                                        className="h-10 w-full rounded-lg border border-border bg-background px-3 text-sm outline-none focus:ring-2 focus:ring-primary/20"
                                    >
                                        <option value="">Todas las secciones</option>
                                        {sections.map((s: any) => (
                                            <option key={s.id} value={String(s.id)}>{s.n_section} - {s.descripcion}</option>
                                        ))}
                                    </select>
                                </div>
                            )}

                            {/* Filtro por Andamio */}
                            {isAllowedAdvanced && (
                                <div className="space-y-1.5">
                                    <label className="text-[10px] font-bold uppercase text-muted-foreground pl-1">Estantería / Andamio</label>
                                    <select
                                        value={f.andamio_id}
                                        onChange={(e) => handleFilterChange("andamio_id", e.target.value)}
                                        disabled={!f.section_id}
                                        className="h-10 w-full rounded-lg border border-border bg-background px-3 text-sm disabled:opacity-55 outline-none focus:ring-2 focus:ring-primary/20"
                                    >
                                        <option value="">Todos los andamios</option>
                                        {filteredAndamios.map((a: any) => (
                                            <option key={a.id} value={String(a.id)}>{a.n_andamio} - {a.descripcion}</option>
                                        ))}
                                    </select>
                                </div>
                            )}

                            {/* Filtro por Caja */}
                            {isAllowedAdvanced && (
                                <div className="space-y-1.5">
                                    <label className="text-[10px] font-bold uppercase text-muted-foreground pl-1">Caja</label>
                                    <select
                                        value={f.box_id}
                                        onChange={(e) => setF((p) => ({ ...p, box_id: e.target.value }))}
                                        disabled={!f.andamio_id}
                                        className="h-10 w-full rounded-lg border border-border bg-background px-3 text-sm disabled:opacity-55 outline-none focus:ring-2 focus:ring-primary/20"
                                    >
                                        <option value="">Todas las cajas</option>
                                        {filteredBoxes.map((b: any) => (
                                            <option key={b.id} value={String(b.id)}>{b.n_box} - {b.descripcion}</option>
                                        ))}
                                    </select>
                                </div>
                            )}
                        </div>
                    )}

                    {/* Botones de acción del panel */}
                    <div className="flex flex-wrap items-center justify-between gap-3 border-t border-border pt-4">
                        <div className="flex gap-2">
                            <button
                                onClick={handleFilter}
                                disabled={isFiltering}
                                className="h-10 rounded-lg bg-primary px-5 text-sm font-semibold text-primary-foreground transition hover:opacity-90 shadow-md shadow-primary/10 flex items-center gap-1.5 disabled:opacity-50"
                            >
                                {isFiltering ? (
                                    <Loader2 className="h-4 w-4 animate-spin" />
                                ) : (
                                    <Filter className="h-4 w-4" />
                                )}
                                {isFiltering ? "Filtrando..." : "Aplicar filtros"}
                            </button>
                            <button
                                onClick={handleClearAllFilters}
                                disabled={isFiltering}
                                className="h-10 rounded-lg border border-border bg-background px-5 text-sm font-semibold text-foreground hover:bg-muted transition flex items-center gap-1.5 disabled:opacity-50"
                            >
                                Limpiar Filtros
                            </button>
                        </div>
                        <div className="flex gap-2">
                            {canCreate && (
                                <button
                                    onClick={() => {
                                        setForm(emptyForm);
                                        setError("");
                                        setCreateOpen(true);
                                    }}
                                    className="inline-flex h-10 items-center gap-2 rounded-lg bg-emerald-600 px-5 text-sm font-semibold text-white transition hover:bg-emerald-700 shadow-md shadow-emerald-600/10"
                                >
                                    <Plus className="h-4 w-4" /> Ingresar bloque
                                </button>
                            )}
                            <button
                                onClick={() => {
                                    const params = new URLSearchParams();
                                    Object.entries(f).forEach(([key, val]) => {
                                        if (val) params.append(key, String(val));
                                    });
                                    setPdfUrl(`/bloques/pdf?${params.toString()}`);
                                    setPdfTitle("Reporte PDF - Bloques Físicos");
                                    setPdfModalOpen(true);
                                }}
                                disabled={!pagination.total}
                                className="inline-flex h-10 items-center gap-2 rounded-lg border border-border bg-background px-5 text-sm font-semibold text-foreground hover:bg-muted transition disabled:cursor-not-allowed disabled:opacity-60"
                                title={!pagination.total ? "No hay registros para generar el reporte" : ""}
                            >
                                <FileDown className="h-4 w-4" /> Reporte PDF
                            </button>
                        </div>
                    </div>

                    {/* Fila de filtros activos (Badges) */}
                    {activeFilters.length > 0 && (
                        <div className="flex flex-wrap items-center gap-2 bg-muted/40 p-3 rounded-lg border border-border/60">
                            <span className="text-[10px] font-black uppercase tracking-wider text-muted-foreground mr-1">Filtros Activos:</span>
                            {activeFilters.map((badge) => (
                                <span
                                    key={badge.field}
                                    className="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-background border border-border text-xs text-foreground font-medium"
                                >
                                    <span className="text-muted-foreground">{badge.label}:</span>
                                    <span>{badge.value}</span>
                                    <button
                                        onClick={() => handleRemoveFilter(badge.field)}
                                        className="text-muted-foreground hover:text-foreground hover:bg-muted rounded-full p-0.5"
                                        title="Eliminar filtro"
                                    >
                                        <X className="h-3 w-3" />
                                    </button>
                                </span>
                            ))}
                        </div>
                    )}
                </div>

                <div className="overflow-hidden rounded-xl border border-border bg-card shadow-sm">
                    <div className="overflow-x-auto">
                        <table className="w-full min-w-[920px] text-sm">
                            <thead className="bg-muted/60">
                                <tr className="text-left text-xs uppercase tracking-wide text-muted-foreground">
                                    <th className="px-4 py-3">#</th>
                                    <th className="px-4 py-3">N bloque</th>
                                    <th className="px-4 py-3">Asunto</th>
                                    <th className="px-4 py-3">Serie</th>
                                    <th className="px-4 py-3">Folios</th>
                                    <th className="px-4 py-3">Rango</th>
                                    <th className="px-4 py-3">Area</th>
                                    <th className="px-4 py-3 text-right">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                {blocks.length === 0 ? (
                                    <tr>
                                        <td colSpan={8} className="px-6 py-16 text-center">
                                            <div className="flex flex-col items-center justify-center max-w-md mx-auto">
                                                <div className="flex h-16 w-16 items-center justify-center rounded-2xl bg-amber-500/10 text-amber-500 mb-4 animate-bounce">
                                                    <Archive className="h-8 w-8" />
                                                </div>
                                                <h3 className="text-lg font-semibold text-foreground">No se encontraron bloques</h3>
                                                <p className="mt-1 text-sm text-muted-foreground">
                                                    No hay registros de bloques físicos en la base de datos o ningún registro coincide con los filtros aplicados.
                                                </p>
                                            </div>
                                        </td>
                                    </tr>
                                ) : (
                                    blocks.map((b, i) => (
                                        <tr
                                            key={b.id}
                                            className="border-t border-border transition-colors hover:bg-muted/30"
                                        >
                                            <td className="px-4 py-3 text-muted-foreground">
                                                {((pagination.current_page - 1) * 10) + i + 1}
                                            </td>
                                            <td className="px-4 py-3 font-semibold text-foreground">
                                                {b.n_bloque}
                                            </td>
                                            <td className="px-4 py-3 max-w-xs truncate" title={b.asunto}>
                                                {b.asunto}
                                            </td>
                                            <td className="px-4 py-3">
                                                {(() => {
                                                    const ds = b.documentary_series || b.documentarySeries;
                                                    return ds ? (
                                                        <span className="font-mono text-xs font-bold text-indigo-600" title={ds.nombre}>
                                                            {ds.codigo}
                                                        </span>
                                                    ) : (
                                                        <span className="text-muted-foreground italic text-xs">Sin serie</span>
                                                    );
                                                })()}
                                            </td>
                                            <td className="px-4 py-3 font-medium text-foreground">
                                                {b.folios || "-"}
                                            </td>
                                            <td className="px-4 py-3 text-xs">
                                                <div className="font-mono font-semibold text-slate-700">
                                                    {b.rango_inicial && b.rango_final ? `${b.rango_inicial} - ${b.rango_final}` : "-"}
                                                </div>
                                                {b.periods && b.periods.length > 1 && (
                                                    <div className="flex flex-wrap gap-1 mt-1">
                                                        {b.periods.map((p: any) => (
                                                            <span
                                                                key={p.id || `${p.rango_inicial}-${p.periodo}`}
                                                                className="inline-flex items-center gap-1 rounded bg-indigo-50 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-800/60 px-1.5 py-0.5 text-[10px] font-mono text-indigo-700 dark:text-indigo-300"
                                                                title={`Folios ${p.rango_inicial} al ${p.rango_final} del año ${p.periodo}`}
                                                            >
                                                                <Calendar className="h-2.5 w-2.5" />
                                                                {p.rango_inicial}-{p.rango_final} ({p.periodo})
                                                            </span>
                                                        ))}
                                                    </div>
                                                )}
                                            </td>
                                            <td className="px-4 py-3">
                                                <span className="inline-flex items-center gap-1.5 rounded-full border border-border bg-muted/30 px-2 py-0.5 text-xs text-muted-foreground">
                                                    <Building2 className="h-3 w-3" />{" "}
                                                    {b.area}
                                                </span>
                                            </td>
                                            <td className="px-4 py-3 text-right">
                                                <div className="flex justify-end gap-2">
                                                    <button
                                                        onClick={() => {
                                                            setSelectedBlock(b);
                                                            setShowOpen(true);
                                                        }}
                                                        className="inline-flex items-center gap-1.5 rounded-md bg-sky-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-sky-700"
                                                    >
                                                        <Eye className="h-3.5 w-3.5" />{" "}
                                                        Ver
                                                    </button>
                                                    {b.can?.update ? (
                                                        <button
                                                            onClick={() => openEdit(b)}
                                                            className="inline-flex items-center gap-1.5 rounded-md bg-amber-500 px-3 py-1.5 text-xs font-semibold text-white hover:bg-amber-600 transition"
                                                        >
                                                            <Pencil className="h-3.5 w-3.5" />{" "}
                                                            Editar
                                                        </button>
                                                    ) : b.root ? (
                                                        <button
                                                            disabled
                                                            title="No se puede editar ni eliminar el bloque porque ya fue digitalizado por Archivo Central."
                                                            className="inline-flex items-center gap-1.5 rounded-md bg-amber-500/40 px-3 py-1.5 text-xs font-semibold text-white/70 cursor-not-allowed"
                                                        >
                                                            <Pencil className="h-3.5 w-3.5" />{" "}
                                                            Editar
                                                        </button>
                                                    ) : null}
                                                    {b.can?.delete ? (
                                                        <button
                                                            onClick={() => {
                                                                setBlockToDelete(b);
                                                                setDeleteModalOpen(true);
                                                            }}
                                                            className="inline-flex items-center gap-1.5 rounded-md bg-red-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-red-700 transition"
                                                        >
                                                            <Trash2 className="h-3.5 w-3.5" />
                                                        </button>
                                                    ) : b.root ? (
                                                        <button
                                                            disabled
                                                            title="No se puede editar ni eliminar el bloque porque ya fue digitalizado por Archivo Central."
                                                            className="inline-flex items-center gap-1.5 rounded-md bg-red-600/40 px-3 py-1.5 text-xs font-semibold text-white/70 cursor-not-allowed"
                                                        >
                                                            <Trash2 className="h-3.5 w-3.5" />
                                                        </button>
                                                    ) : null}
                                                </div>
                                            </td>
                                        </tr>
                                    ))
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>

                <Pagination 
                    {...pagination}
                    onPageChange={(page) => router.get("/bloques", { ...f, page }, { preserveState: true, preserveScroll: true, only: ["blocks", "stats", "pagination", "filters"] })}
                    label="bloques"
                />

                {/* MODAL CREAR / EDITAR */}
                <Modal
                    open={createOpen || editOpen}
                    title={editOpen ? "Editar Bloque" : "Ingresar Bloque"}
                    onClose={() => {
                        if (!isSubmitting) {
                            setCreateOpen(false);
                            setEditOpen(false);
                        }
                    }}
                >
                    <form onSubmit={handleSubmit} className="space-y-4">
                        {error && (
                            <div className="rounded-lg border border-red-300 bg-red-50 p-3 text-xs text-red-700 flex items-center gap-2">
                                <AlertCircle className="h-4 w-4" />
                                {error}
                            </div>
                        )}
                        <div className="grid gap-3 md:grid-cols-2">
                            {editOpen && (
                                <div className="rounded-lg border border-border bg-muted/40 p-2.5 text-sm md:col-span-2 flex items-center justify-between">
                                    <span className="text-xs uppercase tracking-wider text-muted-foreground font-bold">Número de Bloque</span>
                                    <span className="font-semibold text-foreground">{form.n_bloque}</span>
                                </div>
                            )}
                            <div className="space-y-1">
                                <label className="text-xs font-semibold text-muted-foreground">Serie Documental</label>
                                <select
                                    value={form.documentary_series_id}
                                    onChange={(e) =>
                                        setForm({ ...form, documentary_series_id: e.target.value })
                                    }
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
                                <label className="text-xs font-semibold text-muted-foreground">Folios</label>
                                <input
                                    value={form.folios}
                                    onChange={(e) =>
                                        setForm({ ...form, folios: e.target.value })
                                    }
                                    placeholder="Folios"
                                    className="h-10 w-full rounded-lg border border-border bg-background px-3 text-sm"
                                    required
                                />
                            </div>
                            <div className="space-y-1 md:col-span-2">
                                <label className="text-xs font-semibold text-muted-foreground">Asunto / Descripción</label>
                                <input
                                    value={form.asunto}
                                    onChange={(e) =>
                                        setForm({ ...form, asunto: e.target.value })
                                    }
                                    placeholder="Asunto"
                                    className="h-10 w-full rounded-lg border border-border bg-background px-3 text-sm"
                                    required
                                />
                            </div>
                            <div className="space-y-1">
                                <label className="text-xs font-semibold text-muted-foreground">Rango Inicial</label>
                                <input
                                    type="number"
                                    value={form.rango_inicial}
                                    onChange={(e) => {
                                        const newStart = e.target.value;
                                        setForm((prev) => {
                                            const startNum = parseInt(newStart);
                                            const endNum = parseInt(prev.rango_final);
                                            const calculatedFolios = (!isNaN(startNum) && !isNaN(endNum) && endNum >= startNum)
                                                ? String(endNum - startNum + 1)
                                                : prev.folios;
                                            const updatedPeriods = prev.periods.length <= 1
                                                ? [{
                                                    ...prev.periods[0],
                                                    rango_inicial: newStart,
                                                    rango_final: prev.rango_final,
                                                    periodo: prev.periods[0]?.periodo || (prev.fecha ? prev.fecha.split("-")[0] : ""),
                                                }]
                                                : prev.periods;
                                            return { ...prev, rango_inicial: newStart, folios: calculatedFolios, periods: updatedPeriods };
                                        });
                                    }}
                                    placeholder="Rango inicial"
                                    className="h-10 w-full rounded-lg border border-border bg-background px-3 text-sm"
                                    required
                                />
                            </div>
                            <div className="space-y-1">
                                <label className="text-xs font-semibold text-muted-foreground">Rango Final</label>
                                <input
                                    type="number"
                                    value={form.rango_final}
                                    onChange={(e) => {
                                        const newEnd = e.target.value;
                                        setForm((prev) => {
                                            const startNum = parseInt(prev.rango_inicial);
                                            const endNum = parseInt(newEnd);
                                            const calculatedFolios = (!isNaN(startNum) && !isNaN(endNum) && endNum >= startNum)
                                                ? String(endNum - startNum + 1)
                                                : prev.folios;
                                            const updatedPeriods = prev.periods.length <= 1
                                                ? [{
                                                    ...prev.periods[0],
                                                    rango_inicial: prev.rango_inicial,
                                                    rango_final: newEnd,
                                                    periodo: prev.periods[0]?.periodo || (prev.fecha ? prev.fecha.split("-")[0] : ""),
                                                }]
                                                : prev.periods;
                                            return { ...prev, rango_final: newEnd, folios: calculatedFolios, periods: updatedPeriods };
                                        });
                                    }}
                                    placeholder="Rango final"
                                    className="h-10 w-full rounded-lg border border-border bg-background px-3 text-sm"
                                    required
                                />
                            </div>
                            <div className="space-y-1 md:col-span-2">
                                <label className="text-xs font-semibold text-muted-foreground">Fecha</label>
                                <input
                                    type="date"
                                    value={form.fecha}
                                    onChange={(e) => {
                                        const newDate = e.target.value;
                                        const newYear = newDate ? newDate.split("-")[0] : "";
                                        setForm((prev) => {
                                            const updatedPeriods = (prev.periods.length <= 1 && (!prev.periods[0]?.periodo || prev.periods[0]?.periodo === prev.fecha?.split("-")[0]))
                                                ? [{
                                                    rango_inicial: prev.periods[0]?.rango_inicial || prev.rango_inicial,
                                                    rango_final: prev.periods[0]?.rango_final || prev.rango_final,
                                                    periodo: newYear,
                                                }]
                                                : prev.periods;
                                            return { ...prev, fecha: newDate, periods: updatedPeriods };
                                        });
                                    }}
                                    className="h-10 w-full rounded-lg border border-border bg-background px-3 text-sm"
                                    required
                                />
                            </div>

                            {/* SECCIÓN DE PERIODOS Y SUB-RANGOS */}
                            <div className="space-y-3 md:col-span-2 rounded-xl border border-border/80 bg-muted/20 p-3.5">
                                <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                    <div>
                                        <h4 className="text-xs font-bold text-foreground flex items-center gap-1.5">
                                            <Calendar className="h-3.5 w-3.5 text-primary" />
                                            Distribución de Folios por Periodo (Años)
                                        </h4>
                                        <p className="text-[11px] text-muted-foreground">
                                            Si el bloque contiene documentos de distintos años, divide los folios por periodo aquí.
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
                                    {form.periods.map((p, pIdx) => (
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
                                                    placeholder="Ej. 2006"
                                                    className="h-8 w-full rounded-md border border-border bg-background px-2 text-xs font-mono font-bold"
                                                    required
                                                />
                                            </div>
                                            <div className="col-span-12 sm:col-span-2 flex justify-end items-end pt-2 sm:pt-0">
                                                {form.periods.length > 1 ? (
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
                        <div className="flex justify-end gap-2 pt-2">
                            <button
                                type="button"
                                onClick={() => {
                                    setCreateOpen(false);
                                    setEditOpen(false);
                                }}
                                disabled={isSubmitting}
                                className="rounded-lg border border-border bg-background px-4 py-2 text-sm font-medium text-foreground hover:bg-muted disabled:opacity-50"
                            >
                                Cancelar
                            </button>
                            <button
                                type="submit"
                                disabled={isSubmitting}
                                className="inline-flex items-center gap-2 rounded-lg bg-primary px-6 py-2 text-sm font-bold text-primary-foreground disabled:opacity-50 transition"
                            >
                                {isSubmitting ? (
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

                {/* MODAL CONFIRMAR ELIMINACIÓN */}
                <Modal
                    open={deleteModalOpen}
                    title="Confirmar Eliminación"
                    onClose={() => {
                        if (!isDeleting) setDeleteModalOpen(false);
                    }}
                    maxWidth="max-w-md"
                >
                    <div className="space-y-4 text-center py-2">
                        <div className="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-red-100 text-red-600 dark:bg-red-950/50 dark:text-red-400">
                            <AlertTriangle className="h-7 w-7" />
                        </div>
                        <div>
                            <h4 className="text-base font-semibold text-foreground">
                                ¿Eliminar Bloque Nº {blockToDelete?.n_bloque}?
                            </h4>
                            <p className="mt-1 text-xs text-muted-foreground">
                                Esta acción eliminará permanentemente el registro del bloque y sus archivos asociados.
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
                                onClick={handleDeleteBlock}
                                disabled={isDeleting}
                                className="inline-flex items-center gap-2 rounded-lg bg-red-600 px-5 py-2 text-sm font-bold text-white hover:bg-red-700 disabled:opacity-50 transition"
                            >
                                {isDeleting ? (
                                    <>
                                        <Loader2 className="h-4 w-4 animate-spin" /> Eliminando...
                                    </>
                                ) : (
                                    <>
                                        <Trash2 className="h-4 w-4" /> Eliminar Bloque
                                    </>
                                )}
                            </button>
                        </div>
                    </div>
                </Modal>

                {/* MODAL PREVISUALIZADOR DE PDF IN-APP */}
                <Modal
                    open={pdfModalOpen}
                    title={pdfTitle || "Previsualizador de Documentos"}
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
                                title="Previsualizador PDF"
                            />
                        </div>
                    </div>
                </Modal>

                {/* MODAL DETALLE PREMIUM */}
                <Modal
                    open={showOpen}
                    title="Detalle del Bloque"
                    onClose={() => setShowOpen(false)}
                    maxWidth="max-w-4xl"
                >
                    {selectedBlock && (
                        <div className="space-y-6">
                            <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
                                <div className="rounded-xl border border-border bg-muted/30 px-3 py-2">
                                    <p className="text-[10px] uppercase tracking-wider text-muted-foreground font-bold">
                                        N bloque
                                    </p>
                                    <p className="text-sm font-semibold text-foreground">
                                        {selectedBlock.n_bloque}
                                    </p>
                                </div>
                                <div className="rounded-xl border border-border bg-muted/30 px-3 py-2">
                                    <p className="text-[10px] uppercase tracking-wider text-muted-foreground font-bold">
                                        Serie Documental
                                    </p>
                                    <p className="text-sm font-semibold text-foreground">
                                        {(() => {
                                            const ds = selectedBlock.documentary_series || selectedBlock.documentarySeries;
                                            return ds ? `${ds.codigo} - ${ds.nombre}` : "Sin serie";
                                        })()}
                                    </p>
                                </div>
                                <div className="rounded-xl border border-border bg-muted/30 px-3 py-2">
                                    <p className="text-[10px] uppercase tracking-wider text-muted-foreground font-bold">
                                        Folios
                                    </p>
                                    <p className="text-sm font-semibold text-foreground">
                                        {selectedBlock.folios || "0"}
                                    </p>
                                </div>
                                <div className="rounded-xl border border-border bg-muted/30 px-3 py-2">
                                    <p className="text-[10px] uppercase tracking-wider text-muted-foreground font-bold">
                                        Fecha reg.
                                    </p>
                                    <p className="text-sm font-semibold text-foreground">
                                        {formatDateLabel(selectedBlock.fecha)}
                                    </p>
                                </div>
                                <div className="rounded-xl border border-border bg-muted/30 px-3 py-2">
                                    <p className="text-[10px] uppercase tracking-wider text-muted-foreground font-bold">
                                        Rango
                                    </p>
                                    <p className="text-sm font-semibold text-foreground">
                                        {selectedBlock.rango_inicial} -{" "}
                                        {selectedBlock.rango_final}
                                    </p>
                                </div>
                            </div>
                            <div className="rounded-xl border border-border bg-background p-4">
                                <p className="mb-1 text-[10px] uppercase tracking-wider text-muted-foreground font-bold">
                                    Asunto / Descripción
                                </p>
                                <p className="text-sm text-foreground leading-relaxed">
                                    {selectedBlock.asunto}
                                </p>
                            </div>

                            {selectedBlock.periods && selectedBlock.periods.length > 0 && (
                                <div className="rounded-xl border border-border bg-background p-4 space-y-2.5">
                                    <p className="text-[10px] uppercase tracking-wider text-muted-foreground font-bold flex items-center gap-1.5">
                                        <Calendar className="h-3.5 w-3.5 text-primary" /> Distribución de Folios por Periodo
                                    </p>
                                    <div className="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                                        {selectedBlock.periods.map((p: any, pIdx: number) => (
                                            <div key={p.id || pIdx} className="rounded-lg border border-border/70 bg-muted/20 p-2.5 flex items-center justify-between">
                                                <div>
                                                    <span className="text-xs font-semibold text-foreground">Folios {p.rango_inicial} - {p.rango_final}</span>
                                                    <p className="text-[10px] text-muted-foreground">({p.rango_final - p.rango_inicial + 1} folios)</p>
                                                </div>
                                                <span className="rounded bg-primary/10 px-2 py-0.5 font-mono text-xs font-bold text-primary">
                                                    Año {p.periodo}
                                                </span>
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            )}
                            <div className="grid gap-4 sm:grid-cols-2">
                                <div className="rounded-xl border border-border bg-background p-3">
                                    <p className="mb-2 text-[10px] uppercase tracking-wider text-muted-foreground font-bold">
                                        Ubicación Organizacional
                                    </p>
                                    <div className="space-y-2">
                                        <div className="flex justify-between text-xs border-b border-border pb-1">
                                            <span className="text-muted-foreground">
                                                Area:
                                            </span>
                                            <span className="font-medium text-foreground">
                                                {selectedBlock.area}
                                            </span>
                                        </div>
                                        <div className="flex justify-between text-xs border-b border-border pb-1">
                                            <span className="text-muted-foreground">
                                                Grupo:
                                            </span>
                                            <span className="font-medium text-foreground">
                                                {selectedBlock.group_name}
                                            </span>
                                        </div>
                                        <div className="flex justify-between text-xs">
                                            <span className="text-muted-foreground">
                                                Subgrupo:
                                            </span>
                                            <span className="font-medium text-foreground">
                                                {selectedBlock.subgroup_name}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div className="rounded-xl border border-border bg-background p-3">
                                    <p className="mb-2 text-[10px] uppercase tracking-wider text-muted-foreground font-bold">
                                        Almacenamiento Físico
                                    </p>
                                    {selectedBlock.box_info ? (
                                        <div className="space-y-2">
                                            <div className="flex justify-between text-xs border-b border-border pb-1">
                                                <span className="text-muted-foreground">
                                                    Sección:
                                                </span>
                                                <span className="font-medium text-foreground">
                                                    {
                                                        selectedBlock.box_info
                                                            .section
                                                    }
                                                </span>
                                            </div>
                                            <div className="flex justify-between text-xs border-b border-border pb-1">
                                                <span className="text-muted-foreground">
                                                    Andamio:
                                                </span>
                                                <span className="font-medium text-foreground">
                                                    {
                                                        selectedBlock.box_info
                                                            .andamio
                                                    }
                                                </span>
                                            </div>
                                            <div className="flex justify-between text-xs">
                                                <span className="text-muted-foreground">
                                                    Caja:
                                                </span>
                                                <span className="font-medium text-foreground">
                                                    Caja{" "}
                                                    {selectedBlock.box_info.box}
                                                </span>
                                            </div>
                                        </div>
                                    ) : (
                                        <div className="flex h-full flex-col items-center justify-center py-4 text-muted-foreground italic">
                                            <p className="text-xs">
                                                Pendiente de archivar
                                            </p>
                                        </div>
                                    )}
                                </div>
                            </div>
                            <div className="rounded-xl border border-border bg-background p-4 flex justify-between items-center">
                                <div>
                                    <p className="mb-1 text-[10px] uppercase tracking-wider text-muted-foreground font-bold">
                                        Archivo Digital
                                    </p>
                                    {selectedBlock.root ? (
                                        <button
                                            type="button"
                                            onClick={() =>
                                                handleViewFile(selectedBlock)
                                            }
                                            className="inline-flex items-center gap-2 text-sm font-semibold text-primary hover:underline"
                                        >
                                            <FileText className="h-4 w-4" />
                                            Ver documento adjunto
                                        </button>
                                    ) : (
                                        <p className="text-sm text-muted-foreground italic">
                                            Sin archivo digital
                                        </p>
                                    )}
                                </div>
                                <span className="text-xs italic text-muted-foreground">
                                    Registrado por:{" "}
                                    {selectedBlock.user
                                        ? `${selectedBlock.user.name} ${selectedBlock.user.last_name}`
                                        : "Sistema"}
                                </span>
                            </div>
                        </div>
                    )}
                </Modal>
            </div>
        </DashboardLayout>
    );
}
