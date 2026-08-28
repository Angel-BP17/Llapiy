import React, { useState } from 'react';
import DashboardLayout from '@/Layouts/DashboardLayout';
import { Settings, Database, Download, Upload, Trash2, AlertTriangle, Loader2, Sun, Moon } from 'lucide-react';
import { router, usePage } from '@inertiajs/react';
import { clearAll } from '@/actions/App/Http/Controllers/Home/SystemController';
import backupRoutes from '@/routes/configuration/backup';
import { usePermissions } from '@/hooks/use-permissions';

export default function Index() {
    const { props } = usePage();
    const currentTheme = (props.auth as any)?.theme || 'light';
    const { isAdmin } = usePermissions();

    const [isExporting, setIsExporting] = useState(false);
    const [isImporting, setIsImporting] = useState(false);
    const [isClearing, setIsClearing] = useState(false);

    const toggleTheme = (theme: 'light' | 'dark') => {
        router.post('/configuracion/theme', { theme });
    };

    const handleExport = () => {
        setIsExporting(true);
        // Usamos window.location para la descarga directa
        window.location.href = backupRoutes.export.url();
        setTimeout(() => setIsExporting(false), 2000);
    };

    const handleImport = (e: React.ChangeEvent<HTMLInputElement>) => {
        const file = e.target.files?.[0];
        if (!file) return;

        if (!confirm('¿Estás seguro de restaurar este respaldo? Se sobrescribirán todos los datos actuales.')) {
            e.target.value = "";
            return;
        }

        setIsImporting(true);
        router.post(backupRoutes.import.url(), { backup_file: file }, {
            forceFormData: true,
            onFinish: () => {
                setIsImporting(false);
                e.target.value = "";
            }
        });
    };

    const handleClearSystem = () => {
        if (!confirm('¡ADVERTENCIA CRÍTICA!\n\nEsta acción eliminará TODOS los registros (Documentos, Bloques, Usuarios, Logs) y reiniciará el sistema a su estado de fábrica.\n\n¿Deseas continuar?')) {
            return;
        }

        setIsClearing(true);
        const action = clearAll();
        router.delete(action.url, {
            onFinish: () => setIsClearing(false)
        });
    };

    return (
        <DashboardLayout title="Configuración">
            <div className="space-y-5">
                <header className="rounded-2xl border border-border bg-gradient-to-r from-slate-900 to-slate-700 p-5 text-white shadow-sm">
                    <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div className="flex items-center gap-4">
                            <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-white/10 text-white shadow-sm">
                                <Settings className="h-6 w-6" />
                            </div>
                            <div>
                                <p className="text-xs uppercase tracking-[0.24em] text-white/60">Ajustes del Sistema</p>
                                <h2 className="mt-1 text-2xl font-semibold">Configuración General</h2>
                                <p className="mt-1 text-sm text-white/75">Mantenimiento preventivo y gestión de datos del repositorio.</p>
                            </div>
                        </div>
                    </div>
                </header>

                <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    {/* Theme Preference Section */}
                    <section className="rounded-xl border border-border bg-card p-6 shadow-sm flex flex-col h-full">
                        <div className="flex items-center gap-3 mb-4">
                            <Sun className="h-5 w-5 text-amber-500 dark:hidden" />
                            <Moon className="h-5 w-5 text-indigo-400 hidden dark:block" />
                            <h3 className="font-bold text-foreground">Tema Visual</h3>
                        </div>
                        <p className="text-sm text-muted-foreground mb-6 flex-1">
                            Alterna entre el modo claro y oscuro para adaptar la visualización de la aplicación según tu preferencia personal.
                        </p>
                        <div className="grid grid-cols-2 gap-2 bg-muted/50 p-1 rounded-xl">
                            <button
                                type="button"
                                onClick={() => toggleTheme('light')}
                                className={`flex items-center justify-center gap-2 py-2 px-4 rounded-lg text-xs font-bold transition-all ${
                                    currentTheme === 'light'
                                        ? 'bg-white text-slate-900 shadow-sm border border-slate-200'
                                        : 'text-muted-foreground hover:bg-white/50'
                                }`}
                            >
                                <Sun className="h-4 w-4 text-amber-500" /> Claro
                            </button>
                            <button
                                type="button"
                                onClick={() => toggleTheme('dark')}
                                className={`flex items-center justify-center gap-2 py-2 px-4 rounded-lg text-xs font-bold transition-all ${
                                    currentTheme === 'dark'
                                        ? 'bg-slate-950 text-white shadow-sm border border-slate-800'
                                        : 'text-muted-foreground hover:bg-slate-950/20'
                                }`}
                            >
                                <Moon className="h-4 w-4 text-indigo-400" /> Oscuro
                            </button>
                        </div>
                    </section>
                    {isAdmin && (
                        <>
                            {/* Backup Section */}
                            <section className="rounded-xl border border-border bg-card p-6 shadow-sm flex flex-col h-full">
                                <div className="flex items-center gap-3 mb-4">
                                    <Database className="h-5 w-5 text-primary" />
                                    <h3 className="font-bold text-foreground">Copias de Seguridad</h3>
                                </div>
                                <p className="text-sm text-muted-foreground mb-6 flex-1">
                                    Genera un archivo comprimido que contiene la base de datos completa y todos los archivos digitales (PDFs y Fotos) almacenados.
                                </p>
                                <button 
                                    onClick={handleExport}
                                    disabled={isExporting}
                                    className="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-bold text-primary-foreground transition hover:opacity-90 disabled:opacity-50"
                                >
                                    {isExporting ? <Loader2 className="h-4 w-4 animate-spin" /> : <Download className="h-4 w-4" />}
                                    Realizar Backup
                                </button>
                            </section>

                            {/* Import Section */}
                            <section className="rounded-xl border border-border bg-card p-6 shadow-sm flex flex-col h-full">
                                <div className="flex items-center gap-3 mb-4">
                                    <Upload className="h-5 w-5 text-emerald-600" />
                                    <h3 className="font-bold text-foreground">Restaurar Sistema</h3>
                                </div>
                                <p className="text-sm text-muted-foreground mb-6 flex-1">
                                    Sube un archivo de respaldo (.zip) generado previamente para restaurar la información. 
                                    <span className="text-destructive font-semibold"> Esto reemplazará los datos actuales.</span>
                                </p>
                                <label className="w-full inline-flex items-center justify-center gap-2 rounded-lg border-2 border-dashed border-emerald-600/30 bg-emerald-50/50 dark:bg-emerald-950/10 px-4 py-2.5 text-sm font-bold text-emerald-700 dark:text-emerald-500 cursor-pointer transition hover:bg-emerald-50 dark:hover:bg-emerald-950/20">
                                    {isImporting ? <Loader2 className="h-4 w-4 animate-spin" /> : <Upload className="h-4 w-4" />}
                                    {isImporting ? 'Restaurando...' : 'Cargar Respaldo'}
                                    <input type="file" accept=".zip" className="hidden" onChange={handleImport} disabled={isImporting} />
                                </label>
                            </section>

                            {/* Clear System Section */}
                            <section className="rounded-xl border border-border bg-card p-6 shadow-sm flex flex-col h-full border-destructive/20 bg-destructive/5">
                                <div className="flex items-center gap-3 mb-4">
                                    <AlertTriangle className="h-5 w-5 text-destructive" />
                                    <h3 className="font-bold text-destructive">Zona de Peligro</h3>
                                </div>
                                <p className="text-sm text-muted-foreground mb-6 flex-1">
                                    Borra permanentemente todos los datos y restablece el sistema. Úsalo solo si sabes lo que estás haciendo.
                                </p>
                                <button 
                                    onClick={handleClearSystem}
                                    disabled={isClearing}
                                    className="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-destructive px-4 py-2.5 text-sm font-bold text-white transition hover:bg-destructive/90 disabled:opacity-50"
                                >
                                    {isClearing ? <Loader2 className="h-4 w-4 animate-spin" /> : <Trash2 className="h-4 w-4" />}
                                    Limpiar Sistema
                                </button>
                            </section>
                        </>
                    )}
                </div>
            </div>
        </DashboardLayout>
    );
}
