import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\Storage\AndamioController::index
 * @see app/Http/Controllers/Storage/AndamioController.php:24
 * @route '/sections/{section}/andamios'
 */
export const index = (args: { section: string | number | { id: string | number } } | [section: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/sections/{section}/andamios',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Storage\AndamioController::index
 * @see app/Http/Controllers/Storage/AndamioController.php:24
 * @route '/sections/{section}/andamios'
 */
index.url = (args: { section: string | number | { id: string | number } } | [section: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { section: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { section: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    section: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        section: typeof args.section === 'object'
                ? args.section.id
                : args.section,
                }

    return index.definition.url
            .replace('{section}', parsedArgs.section.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Storage\AndamioController::index
 * @see app/Http/Controllers/Storage/AndamioController.php:24
 * @route '/sections/{section}/andamios'
 */
index.get = (args: { section: string | number | { id: string | number } } | [section: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Storage\AndamioController::index
 * @see app/Http/Controllers/Storage/AndamioController.php:24
 * @route '/sections/{section}/andamios'
 */
index.head = (args: { section: string | number | { id: string | number } } | [section: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Storage\AndamioController::store
 * @see app/Http/Controllers/Storage/AndamioController.php:76
 * @route '/sections/{section}/andamios'
 */
export const store = (args: { section: string | number | { id: string | number } } | [section: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/sections/{section}/andamios',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Storage\AndamioController::store
 * @see app/Http/Controllers/Storage/AndamioController.php:76
 * @route '/sections/{section}/andamios'
 */
store.url = (args: { section: string | number | { id: string | number } } | [section: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { section: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { section: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    section: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        section: typeof args.section === 'object'
                ? args.section.id
                : args.section,
                }

    return store.definition.url
            .replace('{section}', parsedArgs.section.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Storage\AndamioController::store
 * @see app/Http/Controllers/Storage/AndamioController.php:76
 * @route '/sections/{section}/andamios'
 */
store.post = (args: { section: string | number | { id: string | number } } | [section: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Storage\AndamioController::update
 * @see app/Http/Controllers/Storage/AndamioController.php:91
 * @route '/sections/{section}/andamios/{andamio}'
 */
export const update = (args: { section: string | number | { id: string | number }, andamio: string | number | { id: string | number } } | [section: string | number | { id: string | number }, andamio: string | number | { id: string | number } ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/sections/{section}/andamios/{andamio}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Storage\AndamioController::update
 * @see app/Http/Controllers/Storage/AndamioController.php:91
 * @route '/sections/{section}/andamios/{andamio}'
 */
update.url = (args: { section: string | number | { id: string | number }, andamio: string | number | { id: string | number } } | [section: string | number | { id: string | number }, andamio: string | number | { id: string | number } ], options?: RouteQueryOptions) => {
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

    return update.definition.url
            .replace('{section}', parsedArgs.section.toString())
            .replace('{andamio}', parsedArgs.andamio.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Storage\AndamioController::update
 * @see app/Http/Controllers/Storage/AndamioController.php:91
 * @route '/sections/{section}/andamios/{andamio}'
 */
update.put = (args: { section: string | number | { id: string | number }, andamio: string | number | { id: string | number } } | [section: string | number | { id: string | number }, andamio: string | number | { id: string | number } ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Storage\AndamioController::destroy
 * @see app/Http/Controllers/Storage/AndamioController.php:106
 * @route '/sections/{section}/andamios/{andamio}'
 */
export const destroy = (args: { section: string | number | { id: string | number }, andamio: string | number | { id: string | number } } | [section: string | number | { id: string | number }, andamio: string | number | { id: string | number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/sections/{section}/andamios/{andamio}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Storage\AndamioController::destroy
 * @see app/Http/Controllers/Storage/AndamioController.php:106
 * @route '/sections/{section}/andamios/{andamio}'
 */
destroy.url = (args: { section: string | number | { id: string | number }, andamio: string | number | { id: string | number } } | [section: string | number | { id: string | number }, andamio: string | number | { id: string | number } ], options?: RouteQueryOptions) => {
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

    return destroy.definition.url
            .replace('{section}', parsedArgs.section.toString())
            .replace('{andamio}', parsedArgs.andamio.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Storage\AndamioController::destroy
 * @see app/Http/Controllers/Storage/AndamioController.php:106
 * @route '/sections/{section}/andamios/{andamio}'
 */
destroy.delete = (args: { section: string | number | { id: string | number }, andamio: string | number | { id: string | number } } | [section: string | number | { id: string | number }, andamio: string | number | { id: string | number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})
const andamios = {
    index: Object.assign(index, index),
store: Object.assign(store, store),
update: Object.assign(update, update),
destroy: Object.assign(destroy, destroy),
}

export default andamios