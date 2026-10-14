import React, { ChangeEvent, Dispatch, SetStateAction, useMemo } from "react";
import { defaultAvatar } from "./UserTable";
import { Area, Role } from "@/types/models";
import { Loader2, AlertTriangle } from "lucide-react";

export type UserForm = {
  name: string;
  last_name: string;
  user_name: string;
  email: string;
  dni: string;
  password?: string;
  password_confirmation?: string;
  role_id: string;
  area_id?: string;
  group_type_id?: string;
  group_id?: string;
  subgroup_id?: string;
  foto_perfil: string | File | null;
};

type UserFormFieldsProps = {
  form: UserForm;
  setForm: Dispatch<SetStateAction<UserForm>>;
  areas: Area[];
  roles: Role[];
  errors?: Record<string, string>;
  isSubmitting?: boolean;
  isEdit?: boolean;
  submitLabel: string;
};

export function UserFormFields({
  form,
  setForm,
  areas,
  roles,
  errors = {},
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

  const avatarSrc = useMemo(() => {
    if (form.foto_perfil instanceof File) {
      return URL.createObjectURL(form.foto_perfil);
    }
    if (typeof form.foto_perfil === "string" && form.foto_perfil.length > 0) {
      return form.foto_perfil;
    }
    return defaultAvatar;
  }, [form.foto_perfil]);

  const hasErrors = Object.keys(errors).length > 0;

  return (
    <div className="space-y-5">
      {hasErrors && (
        <div className="rounded-xl border border-destructive/30 bg-destructive/10 p-3.5 text-destructive text-sm flex items-start gap-2.5">
          <AlertTriangle className="h-5 w-5 shrink-0 mt-0.5 text-destructive" />
          <div className="space-y-1">
            <p className="font-semibold text-xs uppercase tracking-wider">No se pudo guardar el usuario</p>
            <p className="text-xs text-destructive/90">Por favor revisa y corrige los campos marcados:</p>
            <ul className="list-disc list-inside text-xs space-y-0.5 mt-1 font-medium">
              {Object.entries(errors).map(([key, msg]) => (
                <li key={key}>{msg}</li>
              ))}
            </ul>
          </div>
        </div>
      )}

      <div className="grid gap-4 lg:grid-cols-12">
        {/* Foto de Perfil */}
        <div className="rounded-xl border border-border bg-background p-4 lg:col-span-4">
          <h4 className="text-sm font-semibold text-foreground">Foto de perfil</h4>
          <div className="mt-4 flex flex-col items-center gap-3">
            <img
              src={avatarSrc}
              alt="Foto de perfil"
              className="h-32 w-32 rounded-full border border-border object-cover"
            />
            <input
              type="file"
              accept="image/*"
              onChange={onImageChange}
              className="w-full rounded-lg border border-border bg-card px-3 py-2 text-xs"
            />
            {errors.foto_perfil && (
              <p className="text-xs font-medium text-destructive">{errors.foto_perfil}</p>
            )}
          </div>
        </div>

        {/* Datos Personales y Cuenta */}
        <div className="rounded-xl border border-border bg-background p-4 lg:col-span-8 space-y-4">
          <div>
            <h4 className="text-sm font-semibold text-foreground mb-3">Datos personales</h4>
            <div className="grid gap-3 md:grid-cols-2">
              <div className="space-y-1">
                <label className="text-xs font-semibold text-foreground">
                  Nombres <span className="text-destructive">*</span>
                </label>
                <input
                  value={form.name}
                  onChange={(e) => onChange("name", e.target.value.toUpperCase())}
                  placeholder="Ej. JUAN CARLOS"
                  className={`h-10 w-full rounded-lg border bg-card px-3 text-sm outline-none transition ${
                    errors.name ? "border-destructive focus:ring-2 focus:ring-destructive/20" : "border-border focus:ring-2 focus:ring-primary/20"
                  }`}
                  required
                />
                {errors.name && <p className="text-xs font-medium text-destructive">{errors.name}</p>}
              </div>

              <div className="space-y-1">
                <label className="text-xs font-semibold text-foreground">
                  Apellidos <span className="text-destructive">*</span>
                </label>
                <input
                  value={form.last_name}
                  onChange={(e) => onChange("last_name", e.target.value.toUpperCase())}
                  placeholder="Ej. PÉREZ QUISPE"
                  className={`h-10 w-full rounded-lg border bg-card px-3 text-sm outline-none transition ${
                    errors.last_name ? "border-destructive focus:ring-2 focus:ring-destructive/20" : "border-border focus:ring-2 focus:ring-primary/20"
                  }`}
                  required
                />
                {errors.last_name && <p className="text-xs font-medium text-destructive">{errors.last_name}</p>}
              </div>

              <div className="space-y-1 md:col-span-2">
                <label className="text-xs font-semibold text-foreground">
                  Documento de Identidad (DNI) <span className="text-destructive">*</span>
                </label>
                <input
                  value={form.dni}
                  onChange={(e) => onChange("dni", e.target.value.replace(/\D/g, "").slice(0, 10))}
                  placeholder="Ej. 12345678"
                  maxLength={10}
                  className={`h-10 w-full rounded-lg border bg-card px-3 text-sm outline-none transition ${
                    errors.dni ? "border-destructive focus:ring-2 focus:ring-destructive/20" : "border-border focus:ring-2 focus:ring-primary/20"
                  }`}
                  required
                />
                {errors.dni && <p className="text-xs font-medium text-destructive">{errors.dni}</p>}
              </div>
            </div>
          </div>

          <div>
            <h4 className="text-sm font-semibold text-foreground mb-3">Cuenta y Seguridad</h4>
            <div className="grid gap-3 md:grid-cols-2">
              <div className="space-y-1">
                <label className="text-xs font-semibold text-foreground">
                  Nombre de Usuario <span className="text-destructive">*</span>
                </label>
                <input
                  value={form.user_name}
                  onChange={(e) => onChange("user_name", e.target.value.toUpperCase())}
                  placeholder="Ej. JPEREZ"
                  className={`h-10 w-full rounded-lg border bg-card px-3 text-sm outline-none transition ${
                    errors.user_name ? "border-destructive focus:ring-2 focus:ring-destructive/20" : "border-border focus:ring-2 focus:ring-primary/20"
                  }`}
                  required
                />
                {errors.user_name && <p className="text-xs font-medium text-destructive">{errors.user_name}</p>}
              </div>

              <div className="space-y-1">
                <label className="text-xs font-semibold text-foreground">
                  Correo Electrónico <span className="text-destructive">*</span>
                </label>
                <input
                  value={form.email}
                  onChange={(e) => onChange("email", e.target.value)}
                  placeholder="Ej. usuario@empresa.com"
                  type="email"
                  className={`h-10 w-full rounded-lg border bg-card px-3 text-sm outline-none transition ${
                    errors.email ? "border-destructive focus:ring-2 focus:ring-destructive/20" : "border-border focus:ring-2 focus:ring-primary/20"
                  }`}
                  required
                />
                {errors.email && <p className="text-xs font-medium text-destructive">{errors.email}</p>}
              </div>

              <div className="space-y-1">
                <label className="text-xs font-semibold text-foreground">
                  Contraseña {!isEdit && <span className="text-destructive">*</span>}
                </label>
                <input
                  value={form.password || ""}
                  onChange={(e) => onChange("password", e.target.value)}
                  placeholder={isEdit ? "Nueva contraseña (opcional)" : "Mínimo 6 caracteres"}
                  type="password"
                  className={`h-10 w-full rounded-lg border bg-card px-3 text-sm outline-none transition ${
                    errors.password ? "border-destructive focus:ring-2 focus:ring-destructive/20" : "border-border focus:ring-2 focus:ring-primary/20"
                  }`}
                  required={!isEdit}
                />
                {errors.password && <p className="text-xs font-medium text-destructive">{errors.password}</p>}
              </div>

              <div className="space-y-1">
                <label className="text-xs font-semibold text-foreground">
                  Confirmar Contraseña {!isEdit && <span className="text-destructive">*</span>}
                </label>
                <input
                  value={form.password_confirmation || ""}
                  onChange={(e) => onChange("password_confirmation", e.target.value)}
                  placeholder="Repetir contraseña"
                  type="password"
                  className={`h-10 w-full rounded-lg border bg-card px-3 text-sm outline-none transition ${
                    errors.password_confirmation ? "border-destructive focus:ring-2 focus:ring-destructive/20" : "border-border focus:ring-2 focus:ring-primary/20"
                  }`}
                  required={!isEdit}
                />
                {errors.password_confirmation && (
                  <p className="text-xs font-medium text-destructive">{errors.password_confirmation}</p>
                )}
              </div>
            </div>
          </div>

          <div>
            <h4 className="text-sm font-semibold text-foreground mb-3">Rol y Organización</h4>
            <div className="grid gap-3 md:grid-cols-2">
              <div className="space-y-1">
                <label className="text-xs font-semibold text-foreground">
                  Rol Asignado <span className="text-destructive">*</span>
                </label>
                <select
                  value={form.role_id}
                  onChange={(e) => onChange("role_id", e.target.value)}
                  className={`h-10 w-full rounded-lg border bg-card px-3 text-sm outline-none transition ${
                    errors.role_id ? "border-destructive focus:ring-2 focus:ring-destructive/20" : "border-border focus:ring-2 focus:ring-primary/20"
                  }`}
                  required
                >
                  <option value="">Seleccione un rol</option>
                  {roles.map((role) => (
                    <option key={role.id} value={String(role.id)}>
                      {role.label || role.name}
                    </option>
                  ))}
                </select>
                {errors.role_id && <p className="text-xs font-medium text-destructive">{errors.role_id}</p>}
              </div>

              <div className="space-y-1">
                <label className="text-xs font-semibold text-foreground">Área (Opcional)</label>
                <select
                  value={form.area_id || ""}
                  onChange={(e) => {
                    const val = e.target.value;
                    setForm((prev) => ({
                      ...prev,
                      area_id: val,
                      group_type_id: "",
                      group_id: "",
                      subgroup_id: "",
                    }));
                  }}
                  className={`h-10 w-full rounded-lg border bg-card px-3 text-sm outline-none transition ${
                    errors.area_id ? "border-destructive focus:ring-2 focus:ring-destructive/20" : "border-border focus:ring-2 focus:ring-primary/20"
                  }`}
                >
                  <option value="">Seleccione un área (Opcional)</option>
                  {areas.map((area) => (
                    <option key={area.id} value={String(area.id)}>
                      {area.descripcion}
                    </option>
                  ))}
                </select>
                {errors.area_id && <p className="text-xs font-medium text-destructive">{errors.area_id}</p>}
              </div>

              {form.area_id && (
                <div className="space-y-1">
                  <label className="text-xs font-semibold text-foreground">
                    Tipo de Grupo <span className="text-destructive">*</span>
                  </label>
                  {(() => {
                    const area = areas.find((a) => String(a.id) === form.area_id);
                    const agts = area?.area_group_types || [];
                    return (
                      <>
                        <select
                          value={form.group_type_id || ""}
                          onChange={(e) => {
                            const val = e.target.value;
                            setForm((prev) => ({
                              ...prev,
                              group_type_id: val,
                              group_id: "",
                              subgroup_id: "",
                            }));
                          }}
                          className={`h-10 w-full rounded-lg border bg-card px-3 text-sm outline-none transition ${
                            errors.group_type_id ? "border-destructive focus:ring-2 focus:ring-destructive/20" : "border-border focus:ring-2 focus:ring-primary/20"
                          }`}
                          required={agts.length > 0}
                        >
                          <option value="">
                            {agts.length > 0 ? "Seleccione tipo de grupo" : "No hay tipos de grupo registrados"}
                          </option>
                          {agts.map((agt) => (
                            <option key={agt.group_type_id} value={String(agt.group_type_id)}>
                              {agt.group_type?.descripcion}
                            </option>
                          ))}
                        </select>
                        {errors.group_type_id && (
                          <p className="text-xs font-medium text-destructive">{errors.group_type_id}</p>
                        )}
                      </>
                    );
                  })()}
                </div>
              )}

              {form.area_id && form.group_type_id && (
                <div className="space-y-1">
                  <label className="text-xs font-semibold text-foreground">
                    Grupo <span className="text-destructive">*</span>
                  </label>
                  {(() => {
                    const area = areas.find((a) => String(a.id) === form.area_id);
                    const agt = area?.area_group_types?.find((x) => String(x.group_type_id) === form.group_type_id);
                    const groups = agt?.groups || [];
                    return (
                      <>
                        <select
                          value={form.group_id || ""}
                          onChange={(e) => {
                            const val = e.target.value;
                            setForm((prev) => ({
                              ...prev,
                              group_id: val,
                              subgroup_id: "",
                            }));
                          }}
                          className={`h-10 w-full rounded-lg border bg-card px-3 text-sm outline-none transition ${
                            errors.group_id ? "border-destructive focus:ring-2 focus:ring-destructive/20" : "border-border focus:ring-2 focus:ring-primary/20"
                          }`}
                          required={groups.length > 0}
                        >
                          <option value="">
                            {groups.length > 0 ? "Seleccione un grupo" : "No hay grupos registrados"}
                          </option>
                          {groups.map((g) => (
                            <option key={g.id} value={String(g.id)}>
                              {g.descripcion}
                            </option>
                          ))}
                        </select>
                        {errors.group_id && <p className="text-xs font-medium text-destructive">{errors.group_id}</p>}
                      </>
                    );
                  })()}
                </div>
              )}

              {form.area_id && form.group_type_id && form.group_id && (
                <div className="space-y-1 md:col-span-2">
                  <label className="text-xs font-semibold text-foreground">Subgrupo (Opcional)</label>
                  {(() => {
                    const area = areas.find((a) => String(a.id) === form.area_id);
                    const agt = area?.area_group_types?.find((x) => String(x.group_type_id) === form.group_type_id);
                    const group = agt?.groups?.find((g) => String(g.id) === form.group_id);
                    const subgroups = group?.subgroups || [];
                    return (
                      <>
                        <select
                          value={form.subgroup_id || ""}
                          onChange={(e) => onChange("subgroup_id", e.target.value)}
                          className={`h-10 w-full rounded-lg border bg-card px-3 text-sm outline-none transition ${
                            errors.subgroup_id ? "border-destructive focus:ring-2 focus:ring-destructive/20" : "border-border focus:ring-2 focus:ring-primary/20"
                          }`}
                        >
                          <option value="">Seleccione un subgrupo (Opcional)</option>
                          {subgroups.map((sg) => (
                            <option key={sg.id} value={String(sg.id)}>
                              {sg.descripcion}
                            </option>
                          ))}
                        </select>
                        {errors.subgroup_id && (
                          <p className="text-xs font-medium text-destructive">{errors.subgroup_id}</p>
                        )}
                      </>
                    );
                  })()}
                </div>
              )}
            </div>
          </div>
        </div>
      </div>

      <div className="flex justify-end pt-4">
        <button
          type="submit"
          disabled={isSubmitting}
          className="inline-flex items-center gap-2 rounded-lg bg-primary px-8 py-2.5 text-sm font-bold text-primary-foreground disabled:opacity-50 shadow-lg shadow-primary/20 transition-all hover:scale-[1.02]"
        >
          {isSubmitting ? (
            <>
              <Loader2 className="h-4 w-4 animate-spin" /> Guardando...
            </>
          ) : (
            submitLabel
          )}
        </button>
      </div>
    </div>
  );
}
