import { usePage } from '@inertiajs/react';
import { useCallback } from 'react';

/**
 * Hook para verificar permisos y roles del usuario autenticado.
 */
export function usePermissions() {
    const { props } = usePage();
    const auth = props.auth as any;

    const hasPermission = useCallback((permission?: string | string[]) => {
        if (!permission) return true;
        if (!auth?.permissions) return false;
        
        // El administrador tiene todos los permisos implícitamente
        const isAdmin = auth.roles?.some((r: any) => {
            const roleName = typeof r === 'string' ? r : r?.name || '';
            return roleName.toUpperCase() === 'ADMINISTRADOR';
        });

        if (isAdmin) return true;

        if (Array.isArray(permission)) {
            return permission.some(p => auth.permissions.includes(p));
        }

        return auth.permissions.includes(permission);
    }, [auth]);

    const hasRole = useCallback((role: string) => {
        if (!auth?.roles) return false;
        
        return auth.roles.some((r: any) => {
            const roleName = typeof r === 'string' ? r : r?.name || '';
            return roleName.toUpperCase() === role.toUpperCase();
        });
    }, [auth]);

    return {
        auth,
        user: auth?.user,
        can: hasPermission,
        is: hasRole,
        isAdmin: hasRole('ADMINISTRADOR'),
    };
}
