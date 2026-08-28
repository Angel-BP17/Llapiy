import React, { Dispatch, SetStateAction } from 'react';
import { DocumentType, Area } from '@/types/models';

export type DocumentForm = {
  asunto: string;
  n_documento: string;
  folios: number;
  document_type_id: string;
  root: File | string | null;
  campos: Record<number, string>;
};

interface DocumentFormFieldsProps {
  form: DocumentForm;
  setForm: Dispatch<SetStateAction<DocumentForm>>;
  documentTypes: DocumentType[];
  areas: Area[];
  isSubmitting?: boolean;
  isEdit?: boolean;
  submitLabel: string;
}

export function DocumentFormFields({
  form,
  setForm,
  documentTypes,
  areas,
  isSubmitting = false,
  isEdit = false,
  submitLabel
}: DocumentFormFieldsProps) {
  
  const onChange = (field: keyof DocumentForm, value: any) => {
    setForm(prev => ({ ...prev, [field]: value }));
  };

  const handleFieldChange = (campoTypeId: number, value: string) => {
    setForm(prev => ({
      ...prev,
      campos: { ...prev.campos, [campoTypeId]: value }
    }));
  };

  const selectedType = documentTypes.find(t => String(t.id) === form.document_type_id);

  return (
    <div className="space-y-6">
      <div className="grid gap-4 md:grid-cols-2">
        <div className="space-y-1.5">
          <label className="text-[10px] font-black uppercase text-muted-foreground pl-1">Asunto / Título</label>
          <input 
            value={form.asunto} 
            onChange={e => onChange('asunto', e.target.value.toUpperCase())}
            className="h-11 w-full rounded-xl border border-border bg-background px-4 text-sm focus:ring-2 focus:ring-primary/20 outline-none"
            placeholder="EJ. INFORME DE GESTIÓN Q1"
            required
          />
        </div>
        <div className="space-y-1.5">
          <label className="text-[10px] font-black uppercase text-muted-foreground pl-1">Nº de Documento</label>
          <input 
            value={form.n_documento} 
            onChange={e => onChange('n_documento', e.target.value.toUpperCase())}
            className="h-11 w-full rounded-xl border border-border bg-background px-4 text-sm focus:ring-2 focus:ring-primary/20 outline-none"
            placeholder="EJ. INF-2024-001"
            required
          />
        </div>
      </div>

      <div className="grid gap-4 md:grid-cols-3">
        <div className="space-y-1.5">
          <label className="text-[10px] font-black uppercase text-muted-foreground pl-1">Tipo de Documento</label>
          <select 
            value={form.document_type_id} 
            onChange={e => onChange('document_type_id', e.target.value)}
            className="h-11 w-full rounded-xl border border-border bg-background px-4 text-sm focus:ring-2 focus:ring-primary/20 outline-none"
            required
          >
            <option value="">Seleccionar...</option>
            {documentTypes.map(t => <option key={t.id} value={String(t.id)}>{t.name}</option>)}
          </select>
        </div>
        <div className="space-y-1.5">
          <label className="text-[10px] font-black uppercase text-muted-foreground pl-1">Nº de Folios</label>
          <input 
            type="number"
            min="1"
            value={form.folios} 
            onChange={e => onChange('folios', parseInt(e.target.value))}
            className="h-11 w-full rounded-xl border border-border bg-background px-4 text-sm focus:ring-2 focus:ring-primary/20 outline-none"
            required
          />
        </div>
        <div className="space-y-1.5">
          <label className="text-[10px] font-black uppercase text-muted-foreground pl-1">Archivo Digital (PDF)</label>
          <input 
            type="file"
            accept=".pdf"
            onChange={e => onChange('root', e.target.files?.[0] || null)}
            className="h-11 w-full rounded-xl border border-border bg-background px-4 py-2 text-xs focus:ring-2 focus:ring-primary/20 outline-none"
            required={!isEdit}
          />
        </div>
      </div>

      {selectedType && selectedType.campo_types && selectedType.campo_types.length > 0 && (
        <div className="space-y-4 rounded-2xl bg-muted/30 p-5 border border-border">
          <h4 className="text-xs font-black uppercase text-muted-foreground tracking-widest border-b border-border pb-2">Metadatos del Tipo: {selectedType.name}</h4>
          <div className="grid gap-4 sm:grid-cols-2">
            {selectedType.campo_types.map(campo => (
              <div key={campo.id} className="space-y-1.5">
                <label className="text-[10px] font-bold uppercase text-slate-500 pl-1">{campo.name}</label>
                <input 
                  type={
                    campo.data_type === 'date' ? 'date' :
                    campo.data_type === 'time' ? 'time' :
                    campo.data_type === 'date_time' ? 'datetime-local' :
                    ['int', 'float', 'double'].includes(campo.data_type) ? 'number' : 'text'
                  }
                  value={(() => {
                    const rawVal = form.campos[campo.id] || '';
                    if (campo.data_type === 'date_time' && rawVal) {
                      return rawVal.replace(' ', 'T').substring(0, 16);
                    }
                    if (campo.data_type === 'date' && rawVal) {
                      return rawVal.substring(0, 10);
                    }
                    return rawVal;
                  })()}
                  onChange={e => handleFieldChange(campo.id, e.target.value)}
                  className="h-10 w-full rounded-lg border border-border bg-background px-3 text-sm focus:ring-2 focus:ring-primary/20 outline-none"
                  required={campo.required}
                />
              </div>
            ))}
          </div>
        </div>
      )}

      <div className="flex justify-end pt-4">
        <button
          type="submit"
          disabled={isSubmitting}
          className="rounded-xl bg-primary px-10 py-3 text-sm font-bold text-primary-foreground shadow-lg shadow-primary/20 transition-all hover:scale-[1.02] disabled:opacity-50"
        >
          {submitLabel}
        </button>
      </div>
    </div>
  );
}
