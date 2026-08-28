export interface User {
  id: number;
  name: string;
  last_name: string;
  user_name: string;
  dni: string | null;
  email: string;
  email_verified_at?: string | null;
  foto_perfil?: string | null;
  group_id: number | null;
  subgroup_id: number | null;
  created_at: string;
  updated_at: string;
  roles?: Role[];
  permissions?: string[];
  group?: Group;
  subgroup?: Subgroup;
}

export interface Role {
  id: number;
  name: string;
  label?: string;
  permissions?: Permission[];
  permission_list?: string[]; // Para la vista de edición
}

export interface Permission {
  id: number;
  name: string;
}

export interface Area {
  id: number;
  descripcion: string;
  abreviacion: string | null;
  created_at: string;
  updated_at: string;
  groups_count?: number;
  area_group_types?: AreaGroupType[];
}

export interface AreaGroupType {
  id: number;
  area_id: number;
  group_type_id: number;
  groups?: Group[];
  area?: Area;
}

export interface GroupType {
  id: number;
  descripcion: string;
  abreviacion: string;
  groups_count?: number;
}

export interface Group {
  id: number;
  descripcion: string;
  abreviacion: string | null;
  area_group_type_id: number;
  created_at: string;
  updated_at: string;
  area_id?: number; // Injected by controller
  group_type_id?: number; // Injected by controller
  subgroups?: Subgroup[];
  area_group_type?: AreaGroupType;
  subgroups_count?: number;
  users_count?: number;
}

export interface Subgroup {
  id: number;
  descripcion: string;
  group_id: number;
  parent_subgroup_id: number | null;
  created_at: string;
  updated_at: string;
  subgroups?: Subgroup[];
  subgroups_count?: number;
  users_count?: number;
}

export interface Document {
  id: number;
  n_documento: string;
  asunto: string;
  folios: number;
  root: string | null;
  document_type_id: number;
  user_id: number;
  group_id: number;
  subgroup_id: number | null;
  created_at: string;
  updated_at: string;
  document_type?: DocumentType;
  user?: User;
  group?: Group;
  subgroup?: Subgroup;
  campos?: DocumentCampo[];
  can?: {
    update: boolean;
    delete: boolean;
    view: boolean;
  };
}

export interface DocumentType {
  id: number;
  name: string;
  created_at: string;
  updated_at: string;
  campo_types?: CampoType[];
  groups?: Group[];
  subgroups?: Subgroup[];
  documents_count?: number;
}

export interface CampoType {
  id: number;
  name: string;
  data_type: string;
  required: boolean;
}

export interface DocumentCampo {
  id: number;
  document_id: number;
  campo_type_id: number;
  valor: string;
  campo_type?: CampoType;
}

export interface PaginationData {
  total: number;
  current_page: number;
  last_page: number;
  from: number | null;
  to: number | null;
}

export interface ActivityLog {
  id: number;
  user_id: number | null;
  action: string;
  model: string;
  before: string | null;
  after: string | null;
  created_at: string;
  updated_at: string | null;
  user?: User;
}
