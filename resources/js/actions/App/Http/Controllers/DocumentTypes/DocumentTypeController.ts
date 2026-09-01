import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\DocumentTypes\DocumentTypeController::index
 * @see app/Http/Controllers/DocumentTypes/DocumentTypeController.php:26
 * @route '/tipos-documentos'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/tipos-documentos',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DocumentTypes\DocumentTypeController::index
 * @see app/Http/Controllers/DocumentTypes/DocumentTypeController.php:26
 * @route '/tipos-documentos'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DocumentTypes\DocumentTypeController::index
 * @see app/Http/Controllers/DocumentTypes/DocumentTypeController.php:26
 * @route '/tipos-documentos'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\DocumentTypes\DocumentTypeController::index
 * @see app/Http/Controllers/DocumentTypes/DocumentTypeController.php:26
 * @route '/tipos-documentos'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DocumentTypes\DocumentTypeController::store
 * @see app/Http/Controllers/DocumentTypes/DocumentTypeController.php:49
 * @route '/tipos-documentos'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/tipos-documentos',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DocumentTypes\DocumentTypeController::store
 * @see app/Http/Controllers/DocumentTypes/DocumentTypeController.php:49
 * @route '/tipos-documentos'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DocumentTypes\DocumentTypeController::store
 * @see app/Http/Controllers/DocumentTypes/DocumentTypeController.php:49
 * @route '/tipos-documentos'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DocumentTypes\DocumentTypeController::show
 * @see app/Http/Controllers/DocumentTypes/DocumentTypeController.php:63
 * @route '/tipos-documentos/{documentType}'
 */
export const show = (args: { documentType: number | { id: number } } | [documentType: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/tipos-documentos/{documentType}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DocumentTypes\DocumentTypeController::show
 * @see app/Http/Controllers/DocumentTypes/DocumentTypeController.php:63
 * @route '/tipos-documentos/{documentType}'
 */
show.url = (args: { documentType: number | { id: number } } | [documentType: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { documentType: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { documentType: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    documentType: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        documentType: typeof args.documentType === 'object'
                ? args.documentType.id
                : args.documentType,
                }

    return show.definition.url
            .replace('{documentType}', parsedArgs.documentType.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DocumentTypes\DocumentTypeController::show
 * @see app/Http/Controllers/DocumentTypes/DocumentTypeController.php:63
 * @route '/tipos-documentos/{documentType}'
 */
show.get = (args: { documentType: number | { id: number } } | [documentType: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\DocumentTypes\DocumentTypeController::show
 * @see app/Http/Controllers/DocumentTypes/DocumentTypeController.php:63
 * @route '/tipos-documentos/{documentType}'
 */
show.head = (args: { documentType: number | { id: number } } | [documentType: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DocumentTypes\DocumentTypeController::update
 * @see app/Http/Controllers/DocumentTypes/DocumentTypeController.php:75
 * @route '/tipos-documentos/{documentType}'
 */
export const update = (args: { documentType: number | { id: number } } | [documentType: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/tipos-documentos/{documentType}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\DocumentTypes\DocumentTypeController::update
 * @see app/Http/Controllers/DocumentTypes/DocumentTypeController.php:75
 * @route '/tipos-documentos/{documentType}'
 */
update.url = (args: { documentType: number | { id: number } } | [documentType: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { documentType: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { documentType: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    documentType: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        documentType: typeof args.documentType === 'object'
                ? args.documentType.id
                : args.documentType,
                }

    return update.definition.url
            .replace('{documentType}', parsedArgs.documentType.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DocumentTypes\DocumentTypeController::update
 * @see app/Http/Controllers/DocumentTypes/DocumentTypeController.php:75
 * @route '/tipos-documentos/{documentType}'
 */
update.put = (args: { documentType: number | { id: number } } | [documentType: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\DocumentTypes\DocumentTypeController::destroy
 * @see app/Http/Controllers/DocumentTypes/DocumentTypeController.php:89
 * @route '/tipos-documentos/{documentType}'
 */
export const destroy = (args: { documentType: number | { id: number } } | [documentType: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/tipos-documentos/{documentType}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\DocumentTypes\DocumentTypeController::destroy
 * @see app/Http/Controllers/DocumentTypes/DocumentTypeController.php:89
 * @route '/tipos-documentos/{documentType}'
 */
destroy.url = (args: { documentType: number | { id: number } } | [documentType: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { documentType: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { documentType: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    documentType: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        documentType: typeof args.documentType === 'object'
                ? args.documentType.id
                : args.documentType,
                }

    return destroy.definition.url
            .replace('{documentType}', parsedArgs.documentType.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DocumentTypes\DocumentTypeController::destroy
 * @see app/Http/Controllers/DocumentTypes/DocumentTypeController.php:89
 * @route '/tipos-documentos/{documentType}'
 */
destroy.delete = (args: { documentType: number | { id: number } } | [documentType: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})
const DocumentTypeController = { index, store, show, update, destroy }

export default DocumentTypeController