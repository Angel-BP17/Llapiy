import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Storage\SectionController::index
 * @see app/Http/Controllers/Storage/SectionController.php:23
 * @route '/sections'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/sections',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Storage\SectionController::index
 * @see app/Http/Controllers/Storage/SectionController.php:23
 * @route '/sections'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Storage\SectionController::index
 * @see app/Http/Controllers/Storage/SectionController.php:23
 * @route '/sections'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Storage\SectionController::index
 * @see app/Http/Controllers/Storage/SectionController.php:23
 * @route '/sections'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Storage\SectionController::store
 * @see app/Http/Controllers/Storage/SectionController.php:74
 * @route '/sections'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/sections',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Storage\SectionController::store
 * @see app/Http/Controllers/Storage/SectionController.php:74
 * @route '/sections'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Storage\SectionController::store
 * @see app/Http/Controllers/Storage/SectionController.php:74
 * @route '/sections'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Storage\SectionController::show
 * @see app/Http/Controllers/Storage/SectionController.php:89
 * @route '/sections/{section}'
 */
export const show = (args: { section: string | number | { id: string | number } } | [section: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/sections/{section}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Storage\SectionController::show
 * @see app/Http/Controllers/Storage/SectionController.php:89
 * @route '/sections/{section}'
 */
show.url = (args: { section: string | number | { id: string | number } } | [section: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
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

    return show.definition.url
            .replace('{section}', parsedArgs.section.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Storage\SectionController::show
 * @see app/Http/Controllers/Storage/SectionController.php:89
 * @route '/sections/{section}'
 */
show.get = (args: { section: string | number | { id: string | number } } | [section: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Storage\SectionController::show
 * @see app/Http/Controllers/Storage/SectionController.php:89
 * @route '/sections/{section}'
 */
show.head = (args: { section: string | number | { id: string | number } } | [section: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Storage\SectionController::update
 * @see app/Http/Controllers/Storage/SectionController.php:101
 * @route '/sections/{section}'
 */
export const update = (args: { section: string | number | { id: string | number } } | [section: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/sections/{section}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Storage\SectionController::update
 * @see app/Http/Controllers/Storage/SectionController.php:101
 * @route '/sections/{section}'
 */
update.url = (args: { section: string | number | { id: string | number } } | [section: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
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

    return update.definition.url
            .replace('{section}', parsedArgs.section.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Storage\SectionController::update
 * @see app/Http/Controllers/Storage/SectionController.php:101
 * @route '/sections/{section}'
 */
update.put = (args: { section: string | number | { id: string | number } } | [section: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Storage\SectionController::destroy
 * @see app/Http/Controllers/Storage/SectionController.php:116
 * @route '/sections/{section}'
 */
export const destroy = (args: { section: string | number | { id: string | number } } | [section: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/sections/{section}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Storage\SectionController::destroy
 * @see app/Http/Controllers/Storage/SectionController.php:116
 * @route '/sections/{section}'
 */
destroy.url = (args: { section: string | number | { id: string | number } } | [section: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
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

    return destroy.definition.url
            .replace('{section}', parsedArgs.section.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Storage\SectionController::destroy
 * @see app/Http/Controllers/Storage/SectionController.php:116
 * @route '/sections/{section}'
 */
destroy.delete = (args: { section: string | number | { id: string | number } } | [section: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})
const SectionController = { index, store, show, update, destroy }

export default SectionController