import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\Documents\DocumentarySeriesController::index
 * @see app/Http/Controllers/Documents/DocumentarySeriesController.php:25
 * @route '/series-documentales'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/series-documentales',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Documents\DocumentarySeriesController::index
 * @see app/Http/Controllers/Documents/DocumentarySeriesController.php:25
 * @route '/series-documentales'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Documents\DocumentarySeriesController::index
 * @see app/Http/Controllers/Documents/DocumentarySeriesController.php:25
 * @route '/series-documentales'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Documents\DocumentarySeriesController::index
 * @see app/Http/Controllers/Documents/DocumentarySeriesController.php:25
 * @route '/series-documentales'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Documents\DocumentarySeriesController::store
 * @see app/Http/Controllers/Documents/DocumentarySeriesController.php:50
 * @route '/series-documentales'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/series-documentales',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Documents\DocumentarySeriesController::store
 * @see app/Http/Controllers/Documents/DocumentarySeriesController.php:50
 * @route '/series-documentales'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Documents\DocumentarySeriesController::store
 * @see app/Http/Controllers/Documents/DocumentarySeriesController.php:50
 * @route '/series-documentales'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Documents\DocumentarySeriesController::update
 * @see app/Http/Controllers/Documents/DocumentarySeriesController.php:68
 * @route '/series-documentales/{documentary_series}'
 */
export const update = (args: { documentary_series: string | number } | [documentary_series: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/series-documentales/{documentary_series}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Documents\DocumentarySeriesController::update
 * @see app/Http/Controllers/Documents/DocumentarySeriesController.php:68
 * @route '/series-documentales/{documentary_series}'
 */
update.url = (args: { documentary_series: string | number } | [documentary_series: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { documentary_series: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    documentary_series: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        documentary_series: args.documentary_series,
                }

    return update.definition.url
            .replace('{documentary_series}', parsedArgs.documentary_series.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Documents\DocumentarySeriesController::update
 * @see app/Http/Controllers/Documents/DocumentarySeriesController.php:68
 * @route '/series-documentales/{documentary_series}'
 */
update.put = (args: { documentary_series: string | number } | [documentary_series: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Documents\DocumentarySeriesController::destroy
 * @see app/Http/Controllers/Documents/DocumentarySeriesController.php:86
 * @route '/series-documentales/{documentary_series}'
 */
export const destroy = (args: { documentary_series: string | number } | [documentary_series: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/series-documentales/{documentary_series}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Documents\DocumentarySeriesController::destroy
 * @see app/Http/Controllers/Documents/DocumentarySeriesController.php:86
 * @route '/series-documentales/{documentary_series}'
 */
destroy.url = (args: { documentary_series: string | number } | [documentary_series: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { documentary_series: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    documentary_series: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        documentary_series: args.documentary_series,
                }

    return destroy.definition.url
            .replace('{documentary_series}', parsedArgs.documentary_series.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Documents\DocumentarySeriesController::destroy
 * @see app/Http/Controllers/Documents/DocumentarySeriesController.php:86
 * @route '/series-documentales/{documentary_series}'
 */
destroy.delete = (args: { documentary_series: string | number } | [documentary_series: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})
const documentary_series = {
    index: Object.assign(index, index),
store: Object.assign(store, store),
update: Object.assign(update, update),
destroy: Object.assign(destroy, destroy),
}

export default documentary_series