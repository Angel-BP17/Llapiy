import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../wayfinder'
import permissions from './permissions'
/**
* @see \App\Http\Controllers\Users\RoleController::index
 * @see app/Http/Controllers/Users/RoleController.php:26
 * @route '/roles'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/roles',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Users\RoleController::index
 * @see app/Http/Controllers/Users/RoleController.php:26
 * @route '/roles'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Users\RoleController::index
 * @see app/Http/Controllers/Users/RoleController.php:26
 * @route '/roles'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Users\RoleController::index
 * @see app/Http/Controllers/Users/RoleController.php:26
 * @route '/roles'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Users\RoleController::store
 * @see app/Http/Controllers/Users/RoleController.php:53
 * @route '/roles'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/roles',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Users\RoleController::store
 * @see app/Http/Controllers/Users/RoleController.php:53
 * @route '/roles'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Users\RoleController::store
 * @see app/Http/Controllers/Users/RoleController.php:53
 * @route '/roles'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Users\RoleController::update
 * @see app/Http/Controllers/Users/RoleController.php:66
 * @route '/roles/{role}'
 */
export const update = (args: { role: string | number | { id: string | number } } | [role: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/roles/{role}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Users\RoleController::update
 * @see app/Http/Controllers/Users/RoleController.php:66
 * @route '/roles/{role}'
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
 * @see app/Http/Controllers/Users/RoleController.php:66
 * @route '/roles/{role}'
 */
update.put = (args: { role: string | number | { id: string | number } } | [role: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Users\RoleController::destroy
 * @see app/Http/Controllers/Users/RoleController.php:80
 * @route '/roles/{role}'
 */
export const destroy = (args: { role: string | number | { id: string | number } } | [role: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/roles/{role}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Users\RoleController::destroy
 * @see app/Http/Controllers/Users/RoleController.php:80
 * @route '/roles/{role}'
 */
destroy.url = (args: { role: string | number | { id: string | number } } | [role: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
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

    return destroy.definition.url
            .replace('{role}', parsedArgs.role.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Users\RoleController::destroy
 * @see app/Http/Controllers/Users/RoleController.php:80
 * @route '/roles/{role}'
 */
destroy.delete = (args: { role: string | number | { id: string | number } } | [role: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})
const roles = {
    index: Object.assign(index, index),
store: Object.assign(store, store),
update: Object.assign(update, update),
destroy: Object.assign(destroy, destroy),
permissions: Object.assign(permissions, permissions),
}

export default roles