import React, { useState } from 'react';
import DashboardLayout from '@/Layouts/DashboardLayout';
import { Modal } from '@/components/Modal';
import { router, usePage } from '@inertiajs/react';
import { 
  ClipboardList, Eye, FileDown, LayoutGrid, Users
} from 'lucide-react';
import Pagination from '@/components/ui/Pagination';

interface ActivityLogsIndexProps {
  logs: {
    data: any[];
    total: number;
    current_page: number;
    last_page: number;
    from: number;
  };
  users: any[];
  modules: string[];
  filters: any;
}

function toDisplayDate(value: string) {
  if (!value) return "-";
  const match = value.match(/(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2})/);
  if (!match) return value;
  const [, y, m, d, hh, mm] = match;
  return `${d}/${m}/${y} ${hh}:${mm}`;
}

export default function Index({ logs, users, modules, filters }: ActivityLogsIndexProps) {
  const { auth } = usePage().props as any;
  const [f, setF] = useState({
    date: filters.date || "",
    user_id: filters.user_id || "",
    module: filters.module || ""
  });

  const [dataModalOpen, setDataModalOpen] = useState(false);
  const [dataModalTitle, setDataModalTitle] = useState("");
  const [dataModalData, setDataModalData] = useState<any>(null);

  const can = (p: string) => auth.permissions?.includes(p) || auth.roles?.some((r: any) => (typeof r === 'string' ? r : r?.name || '').toUpperCase() === 'ADMINISTRADOR');

  const handleFilter = () => router.get('/activity-logs', f, { preserveState: true });

  const openDataModal = (title: string, data: any) => {
    setDataModalTitle(title);
    setDataModalData(data);
    setDataModalOpen(true);
  };

  return (
    <DashboardLayout title="Auditoría">
      <div className="space-y-5">
        <header className="rounded-2xl border border-border bg-gradient-to-r from-slate-900 to-emerald-700 p-5 text-white shadow-sm">
          <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
              <p className="text-xs uppercase tracking-[0.24em] text-white/60">Auditoría del Sistema</p>
              <h2 className="mt-2 text-2xl font-semibold">Registro de Actividades</h2>
              <p className="mt-1 text-sm text-white/75">Revisa cambios, usuarios y módulos afectados para garantizar la integridad.</p>
            </div>
            <div className="flex h-10 items-center gap-2 rounded-xl border border-white/20 bg-white/10 px-4 py-2 backdrop-blur-md">
              <ClipboardList className="h-4 w-4 text-emerald-300" />
              <span className="text-xs font-black uppercase tracking-wider text-white">
                {logs.total} <span className="text-white/60 ml-1">Registros</span>
              </span>
            </div>
          </div>
        </header>

        <div className="grid gap-3 sm:grid-cols-3">
          <article className="rounded-xl border border-border bg-card p-4 shadow-sm flex items-center gap-3">
            <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-primary/10 text-primary">
              <ClipboardList className="h-5 w-5" />
            </div>
            <div>
              <p className="text-xs text-muted-foreground">Actividades listadas</p>
              <p className="mt-1 text-2xl font-semibold text-foreground">{logs.total}</p>
            </div>
          </article>
          <article className="rounded-xl border border-border bg-card p-4 shadow-sm flex items-center gap-3">
            <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">
              <Users className="h-5 w-5" />
            </div>
            <div>
              <p className="text-xs text-muted-foreground">Usuarios registrados</p>
              <p className="mt-1 text-2xl font-semibold text-foreground">{users.length}</p>
            </div>
          </article>
          <article className="rounded-xl border border-border bg-card p-4 shadow-sm flex items-center gap-3">
            <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-100 text-amber-600">
              <LayoutGrid className="h-5 w-5" />
            </div>
            <div>
              <p className="text-xs text-muted-foreground">Módulos monitoreados</p>
              <p className="mt-1 text-2xl font-semibold text-foreground">{modules.length}</p>
            </div>
          </article>
        </div>

        <div className="rounded-xl border border-border bg-card p-4 shadow-sm">
          <div className="grid gap-3 md:grid-cols-3">
            <input type="date" value={f.date} onChange={e => setF({...f, date: e.target.value})} className="h-10 rounded-lg border border-border bg-background px-3 text-sm" />
            <select value={f.user_id} onChange={e => setF({...f, user_id: e.target.value})} className="h-10 rounded-lg border border-border bg-background px-3 text-sm">
              <option value="">Cualquier usuario</option>
              {users.map(u => <option key={u.id} value={u.id}>{u.name} {u.last_name}</option>)}
            </select>
            <select value={f.module} onChange={e => setF({...f, module: e.target.value})} className="h-10 rounded-lg border border-border bg-background px-3 text-sm">
              <option value="">Cualquier módulo</option>
              {modules.map(m => <option key={m} value={m}>{m}</option>)}
            </select>
          </div>
          <div className="mt-4 flex flex-wrap gap-2">
            <button onClick={handleFilter} className="h-10 rounded-lg bg-primary px-4 text-sm font-semibold text-primary-foreground">Aplicar filtros</button>
            {can('activity-logs.view') && (
              <button 
                type="button"
                disabled={!logs.total}
                onClick={() => window.open('/activity-logs/pdf', '_blank')}
                className="inline-flex h-10 items-center gap-2 rounded-lg bg-rose-600 px-4 text-sm font-semibold text-white disabled:cursor-not-allowed disabled:opacity-60"
                title={!logs.total ? "No hay registros para generar el reporte" : ""}
              >
                <FileDown className="h-4 w-4" /> Generar reporte
              </button>
            )}
            <button onClick={() => { setF({date:"", user_id:"", module:""}); router.get('/activity-logs'); }} className="h-10 rounded-lg border border-border bg-card px-4 text-sm font-semibold text-foreground">Limpiar</button>
          </div>
        </div>

        <div className="overflow-hidden rounded-xl border border-border bg-card shadow-sm">
          <div className="overflow-x-auto">
            <table className="w-full min-w-[800px] text-sm">
              <thead className="bg-muted/60">
                <tr className="text-left text-xs uppercase tracking-wide text-muted-foreground">
                  <th className="px-4 py-3">Fecha y Hora</th>
                  <th className="px-4 py-3">Usuario</th>
                  <th className="px-4 py-3">Módulo</th>
                  <th className="px-4 py-3">Acción</th>
                  <th className="px-4 py-3 text-right">Detalles</th>
                </tr>
              </thead>
              <tbody>
                {logs.data.map((log: any) => (
                  <tr key={log.id} className="border-t border-border hover:bg-muted/30 transition-colors">
                    <td className="px-4 py-3 text-muted-foreground">{toDisplayDate(log.created_at)}</td>
                    <td className="px-4 py-3 font-medium text-foreground">{log.user?.name} {log.user?.last_name}</td>
                    <td className="px-4 py-3">
                      <span className="rounded bg-primary/10 px-2 py-0.5 text-[10px] font-bold uppercase text-primary">
                        {log.model.replace("App\\Models\\", "")}
                      </span>
                    </td>
                    <td className="px-4 py-3 capitalize">
                      <span className="font-semibold text-slate-700">
                        {log.action || log.event || "Acción"}
                      </span>
                    </td>
                    <td className="px-4 py-3 text-right">
                      <div className="flex justify-end gap-2">
                        {log.before && (
                          <button onClick={() => openDataModal("Estado Anterior", log.before)} className="inline-flex items-center gap-1.5 rounded-md bg-slate-600 px-3 py-1.5 text-xs font-semibold text-white">
                            <Eye className="h-3.5 w-3.5" /> Ant.
                          </button>
                        )}
                        {log.after && (
                          <button onClick={() => openDataModal("Nuevo Estado", log.after)} className="inline-flex items-center gap-1.5 rounded-md bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white">
                            <Eye className="h-3.5 w-3.5" /> Sig.
                          </button>
                        )}
                      </div>
                    </td>
                  </tr>
                ))}
                {logs.data.length === 0 && (
                  <tr>
                    <td colSpan={5} className="px-4 py-16 text-center">
                      <div className="flex flex-col items-center justify-center max-w-md mx-auto">
                        <div className="flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-500/10 text-indigo-600 mb-4 animate-bounce">
                          <ClipboardList className="h-8 w-8" />
                        </div>
                        <h3 className="text-lg font-semibold text-foreground">No hay registros de actividad</h3>
                        <p className="mt-1 text-sm text-muted-foreground">
                          No hay registros de acciones o eventos en el sistema que coincidan con la búsqueda.
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
          {...logs}
          onPageChange={(page) => router.get('/activity-logs', { ...f, page }, { preserveState: true })}
          label="actividades"
        />

        <Modal open={dataModalOpen} title={dataModalTitle} onClose={() => setDataModalOpen(false)}>
          <div className="max-h-[60vh] overflow-y-auto rounded-lg bg-slate-950 p-4 font-mono text-xs text-emerald-400">
            <pre>{JSON.stringify(dataModalData, null, 2)}</pre>
          </div>
        </Modal>
      </div>
    </DashboardLayout>
  );
}
