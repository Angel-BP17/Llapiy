import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\DocumentTypes\CampoController::index
 * @see app/Http/Controllers/DocumentTypes/CampoController.php:25
 * @route '/campos'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/campos',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DocumentTypes\CampoController::index
 * @see app/Http/Controllers/DocumentTypes/CampoController.php:25
 * @route '/campos'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DocumentTypes\CampoController::index
 * @see app/Http/Controllers/DocumentTypes/CampoController.php:25
 * @route '/campos'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\DocumentTypes\CampoController::index
 * @see app/Http/Controllers/DocumentTypes/CampoController.php:25
 * @route '/campos'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DocumentTypes\CampoController::store
 * @see app/Http/Controllers/DocumentTypes/CampoController.php:41
 * @route '/campos'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/campos',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DocumentTypes\CampoController::store
 * @see app/Http/Controllers/DocumentTypes/CampoController.php:41
 * @route '/campos'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DocumentTypes\CampoController::store
 * @see app/Http/Controllers/DocumentTypes/CampoController.php:41
 * @route '/campos'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DocumentTypes\CampoController::show
 * @see app/Http/Controllers/DocumentTypes/CampoController.php:51
 * @route '/campos/{campo}'
 */
export const show = (args: { campo: string | number | { id: string | number } } | [campo: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/campos/{campo}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DocumentTypes\CampoController::show
 * @see app/Http/Controllers/DocumentTypes/CampoController.php:51
 * @route '/campos/{campo}'
 */
show.url = (args: { campo: string | number | { id: string | number } } | [campo: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { campo: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { campo: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    campo: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        campo: typeof args.campo === 'object'
                ? args.campo.id
                : args.campo,
                }

    return show.definition.url
            .replace('{campo}', parsedArgs.campo.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DocumentTypes\CampoController::show
 * @see app/Http/Controllers/DocumentTypes/CampoController.php:51
 * @route '/campos/{campo}'
 */
show.get = (args: { campo: string | number | { id: string | number } } | [campo: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\DocumentTypes\CampoController::show
 * @see app/Http/Controllers/DocumentTypes/CampoController.php:51
 * @route '/campos/{campo}'
 */
show.head = (args: { campo: string | number | { id: string | number } } | [campo: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DocumentTypes\CampoController::update
 * @see app/Http/Controllers/DocumentTypes/CampoController.php:61
 * @route '/campos/{campo}'
 */
export const update = (args: { campo: string | number | { id: string | number } } | [campo: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/campos/{campo}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\DocumentTypes\CampoController::update
 * @see app/Http/Controllers/DocumentTypes/CampoController.php:61
 * @route '/campos/{campo}'
 */
update.url = (args: { campo: string | number | { id: string | number } } | [campo: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { campo: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { campo: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    campo: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        campo: typeof args.campo === 'object'
                ? args.campo.id
                : args.campo,
                }

    return update.definition.url
            .replace('{campo}', parsedArgs.campo.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DocumentTypes\CampoController::update
 * @see app/Http/Controllers/DocumentTypes/CampoController.php:61
 * @route '/campos/{campo}'
 */
update.put = (args: { campo: string | number | { id: string | number } } | [campo: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\DocumentTypes\CampoController::destroy
 * @see app/Http/Controllers/DocumentTypes/CampoController.php:71
 * @route '/campos/{campo}'
 */
export const destroy = (args: { campo: string | number | { id: string | number } } | [campo: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/campos/{campo}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\DocumentTypes\CampoController::destroy
 * @see app/Http/Controllers/DocumentTypes/CampoController.php:71
 * @route '/campos/{campo}'
 */
destroy.url = (args: { campo: string | number | { id: string | number } } | [campo: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { campo: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { campo: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    campo: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        campo: typeof args.campo === 'object'
                ? args.campo.id
                : args.campo,
                }

    return destroy.definition.url
            .replace('{campo}', parsedArgs.campo.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DocumentTypes\CampoController::destroy
 * @see app/Http/Controllers/DocumentTypes/CampoController.php:71
 * @route '/campos/{campo}'
 */
destroy.delete = (args: { campo: string | number | { id: string | number } } | [campo: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})
const CampoController = { index, store, show, update, destroy }

export default CampoController