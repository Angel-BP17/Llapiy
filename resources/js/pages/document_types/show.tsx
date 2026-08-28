import React from 'react';
import DashboardLayout from '@/Layouts/DashboardLayout';
import { Head, Link } from '@inertiajs/react';
import { ArrowLeft, Tags, LayoutGrid, Layers, FileText, ChevronRight } from 'lucide-react';
import docsRoutes from '@/routes/document_types';

interface ShowProps {
  documentType: any;
}

export default function Show({ documentType }: ShowProps) {
  return (
    <DashboardLayout title={`Plantilla: ${documentType.name}`}>
      <Head title={`Tipo de Documento: ${documentType.name}`} />
      
      <div className="space-y-6">
        <div className="flex items-center gap-4">
          <Link 
            href={docsRoutes.index.url()} 
            className="flex h-10 w-10 items-center justify-center rounded-full border border-border bg-card text-muted-foreground transition hover:text-foreground"
          >
            <ArrowLeft className="h-5 w-5" />
          </Link>
          <div>
            <h2 className="text-2xl font-bold text-foreground">Detalle de Plantilla</h2>
            <p className="text-sm text-muted-foreground">Configuración de metadatos y asignaciones organizacionales.</p>
          </div>
        </div>

        <div className="grid gap-6 lg:grid-cols-3">
          {/* Información General */}
          <div className="space-y-6 lg:col-span-1">
            <div className="rounded-2xl border border-border bg-card p-6 shadow-sm">
              <div className="flex items-center gap-3 mb-6">
                <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-primary/10 text-primary">
                  <FileText className="h-6 w-6" />
                </div>
                <div>
                  <h3 className="text-lg font-bold text-foreground">{documentType.name}</h3>
                  <p className="text-xs font-medium text-muted-foreground uppercase tracking-wider">ID: {documentType.id}</p>
                </div>
              </div>

              <div className="space-y-4">
                <div className="rounded-xl border border-border bg-muted/30 p-4">
                  <p className="text-xs font-bold uppercase tracking-wider text-muted-foreground mb-2">Campos de Metadatos</p>
                  <div className="flex flex-wrap gap-2">
                    {documentType.campo_types?.map((campo: any) => (
                      <span key={campo.id} className="inline-flex items-center gap-1 rounded-full bg-emerald-500/10 px-2.5 py-0.5 text-xs font-medium text-emerald-600 border border-emerald-500/20">
                        {campo.name}
                      </span>
                    ))}
                    {(!documentType.campo_types || documentType.campo_types.length === 0) && (
                      <p className="text-sm text-muted-foreground italic">Sin campos asignados</p>
                    )}
                  </div>
                </div>
              </div>
            </div>
          </div>

          {/* Asignaciones Organizacionales */}
          <div className="space-y-6 lg:col-span-2">
            <div className="rounded-2xl border border-border bg-card p-6 shadow-sm">
              <h4 className="mb-4 flex items-center gap-2 text-lg font-semibold text-foreground">
                <LayoutGrid className="h-5 w-5 text-primary" /> Grupos Asignados
              </h4>
              <div className="grid gap-3">
                {documentType.groups?.map((group: any) => (
                  <div key={group.id} className="flex items-center justify-between rounded-xl border border-border bg-muted/30 p-4">
                    <div className="flex items-center gap-3">
                      <div className="h-2 w-2 rounded-full bg-primary" />
                      <span className="font-medium text-foreground">{group.descripcion}</span>
                    </div>
                    <span className="text-xs text-muted-foreground uppercase">{group.abreviacion}</span>
                  </div>
                ))}
                {(!documentType.groups || documentType.groups.length === 0) && (
                  <div className="rounded-xl border border-dashed border-border p-8 text-center">
                    <p className="text-sm text-muted-foreground">No hay grupos asignados a esta plantilla.</p>
                  </div>
                )}
              </div>
            </div>

            <div className="rounded-2xl border border-border bg-card p-6 shadow-sm">
              <h4 className="mb-4 flex items-center gap-2 text-lg font-semibold text-foreground">
                <Layers className="h-5 w-5 text-primary" /> Subgrupos Asignados
              </h4>
              <div className="grid gap-3">
                {documentType.subgroups?.map((sub: any) => (
                  <div key={sub.id} className="flex items-center justify-between rounded-xl border border-border bg-muted/30 p-4">
                    <div className="flex items-center gap-3">
                      <ChevronRight className="h-4 w-4 text-primary" />
                      <span className="font-medium text-foreground">{sub.descripcion}</span>
                    </div>
                  </div>
                ))}
                {(!documentType.subgroups || documentType.subgroups.length === 0) && (
                  <div className="rounded-xl border border-dashed border-border p-8 text-center">
                    <p className="text-sm text-muted-foreground">No hay subgrupos asignados específicamente.</p>
                  </div>
                )}
              </div>
            </div>
          </div>
        </div>
      </div>
    </DashboardLayout>
  );
}
