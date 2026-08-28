import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Users\RoleController::update
 * @see app/Http/Controllers/Users/RoleController.php:90
 * @route '/roles/{role}/permissions'
 */
export const update = (args: { role: string | number | { id: string | number } } | [role: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/roles/{role}/permissions',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Users\RoleController::update
 * @see app/Http/Controllers/Users/RoleController.php:90
 * @route '/roles/{role}/permissions'
 */
update.url = (args: { role: string | number | { id: string | number } } | [role: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { role: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { role: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    role: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        role: typeof args.role === 'object'
                ? args.role.id
                : args.role,
                }

    return update.definition.url
            .replace('{role}', parsedArgs.role.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Users\RoleController::update
 * @see app/Http/Controllers/Users/RoleController.php:90
 * @route '/roles/{role}/permissions'
 */
update.put = (args: { role: string | number | { id: string | number } } | [role: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})
const permissions = {
    update: Object.assign(update, update),
}

export default permissions