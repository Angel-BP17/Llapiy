import React, { Dispatch, SetStateAction } from 'react';

type DataType = "string" | "text" | "char" | "int" | "float" | "double" | "boolean" | "enum" | "date" | "time" | "date_time";
export type CampoForm = {
  name: string;
  data_type: DataType;
  is_nullable: boolean;
  length: string;
  allow_negative: boolean;
  allow_zero: boolean;
  enum_values_text: string;
};

interface CampoFormFieldsProps {
  form: CampoForm;
  setForm: Dispatch<SetStateAction<CampoForm>>;
  error: string;
  submitLabel: string;
  isSubmitting?: boolean;
}

const dataTypes: DataType[] = ["string", "text", "char", "int", "float", "double", "boolean", "enum", "date", "time", "date_time"];

export function CampoFormFields({
  form, setForm, error, submitLabel, isSubmitting = false
}: CampoFormFieldsProps) {
  const isNumeric = ["int", "float", "double"].includes(form.data_type);
  const isEnum = form.data_type === "enum";

  return (
    <div className="space-y-4">
      {error && <div className="rounded-lg border border-red-300 bg-red-50 px-3 py-2 text-sm text-red-700">{error}</div>}
      <div className="grid gap-3">
        <div className="space-y-1">
          <label htmlFor="campo_name" className="text-xs font-bold uppercase text-muted-foreground">Nombre del Campo</label>
          <input 
            id="campo_name"
            name="name"
            value={form.name} 
            onChange={e => setForm({...form, name: e.target.value})} 
            className="h-10 w-full rounded-lg border border-border bg-background px-3 text-sm" 
            placeholder="Ej. Prioridad" 
          />
        </div>
        <div className="space-y-1">
          <label htmlFor="data_type" className="text-xs font-bold uppercase text-muted-foreground">Tipo de Dato</label>
          <select 
            id="data_type"
            name="data_type"
            value={form.data_type} 
            onChange={e => setForm({...form, data_type: e.target.value as DataType, enum_values_text: ""})} 
            className="h-10 w-full rounded-lg border border-border bg-background px-3 text-sm"
          >
            {dataTypes.map(t => (
              <option key={t} value={t}>
                {t === 'date_time' ? 'DATE AND TIME' : t.toUpperCase()}
              </option>
            ))}
          </select>
        </div>
        <div className="space-y-1">
          <label htmlFor="length" className="text-xs font-bold uppercase text-muted-foreground">Longitud Máxima (Opcional)</label>
          <input 
            id="length"
            name="length"
            type="number" 
            value={form.length} 
            onChange={e => setForm({...form, length: e.target.value})} 
            className="h-10 w-full rounded-lg border border-border bg-background px-3 text-sm" 
          />
        </div>
        <div className="flex items-center gap-2 pt-1">
          <input 
            id="is_nullable"
            type="checkbox" 
            checked={form.is_nullable} 
            onChange={e => setForm({...form, is_nullable: e.target.checked})} 
            className="h-4 w-4 rounded border-border" 
          />
          <label htmlFor="is_nullable" className="text-sm font-medium text-foreground">Permitir valor nulo</label>
        </div>
        
        {isNumeric && (
          <div className="flex gap-4 pt-1">
            <label className="flex items-center gap-2 text-sm text-foreground">
              <input type="checkbox" checked={form.allow_negative} onChange={e => setForm({...form, allow_negative: e.target.checked})} className="h-4 w-4 rounded border-border" />
              Permitir negativos
            </label>
            <label className="flex items-center gap-2 text-sm text-foreground">
              <input type="checkbox" checked={form.allow_zero} onChange={e => setForm({...form, allow_zero: e.target.checked})} className="h-4 w-4 rounded border-border" />
              Permitir cero
            </label>
          </div>
        )}

        {isEnum && (
          <div className="space-y-1">
            <label className="text-xs font-bold uppercase text-muted-foreground">Opciones (sep. por comas)</label>
            <textarea rows={3} value={form.enum_values_text} onChange={e => setForm({...form, enum_values_text: e.target.value})} className="w-full rounded-lg border border-border bg-background px-3 py-2 text-sm" placeholder="Alta, Media, Baja" />
          </div>
        )}
      </div>
      <div className="flex justify-end pt-4">
        <button type="submit" disabled={isSubmitting} className="rounded-lg bg-primary px-6 py-2 text-sm font-bold text-primary-foreground disabled:opacity-50">
          {submitLabel}
        </button>
      </div>
    </div>
  );
}
