import { usePage } from '@inertiajs/react';

export function useAuth() {
    const { auth } = usePage().props;

    const roleNames = (auth.user?.roles ?? []).map((role) => role.name);

    const hasRole = (role: string): boolean => roleNames.includes(role);

    const hasAnyRole = (roles: string[]): boolean => roles.some((role) => roleNames.includes(role));

    return {
        user: auth.user,
        roles: roleNames,
        hasRole,
        hasAnyRole,
    };
}
