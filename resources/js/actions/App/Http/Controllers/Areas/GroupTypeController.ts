import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Areas\GroupTypeController::index
 * @see app/Http/Controllers/Areas/GroupTypeController.php:21
 * @route '/tipos-grupos'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/tipos-grupos',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Areas\GroupTypeController::index
 * @see app/Http/Controllers/Areas/GroupTypeController.php:21
 * @route '/tipos-grupos'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Areas\GroupTypeController::index
 * @see app/Http/Controllers/Areas/GroupTypeController.php:21
 * @route '/tipos-grupos'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Areas\GroupTypeController::index
 * @see app/Http/Controllers/Areas/GroupTypeController.php:21
 * @route '/tipos-grupos'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Areas\GroupTypeController::store
 * @see app/Http/Controllers/Areas/GroupTypeController.php:34
 * @route '/tipos-grupos'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/tipos-grupos',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Areas\GroupTypeController::store
 * @see app/Http/Controllers/Areas/GroupTypeController.php:34
 * @route '/tipos-grupos'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Areas\GroupTypeController::store
 * @see app/Http/Controllers/Areas/GroupTypeController.php:34
 * @route '/tipos-grupos'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Areas\GroupTypeController::update
 * @see app/Http/Controllers/Areas/GroupTypeController.php:44
 * @route '/tipos-grupos/{id}'
 */
export const update = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/tipos-grupos/{id}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Areas\GroupTypeController::update
 * @see app/Http/Controllers/Areas/GroupTypeController.php:44
 * @route '/tipos-grupos/{id}'
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
* @see \App\Http\Controllers\Areas\GroupTypeController::update
 * @see app/Http/Controllers/Areas/GroupTypeController.php:44
 * @route '/tipos-grupos/{id}'
 */
update.put = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Areas\GroupTypeController::destroy
 * @see app/Http/Controllers/Areas/GroupTypeController.php:55
 * @route '/tipos-grupos/{id}'
 */
export const destroy = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/tipos-grupos/{id}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Areas\GroupTypeController::destroy
 * @see app/Http/Controllers/Areas/GroupTypeController.php:55
 * @route '/tipos-grupos/{id}'
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
* @see \App\Http\Controllers\Areas\GroupTypeController::destroy
 * @see app/Http/Controllers/Areas/GroupTypeController.php:55
 * @route '/tipos-grupos/{id}'
 */
destroy.delete = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})
const GroupTypeController = { index, store, update, destroy }

export default GroupTypeController