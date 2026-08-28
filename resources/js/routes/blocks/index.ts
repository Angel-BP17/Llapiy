import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\Documents\BlockController::index
 * @see app/Http/Controllers/Documents/BlockController.php:36
 * @route '/bloques'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/bloques',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Documents\BlockController::index
 * @see app/Http/Controllers/Documents/BlockController.php:36
 * @route '/bloques'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Documents\BlockController::index
 * @see app/Http/Controllers/Documents/BlockController.php:36
 * @route '/bloques'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Documents\BlockController::index
 * @see app/Http/Controllers/Documents/BlockController.php:36
 * @route '/bloques'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Documents\BlockController::store
 * @see app/Http/Controllers/Documents/BlockController.php:105
 * @route '/bloques'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/bloques',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Documents\BlockController::store
 * @see app/Http/Controllers/Documents/BlockController.php:105
 * @route '/bloques'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Documents\BlockController::store
 * @see app/Http/Controllers/Documents/BlockController.php:105
 * @route '/bloques'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Documents\BlockController::pdf
 * @see app/Http/Controllers/Documents/BlockController.php:211
 * @route '/bloques/pdf'
 */
export const pdf = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: pdf.url(options),
    method: 'get',
})

pdf.definition = {
    methods: ["get","head"],
    url: '/bloques/pdf',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Documents\BlockController::pdf
 * @see app/Http/Controllers/Documents/BlockController.php:211
 * @route '/bloques/pdf'
 */
pdf.url = (options?: RouteQueryOptions) => {
    return pdf.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Documents\BlockController::pdf
 * @see app/Http/Controllers/Documents/BlockController.php:211
 * @route '/bloques/pdf'
 */
pdf.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: pdf.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Documents\BlockController::pdf
 * @see app/Http/Controllers/Documents/BlockController.php:211
 * @route '/bloques/pdf'
 */
pdf.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: pdf.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Documents\BlockController::show
 * @see app/Http/Controllers/Documents/BlockController.php:128
 * @route '/bloques/{block}'
 */
export const show = (args: { block: string | number | { id: string | number } } | [block: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/bloques/{block}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Documents\BlockController::show
 * @see app/Http/Controllers/Documents/BlockController.php:128
 * @route '/bloques/{block}'
 */
show.url = (args: { block: string | number | { id: string | number } } | [block: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { block: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { block: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    block: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        block: typeof args.block === 'object'
                ? args.block.id
                : args.block,
                }

    return show.definition.url
            .replace('{block}', parsedArgs.block.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Documents\BlockController::show
 * @see app/Http/Controllers/Documents/BlockController.php:128
 * @route '/bloques/{block}'
 */
show.get = (args: { block: string | number | { id: string | number } } | [block: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Documents\BlockController::show
 * @see app/Http/Controllers/Documents/BlockController.php:128
 * @route '/bloques/{block}'
 */
show.head = (args: { block: string | number | { id: string | number } } | [block: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Documents\BlockController::update
 * @see app/Http/Controllers/Documents/BlockController.php:147
 * @route '/bloques/{block}'
 */
export const update = (args: { block: string | number | { id: string | number } } | [block: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/bloques/{block}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Documents\BlockController::update
 * @see app/Http/Controllers/Documents/BlockController.php:147
 * @route '/bloques/{block}'
 */
update.url = (args: { block: string | number | { id: string | number } } | [block: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { block: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { block: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    block: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        block: typeof args.block === 'object'
                ? args.block.id
                : args.block,
                }

    return update.definition.url
            .replace('{block}', parsedArgs.block.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Documents\BlockController::update
 * @see app/Http/Controllers/Documents/BlockController.php:147
 * @route '/bloques/{block}'
 */
update.put = (args: { block: string | number | { id: string | number } } | [block: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Documents\BlockController::destroy
 * @see app/Http/Controllers/Documents/BlockController.php:163
 * @route '/bloques/{block}'
 */
export const destroy = (args: { block: string | number | { id: string | number } } | [block: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/bloques/{block}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Documents\BlockController::destroy
 * @see app/Http/Controllers/Documents/BlockController.php:163
 * @route '/bloques/{block}'
 */
destroy.url = (args: { block: string | number | { id: string | number } } | [block: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { block: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { block: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    block: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        block: typeof args.block === 'object'
                ? args.block.id
                : args.block,
                }

    return destroy.definition.url
            .replace('{block}', parsedArgs.block.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Documents\BlockController::destroy
 * @see app/Http/Controllers/Documents/BlockController.php:163
 * @route '/bloques/{block}'
 */
destroy.delete = (args: { block: string | number | { id: string | number } } | [block: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Documents\BlockController::upload
 * @see app/Http/Controllers/Documents/BlockController.php:195
 * @route '/bloques/{block}/upload'
 */
export const upload = (args: { block: string | number | { id: string | number } } | [block: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: upload.url(args, options),
    method: 'put',
})

upload.definition = {
    methods: ["put"],
    url: '/bloques/{block}/upload',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Documents\BlockController::upload
 * @see app/Http/Controllers/Documents/BlockController.php:195
 * @route '/bloques/{block}/upload'
 */
upload.url = (args: { block: string | number | { id: string | number } } | [block: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { block: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { block: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    block: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        block: typeof args.block === 'object'
                ? args.block.id
                : args.block,
                }

    return upload.definition.url
            .replace('{block}', parsedArgs.block.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Documents\BlockController::upload
 * @see app/Http/Controllers/Documents/BlockController.php:195
 * @route '/bloques/{block}/upload'
 */
upload.put = (args: { block: string | number | { id: string | number } } | [block: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: upload.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Documents\BlockController::file
 * @see app/Http/Controllers/Documents/BlockController.php:179
 * @route '/bloques/{block}/file'
 */
export const file = (args: { block: string | number | { id: string | number } } | [block: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: file.url(args, options),
    method: 'get',
})

file.definition = {
    methods: ["get","head"],
    url: '/bloques/{block}/file',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Documents\BlockController::file
 * @see app/Http/Controllers/Documents/BlockController.php:179
 * @route '/bloques/{block}/file'
 */
file.url = (args: { block: string | number | { id: string | number } } | [block: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { block: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { block: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    block: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        block: typeof args.block === 'object'
                ? args.block.id
                : args.block,
                }

    return file.definition.url
            .replace('{block}', parsedArgs.block.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Documents\BlockController::file
 * @see app/Http/Controllers/Documents/BlockController.php:179
 * @route '/bloques/{block}/file'
 */
file.get = (args: { block: string | number | { id: string | number } } | [block: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: file.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Documents\BlockController::file
 * @see app/Http/Controllers/Documents/BlockController.php:179
 * @route '/bloques/{block}/file'
 */
file.head = (args: { block: string | number | { id: string | number } } | [block: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: file.url(args, options),
    method: 'head',
})
const blocks = {
    index: Object.assign(index, index),
store: Object.assign(store, store),
pdf: Object.assign(pdf, pdf),
show: Object.assign(show, show),
update: Object.assign(update, update),
destroy: Object.assign(destroy, destroy),
upload: Object.assign(upload, upload),
file: Object.assign(file, file),
}

export default blocks