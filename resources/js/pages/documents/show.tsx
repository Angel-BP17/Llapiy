import React from 'react';
import { Head, Link } from '@inertiajs/react';
import DashboardLayout from '@/Layouts/DashboardLayout';
import { Document } from '@/types/models';
import { ArrowLeft, FileText, Calendar, User as UserIcon, Building2 } from 'lucide-react';

interface DocumentShowProps {
  document: Document;
}

export default function DocumentShow({ document }: DocumentShowProps) {
  return (
    <DashboardLayout>
      <Head title={`Documento - ${document.asunto || document.id}`} />
      <div className="container mx-auto px-4 py-6 max-w-4xl">
        <div className="mb-6 flex items-center justify-between">
          <Link
            href="/documentos"
            className="inline-flex items-center gap-2 text-sm font-medium text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-200"
          >
            <ArrowLeft className="w-4 h-4" />
            Volver a Documentos
          </Link>
        </div>

        <div className="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6">
          <div className="flex items-start gap-4">
            <div className="p-3 bg-blue-50 dark:bg-blue-950/50 rounded-lg text-blue-600 dark:text-blue-400">
              <FileText className="w-6 h-6" />
            </div>
            <div className="flex-1">
              <h1 className="text-xl font-bold text-slate-900 dark:text-white">
                {document.asunto || 'Sin asunto'}
              </h1>
              <p className="text-sm text-slate-500 dark:text-slate-400 mt-1">
                ID del Documento: #{document.id}
              </p>
            </div>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6 pt-6 border-t border-slate-100 dark:border-slate-800 text-sm">
            <div className="flex items-center gap-3 text-slate-600 dark:text-slate-300">
              <Calendar className="w-4 h-4 text-slate-400" />
              <span>Fecha: {document.fecha ? String(document.fecha) : 'No especificada'}</span>
            </div>

            <div className="flex items-center gap-3 text-slate-600 dark:text-slate-300">
              <UserIcon className="w-4 h-4 text-slate-400" />
              <span>Registrado por: {document.user?.name || 'Desconocido'}</span>
            </div>

            {document.group && (
              <div className="flex items-center gap-3 text-slate-600 dark:text-slate-300">
                <Building2 className="w-4 h-4 text-slate-400" />
                <span>Grupo: {document.group.descripcion}</span>
              </div>
            )}
          </div>
        </div>
      </div>
    </DashboardLayout>
  );
}
