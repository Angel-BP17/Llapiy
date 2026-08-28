import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Documents\DocumentController::index
 * @see app/Http/Controllers/Documents/DocumentController.php:32
 * @route '/documentos'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/documentos',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Documents\DocumentController::index
 * @see app/Http/Controllers/Documents/DocumentController.php:32
 * @route '/documentos'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Documents\DocumentController::index
 * @see app/Http/Controllers/Documents/DocumentController.php:32
 * @route '/documentos'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Documents\DocumentController::index
 * @see app/Http/Controllers/Documents/DocumentController.php:32
 * @route '/documentos'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Documents\DocumentController::store
 * @see app/Http/Controllers/Documents/DocumentController.php:88
 * @route '/documentos'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/documentos',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Documents\DocumentController::store
 * @see app/Http/Controllers/Documents/DocumentController.php:88
 * @route '/documentos'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Documents\DocumentController::store
 * @see app/Http/Controllers/Documents/DocumentController.php:88
 * @route '/documentos'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Documents\DocumentController::pdf
 * @see app/Http/Controllers/Documents/DocumentController.php:185
 * @route '/documentos/pdf'
 */
export const pdf = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: pdf.url(options),
    method: 'get',
})

pdf.definition = {
    methods: ["get","head"],
    url: '/documentos/pdf',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Documents\DocumentController::pdf
 * @see app/Http/Controllers/Documents/DocumentController.php:185
 * @route '/documentos/pdf'
 */
pdf.url = (options?: RouteQueryOptions) => {
    return pdf.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Documents\DocumentController::pdf
 * @see app/Http/Controllers/Documents/DocumentController.php:185
 * @route '/documentos/pdf'
 */
pdf.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: pdf.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Documents\DocumentController::pdf
 * @see app/Http/Controllers/Documents/DocumentController.php:185
 * @route '/documentos/pdf'
 */
pdf.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: pdf.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Documents\DocumentController::show
 * @see app/Http/Controllers/Documents/DocumentController.php:102
 * @route '/documentos/{document}'
 */
export const show = (args: { document: string | number | { id: string | number } } | [document: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/documentos/{document}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Documents\DocumentController::show
 * @see app/Http/Controllers/Documents/DocumentController.php:102
 * @route '/documentos/{document}'
 */
show.url = (args: { document: string | number | { id: string | number } } | [document: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { document: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { document: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    document: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        document: typeof args.document === 'object'
                ? args.document.id
                : args.document,
                }

    return show.definition.url
            .replace('{document}', parsedArgs.document.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Documents\DocumentController::show
 * @see app/Http/Controllers/Documents/DocumentController.php:102
 * @route '/documentos/{document}'
 */
show.get = (args: { document: string | number | { id: string | number } } | [document: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Documents\DocumentController::show
 * @see app/Http/Controllers/Documents/DocumentController.php:102
 * @route '/documentos/{document}'
 */
show.head = (args: { document: string | number | { id: string | number } } | [document: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Documents\DocumentController::update
 * @see app/Http/Controllers/Documents/DocumentController.php:120
 * @route '/documentos/{document}'
 */
export const update = (args: { document: string | number | { id: string | number } } | [document: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/documentos/{document}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Documents\DocumentController::update
 * @see app/Http/Controllers/Documents/DocumentController.php:120
 * @route '/documentos/{document}'
 */
update.url = (args: { document: string | number | { id: string | number } } | [document: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { document: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { document: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    document: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        document: typeof args.document === 'object'
                ? args.document.id
                : args.document,
                }

    return update.definition.url
            .replace('{document}', parsedArgs.document.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Documents\DocumentController::update
 * @see app/Http/Controllers/Documents/DocumentController.php:120
 * @route '/documentos/{document}'
 */
update.put = (args: { document: string | number | { id: string | number } } | [document: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Documents\DocumentController::destroy
 * @see app/Http/Controllers/Documents/DocumentController.php:142
 * @route '/documentos/{document}'
 */
export const destroy = (args: { document: string | number | { id: string | number } } | [document: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/documentos/{document}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Documents\DocumentController::destroy
 * @see app/Http/Controllers/Documents/DocumentController.php:142
 * @route '/documentos/{document}'
 */
destroy.url = (args: { document: string | number | { id: string | number } } | [document: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { document: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { document: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    document: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        document: typeof args.document === 'object'
                ? args.document.id
                : args.document,
                }

    return destroy.definition.url
            .replace('{document}', parsedArgs.document.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Documents\DocumentController::destroy
 * @see app/Http/Controllers/Documents/DocumentController.php:142
 * @route '/documentos/{document}'
 */
destroy.delete = (args: { document: string | number | { id: string | number } } | [document: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Documents\DocumentController::file
 * @see app/Http/Controllers/Documents/DocumentController.php:158
 * @route '/documentos/{document}/file'
 */
export const file = (args: { document: string | number | { id: string | number } } | [document: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: file.url(args, options),
    method: 'get',
})

file.definition = {
    methods: ["get","head"],
    url: '/documentos/{document}/file',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Documents\DocumentController::file
 * @see app/Http/Controllers/Documents/DocumentController.php:158
 * @route '/documentos/{document}/file'
 */
file.url = (args: { document: string | number | { id: string | number } } | [document: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { document: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { document: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    document: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        document: typeof args.document === 'object'
                ? args.document.id
                : args.document,
                }

    return file.definition.url
            .replace('{document}', parsedArgs.document.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Documents\DocumentController::file
 * @see app/Http/Controllers/Documents/DocumentController.php:158
 * @route '/documentos/{document}/file'
 */
file.get = (args: { document: string | number | { id: string | number } } | [document: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: file.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Documents\DocumentController::file
 * @see app/Http/Controllers/Documents/DocumentController.php:158
 * @route '/documentos/{document}/file'
 */
file.head = (args: { document: string | number | { id: string | number } } | [document: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: file.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Documents\DocumentController::upload
 * @see app/Http/Controllers/Documents/DocumentController.php:171
 * @route '/documentos/{document}/upload'
 */
export const upload = (args: { document: string | number | { id: string | number } } | [document: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: upload.url(args, options),
    method: 'put',
})

upload.definition = {
    methods: ["put"],
    url: '/documentos/{document}/upload',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Documents\DocumentController::upload
 * @see app/Http/Controllers/Documents/DocumentController.php:171
 * @route '/documentos/{document}/upload'
 */
upload.url = (args: { document: string | number | { id: string | number } } | [document: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { document: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { document: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    document: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        document: typeof args.document === 'object'
                ? args.document.id
                : args.document,
                }

    return upload.definition.url
            .replace('{document}', parsedArgs.document.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Documents\DocumentController::upload
 * @see app/Http/Controllers/Documents/DocumentController.php:171
 * @route '/documentos/{document}/upload'
 */
upload.put = (args: { document: string | number | { id: string | number } } | [document: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: upload.url(args, options),
    method: 'put',
})
const DocumentController = { index, store, pdf, show, update, destroy, file, upload }

export default DocumentController