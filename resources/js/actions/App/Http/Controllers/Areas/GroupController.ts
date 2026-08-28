import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Areas\GroupController::store
 * @see app/Http/Controllers/Areas/GroupController.php:19
 * @route '/groups'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/groups',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Areas\GroupController::store
 * @see app/Http/Controllers/Areas/GroupController.php:19
 * @route '/groups'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Areas\GroupController::store
 * @see app/Http/Controllers/Areas/GroupController.php:19
 * @route '/groups'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Areas\GroupController::update
 * @see app/Http/Controllers/Areas/GroupController.php:29
 * @route '/groups/{id}'
 */
export const update = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/groups/{id}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Areas\GroupController::update
 * @see app/Http/Controllers/Areas/GroupController.php:29
 * @route '/groups/{id}'
 */
update.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    id: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        id: args.id,
                }

    return update.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Areas\GroupController::update
 * @see app/Http/Controllers/Areas/GroupController.php:29
 * @route '/groups/{id}'
 */
update.put = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Areas\GroupController::destroy
 * @see app/Http/Controllers/Areas/GroupController.php:40
 * @route '/groups/{id}'
 */
export const destroy = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/groups/{id}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Areas\GroupController::destroy
 * @see app/Http/Controllers/Areas/GroupController.php:40
 * @route '/groups/{id}'
 */
destroy.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    id: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        id: args.id,
                }

    return destroy.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Areas\GroupController::destroy
 * @see app/Http/Controllers/Areas/GroupController.php:40
 * @route '/groups/{id}'
 */
destroy.delete = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})
const GroupController = { store, update, destroy }

export default GroupController