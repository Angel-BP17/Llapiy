import React, { ChangeEvent, Dispatch, SetStateAction } from "react";
import { defaultAvatar } from "./UserTable";
import { Area, Role } from "@/types/models";

export type UserForm = {
  name: string;
  last_name: string;
  user_name: string;
  email: string;
  dni: string;
  password?: string;
  password_confirmation?: string;
  role_id: string; // Simplificamos para el formulario
  area_id?: string;
  group_type_id?: string;
  group_id: string;
  subgroup_id: string;
  foto_perfil: string | File | null;
};

type UserFormFieldsProps = {
  form: UserForm;
  setForm: Dispatch<SetStateAction<UserForm>>;
  areas: Area[];
  roles: Role[];
  isSubmitting?: boolean;
  isEdit?: boolean;
  submitLabel: string;
};

export function UserFormFields({
  form,
  setForm,
  areas,
  roles,
  isSubmitting = false,
  isEdit = false,
  submitLabel,
}: UserFormFieldsProps) {
  
  const onChange = (field: keyof UserForm, value: any) => {
    setForm(prev => ({ ...prev, [field]: value }));
  };

  const onImageChange = (e: ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (file) {
      onChange("foto_perfil", file);
    }
  };

  return (
    <div className="space-y-5">
      <div className="grid gap-4 lg:grid-cols-12">
        <div className="rounded-xl border border-border bg-background p-4 lg:col-span-4">
          <h4 className="text-sm font-semibold text-foreground">Foto de perfil</h4>
          <div className="mt-4 flex flex-col items-center gap-3">
            <img
              src={typeof form.foto_perfil === 'string' ? form.foto_perfil : defaultAvatar}
              alt="Foto de perfil"
              className="h-32 w-32 rounded-full border border-border object-cover"
            />
            <input
              type="file"
              accept="image/*"
              onChange={onImageChange}
              className="w-full rounded-lg border border-border bg-card px-3 py-2 text-xs"
            />
          </div>
        </div>

        <div className="rounded-xl border border-border bg-background p-4 lg:col-span-8">
          <h4 className="text-sm font-semibold text-foreground">Datos personales</h4>
          <div className="mt-3 grid gap-3 md:grid-cols-2">
            <input
              value={form.name}
              onChange={(e) => onChange("name", e.target.value.toUpperCase())}
              placeholder="Nombres"
              className="h-10 rounded-lg border border-border bg-card px-3 text-sm"
              required
            />
            <input
              value={form.last_name}
              onChange={(e) => onChange("last_name", e.target.value.toUpperCase())}
              placeholder="Apellidos"
              className="h-10 rounded-lg border border-border bg-card px-3 text-sm"
              required
            />
            <input
              value={form.dni}
              onChange={(e) => onChange("dni", e.target.value)}
              placeholder="DNI"
              className="h-10 rounded-lg border border-border bg-card px-3 text-sm"
            />
          </div>

          <h4 className="mt-5 text-sm font-semibold text-foreground">Cuenta</h4>
          <div className="mt-3 grid gap-3 md:grid-cols-2">
            <input
              value={form.user_name}
              onChange={(e) => onChange("user_name", e.target.value.toUpperCase())}
              placeholder="Usuario"
              className="h-10 rounded-lg border border-border bg-card px-3 text-sm"
              required
            />
            <input
              value={form.email}
              onChange={(e) => onChange("email", e.target.value)}
              placeholder="Correo"
              type="email"
              className="h-10 rounded-lg border border-border bg-card px-3 text-sm"
              required
            />
            <input
              value={form.password || ''}
              onChange={(e) => onChange("password", e.target.value)}
              placeholder={isEdit ? "Nueva contraseña (opcional)" : "Contraseña"}
              type="password"
              className="h-10 rounded-lg border border-border bg-card px-3 text-sm"
              required={!isEdit}
            />
            <input
              value={form.password_confirmation || ''}
              onChange={(e) => onChange("password_confirmation", e.target.value)}
              placeholder="Confirmar contraseña"
              type="password"
              className="h-10 rounded-lg border border-border bg-card px-3 text-sm"
              required={!isEdit}
            />
          </div>

          <h4 className="mt-5 text-sm font-semibold text-foreground">Rol y Organización</h4>
          <div className="mt-3 grid gap-3 md:grid-cols-2">
            <select
              value={form.role_id}
              onChange={(e) => onChange("role_id", e.target.value)}
              className="h-10 rounded-lg border border-border bg-card px-3 text-sm"
              required
            >
              <option value="">Seleccione un rol</option>
              {roles.map((role) => (
                <option key={role.id} value={String(role.id)}>{role.label || role.name}</option>
              ))}
            </select>

            <select
              value={form.area_id || ""}
              onChange={(e) => {
                const val = e.target.value;
                setForm(prev => ({
                  ...prev,
                  area_id: val,
                  group_type_id: "",
                  group_id: "",
                  subgroup_id: ""
                }));
              }}
              className="h-10 rounded-lg border border-border bg-card px-3 text-sm"
            >
              <option value="">Seleccione un área (Opcional)</option>
              {areas.map((area) => (
                <option key={area.id} value={String(area.id)}>{area.descripcion}</option>
              ))}
            </select>

            {form.area_id && (
              <select
                value={form.group_type_id || ""}
                onChange={(e) => {
                  const val = e.target.value;
                  setForm(prev => ({
                    ...prev,
                    group_type_id: val,
                    group_id: "",
                    subgroup_id: ""
                  }));
                }}
                className="h-10 rounded-lg border border-border bg-card px-3 text-sm"
                required
              >
                <option value="">Seleccione tipo de grupo</option>
                {(() => {
                  const area = areas.find(a => String(a.id) === form.area_id);
                  const agts = area?.area_group_types || [];
                  return agts.map(agt => (
                    <option key={agt.group_type_id} value={String(agt.group_type_id)}>
                      {agt.group_type?.descripcion}
                    </option>
                  ));
                })()}
              </select>
            )}

            {form.area_id && form.group_type_id && (
              <select
                value={form.group_id || ""}
                onChange={(e) => {
                  const val = e.target.value;
                  setForm(prev => ({
                    ...prev,
                    group_id: val,
                    subgroup_id: ""
                  }));
                }}
                className="h-10 rounded-lg border border-border bg-card px-3 text-sm"
                required
              >
                <option value="">Seleccione un grupo</option>
                {(() => {
                  const area = areas.find(a => String(a.id) === form.area_id);
                  const agt = area?.area_group_types?.find(x => String(x.group_type_id) === form.group_type_id);
                  const groups = agt?.groups || [];
                  return groups.map(g => (
                    <option key={g.id} value={String(g.id)}>{g.descripcion}</option>
                  ));
                })()}
              </select>
            )}

            {form.area_id && form.group_type_id && form.group_id && (
              <select
                value={form.subgroup_id || ""}
                onChange={(e) => onChange("subgroup_id", e.target.value)}
                className="h-10 rounded-lg border border-border bg-card px-3 text-sm"
              >
                <option value="">Seleccione un subgrupo (Opcional)</option>
                {(() => {
                  const area = areas.find(a => String(a.id) === form.area_id);
                  const agt = area?.area_group_types?.find(x => String(x.group_type_id) === form.group_type_id);
                  const group = agt?.groups?.find(g => String(g.id) === form.group_id);
                  const subgroups = group?.subgroups || [];
                  return subgroups.map(sg => (
                    <option key={sg.id} value={String(sg.id)}>{sg.descripcion}</option>
                  ));
                })()}
              </select>
            )}
          </div>
        </div>
      </div>

      <div className="flex justify-end pt-4">
        <button
          type="submit"
          disabled={isSubmitting}
          className="rounded-lg bg-primary px-8 py-2 text-sm font-bold text-primary-foreground disabled:opacity-50 shadow-lg shadow-primary/20 transition-all hover:scale-[1.02]"
        >
          {submitLabel}
        </button>
      </div>
    </div>
  );
}
