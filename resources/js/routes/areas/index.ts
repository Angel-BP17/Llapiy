import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\Areas\AreaController::index
 * @see app/Http/Controllers/Areas/AreaController.php:26
 * @route '/areas'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/areas',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Areas\AreaController::index
 * @see app/Http/Controllers/Areas/AreaController.php:26
 * @route '/areas'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Areas\AreaController::index
 * @see app/Http/Controllers/Areas/AreaController.php:26
 * @route '/areas'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Areas\AreaController::index
 * @see app/Http/Controllers/Areas/AreaController.php:26
 * @route '/areas'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Areas\AreaController::store
 * @see app/Http/Controllers/Areas/AreaController.php:55
 * @route '/areas'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/areas',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Areas\AreaController::store
 * @see app/Http/Controllers/Areas/AreaController.php:55
 * @route '/areas'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Areas\AreaController::store
 * @see app/Http/Controllers/Areas/AreaController.php:55
 * @route '/areas'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Areas\AreaController::show
 * @see app/Http/Controllers/Areas/AreaController.php:65
 * @route '/areas/{area}'
 */
export const show = (args: { area: number | { id: number } } | [area: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/areas/{area}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Areas\AreaController::show
 * @see app/Http/Controllers/Areas/AreaController.php:65
 * @route '/areas/{area}'
 */
show.url = (args: { area: number | { id: number } } | [area: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { area: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { area: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    area: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        area: typeof args.area === 'object'
                ? args.area.id
                : args.area,
                }

    return show.definition.url
            .replace('{area}', parsedArgs.area.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Areas\AreaController::show
 * @see app/Http/Controllers/Areas/AreaController.php:65
 * @route '/areas/{area}'
 */
show.get = (args: { area: number | { id: number } } | [area: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Areas\AreaController::show
 * @see app/Http/Controllers/Areas/AreaController.php:65
 * @route '/areas/{area}'
 */
show.head = (args: { area: number | { id: number } } | [area: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Areas\AreaController::update
 * @see app/Http/Controllers/Areas/AreaController.php:75
 * @route '/areas/{area}'
 */
export const update = (args: { area: number | { id: number } } | [area: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/areas/{area}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Areas\AreaController::update
 * @see app/Http/Controllers/Areas/AreaController.php:75
 * @route '/areas/{area}'
 */
update.url = (args: { area: number | { id: number } } | [area: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { area: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { area: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    area: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        area: typeof args.area === 'object'
                ? args.area.id
                : args.area,
                }

    return update.definition.url
            .replace('{area}', parsedArgs.area.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Areas\AreaController::update
 * @see app/Http/Controllers/Areas/AreaController.php:75
 * @route '/areas/{area}'
 */
update.put = (args: { area: number | { id: number } } | [area: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Areas\AreaController::destroy
 * @see app/Http/Controllers/Areas/AreaController.php:85
 * @route '/areas/{area}'
 */
export const destroy = (args: { area: number | { id: number } } | [area: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/areas/{area}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Areas\AreaController::destroy
 * @see app/Http/Controllers/Areas/AreaController.php:85
 * @route '/areas/{area}'
 */
destroy.url = (args: { area: number | { id: number } } | [area: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { area: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { area: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    area: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        area: typeof args.area === 'object'
                ? args.area.id
                : args.area,
                }

    return destroy.definition.url
            .replace('{area}', parsedArgs.area.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Areas\AreaController::destroy
 * @see app/Http/Controllers/Areas/AreaController.php:85
 * @route '/areas/{area}'
 */
destroy.delete = (args: { area: number | { id: number } } | [area: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})
const areas = {
    index: Object.assign(index, index),
store: Object.assign(store, store),
show: Object.assign(show, show),
update: Object.assign(update, update),
destroy: Object.assign(destroy, destroy),
}

export default areas