import { Eye, Pencil, Trash2, Users } from "lucide-react";
import { User } from "@/types/models";

export type UserRecord = User;

type UserTableProps = {
    users: UserRecord[];
    isLoading?: boolean;
    onView: (user: UserRecord) => void;
    onEdit: (user: UserRecord) => void;
    onDelete: (user: UserRecord) => void;
};

export const defaultAvatar = "/img/default-avatar.png";

export function UserTable({
    users,
    isLoading,
    onView,
    onEdit,
    onDelete,
}: UserTableProps) {
    return (
        <div className="overflow-hidden rounded-xl border border-border bg-card shadow-sm">
            <div className="overflow-x-auto">
                <table className="w-full min-w-[820px] text-sm">
                    <thead className="bg-muted/60">
                        <tr className="text-left text-xs uppercase tracking-wide text-muted-foreground">
                            <th className="px-4 py-3">ID</th>
                            <th className="px-4 py-3">Nombre</th>
                            <th className="px-4 py-3">Apellido</th>
                            <th className="px-4 py-3">Foto</th>
                            <th className="px-4 py-3">Correo</th>
                            <th className="px-4 py-3 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        {isLoading ? (
                            Array.from({ length: 6 }).map((_, index) => (
                                <tr
                                    key={`users-loading-${index}`}
                                    className="border-t border-border"
                                >
                                    <td colSpan={6} className="px-4 py-3">
                                        <div className="h-8 animate-pulse rounded bg-muted/70" />
                                    </td>
                                </tr>
                            ))
                        ) : users.length ? (
                            users.map((user) => (
                                <tr
                                    key={user.id}
                                    className="border-t border-border"
                                >
                                    <td className="px-4 py-3 font-semibold text-foreground">
                                        {user.id}
                                    </td>
                                    <td className="px-4 py-3">{user.name}</td>
                                    <td className="px-4 py-3">
                                        {user.last_name}
                                    </td>
                                    <td className="px-4 py-3">
                                        <img
                                            src={
                                                user.foto_perfil ||
                                                defaultAvatar
                                            }
                                            alt={`Foto de ${user.name}`}
                                            className="h-9 w-9 rounded-full border border-border object-cover"
                                        />
                                    </td>
                                    <td className="px-4 py-3">{user.email}</td>
                                    <td className="px-4 py-3">
                                        <div className="flex justify-end gap-2">
                                            <button
                                                type="button"
                                                onClick={() => onView(user)}
                                                className="inline-flex items-center gap-1.5 rounded-md bg-sky-600 px-3 py-1.5 text-xs font-semibold text-white"
                                            >
                                                <Eye className="h-3.5 w-3.5" />
                                                Ver
                                            </button>
                                            <button
                                                type="button"
                                                onClick={() => onEdit(user)}
                                                className="inline-flex items-center gap-1.5 rounded-md bg-amber-500 px-3 py-1.5 text-xs font-semibold text-white"
                                            >
                                                <Pencil className="h-3.5 w-3.5" />
                                                Editar
                                            </button>
                                            <button
                                                type="button"
                                                onClick={() => onDelete(user)}
                                                className="inline-flex items-center gap-1.5 rounded-md bg-red-600 px-3 py-1.5 text-xs font-semibold text-white"
                                            >
                                                <Trash2 className="h-3.5 w-3.5" />
                                                Eliminar
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            ))
                        ) : (
                            <tr>
                                <td colSpan={6} className="px-4 py-16 text-center">
                                    <div className="flex flex-col items-center justify-center max-w-md mx-auto">
                                        <div className="flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-500/10 text-indigo-600 mb-4 animate-bounce">
                                            <Users className="h-8 w-8" />
                                        </div>
                                        <h3 className="text-lg font-semibold text-foreground">No se encontraron usuarios</h3>
                                        <p className="mt-1 text-sm text-muted-foreground">
                                            No hay registros de usuarios en la base de datos o ningún registro coincide con la búsqueda.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        )}
                    </tbody>
                </table>
            </div>
        </div>
    );
}
