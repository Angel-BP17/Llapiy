import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Storage\BoxController::index
 * @see app/Http/Controllers/Storage/BoxController.php:25
 * @route '/sections/{section}/andamios/{andamio}/boxes'
 */
export const index = (args: { section: number | { id: number }, andamio: number | { id: number } } | [section: number | { id: number }, andamio: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/sections/{section}/andamios/{andamio}/boxes',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Storage\BoxController::index
 * @see app/Http/Controllers/Storage/BoxController.php:25
 * @route '/sections/{section}/andamios/{andamio}/boxes'
 */
index.url = (args: { section: number | { id: number }, andamio: number | { id: number } } | [section: number | { id: number }, andamio: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
                    section: args[0],
                    andamio: args[1],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        section: typeof args.section === 'object'
                ? args.section.id
                : args.section,
                                andamio: typeof args.andamio === 'object'
                ? args.andamio.id
                : args.andamio,
                }

    return index.definition.url
            .replace('{section}', parsedArgs.section.toString())
            .replace('{andamio}', parsedArgs.andamio.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Storage\BoxController::index
 * @see app/Http/Controllers/Storage/BoxController.php:25
 * @route '/sections/{section}/andamios/{andamio}/boxes'
 */
index.get = (args: { section: number | { id: number }, andamio: number | { id: number } } | [section: number | { id: number }, andamio: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Storage\BoxController::index
 * @see app/Http/Controllers/Storage/BoxController.php:25
 * @route '/sections/{section}/andamios/{andamio}/boxes'
 */
index.head = (args: { section: number | { id: number }, andamio: number | { id: number } } | [section: number | { id: number }, andamio: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Storage\BoxController::store
 * @see app/Http/Controllers/Storage/BoxController.php:78
 * @route '/sections/{section}/andamios/{andamio}/boxes'
 */
export const store = (args: { section: number | { id: number }, andamio: number | { id: number } } | [section: number | { id: number }, andamio: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/sections/{section}/andamios/{andamio}/boxes',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Storage\BoxController::store
 * @see app/Http/Controllers/Storage/BoxController.php:78
 * @route '/sections/{section}/andamios/{andamio}/boxes'
 */
store.url = (args: { section: number | { id: number }, andamio: number | { id: number } } | [section: number | { id: number }, andamio: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
                    section: args[0],
                    andamio: args[1],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        section: typeof args.section === 'object'
                ? args.section.id
                : args.section,
                                andamio: typeof args.andamio === 'object'
                ? args.andamio.id
                : args.andamio,
                }

    return store.definition.url
            .replace('{section}', parsedArgs.section.toString())
            .replace('{andamio}', parsedArgs.andamio.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Storage\BoxController::store
 * @see app/Http/Controllers/Storage/BoxController.php:78
 * @route '/sections/{section}/andamios/{andamio}/boxes'
 */
store.post = (args: { section: number | { id: number }, andamio: number | { id: number } } | [section: number | { id: number }, andamio: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Storage\BoxController::update
 * @see app/Http/Controllers/Storage/BoxController.php:92
 * @route '/sections/{section}/andamios/{andamio}/boxes/{box}'
 */
export const update = (args: { section: number | { id: number }, andamio: number | { id: number }, box: number | { id: number } } | [section: number | { id: number }, andamio: number | { id: number }, box: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/sections/{section}/andamios/{andamio}/boxes/{box}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Storage\BoxController::update
 * @see app/Http/Controllers/Storage/BoxController.php:92
 * @route '/sections/{section}/andamios/{andamio}/boxes/{box}'
 */
update.url = (args: { section: number | { id: number }, andamio: number | { id: number }, box: number | { id: number } } | [section: number | { id: number }, andamio: number | { id: number }, box: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
                    section: args[0],
                    andamio: args[1],
                    box: args[2],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        section: typeof args.section === 'object'
                ? args.section.id
                : args.section,
                                andamio: typeof args.andamio === 'object'
                ? args.andamio.id
                : args.andamio,
                                box: typeof args.box === 'object'
                ? args.box.id
                : args.box,
                }

    return update.definition.url
            .replace('{section}', parsedArgs.section.toString())
            .replace('{andamio}', parsedArgs.andamio.toString())
            .replace('{box}', parsedArgs.box.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Storage\BoxController::update
 * @see app/Http/Controllers/Storage/BoxController.php:92
 * @route '/sections/{section}/andamios/{andamio}/boxes/{box}'
 */
update.put = (args: { section: number | { id: number }, andamio: number | { id: number }, box: number | { id: number } } | [section: number | { id: number }, andamio: number | { id: number }, box: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Storage\BoxController::destroy
 * @see app/Http/Controllers/Storage/BoxController.php:106
 * @route '/sections/{section}/andamios/{andamio}/boxes/{box}'
 */
export const destroy = (args: { section: number | { id: number }, andamio: number | { id: number }, box: number | { id: number } } | [section: number | { id: number }, andamio: number | { id: number }, box: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/sections/{section}/andamios/{andamio}/boxes/{box}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Storage\BoxController::destroy
 * @see app/Http/Controllers/Storage/BoxController.php:106
 * @route '/sections/{section}/andamios/{andamio}/boxes/{box}'
 */
destroy.url = (args: { section: number | { id: number }, andamio: number | { id: number }, box: number | { id: number } } | [section: number | { id: number }, andamio: number | { id: number }, box: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
                    section: args[0],
                    andamio: args[1],
                    box: args[2],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        section: typeof args.section === 'object'
                ? args.section.id
                : args.section,
                                andamio: typeof args.andamio === 'object'
                ? args.andamio.id
                : args.andamio,
                                box: typeof args.box === 'object'
                ? args.box.id
                : args.box,
                }

    return destroy.definition.url
            .replace('{section}', parsedArgs.section.toString())
            .replace('{andamio}', parsedArgs.andamio.toString())
            .replace('{box}', parsedArgs.box.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Storage\BoxController::destroy
 * @see app/Http/Controllers/Storage/BoxController.php:106
 * @route '/sections/{section}/andamios/{andamio}/boxes/{box}'
 */
destroy.delete = (args: { section: number | { id: number }, andamio: number | { id: number }, box: number | { id: number } } | [section: number | { id: number }, andamio: number | { id: number }, box: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})
const BoxController = { index, store, update, destroy }

export default BoxController