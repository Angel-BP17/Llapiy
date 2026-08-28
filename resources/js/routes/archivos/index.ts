import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\Storage\ArchivoController::index
 * @see app/Http/Controllers/Storage/ArchivoController.php:21
 * @route '/sections/{section}/andamios/{andamio}/boxes/{box}/archivos'
 */
export const index = (args: { section: string | number, andamio: string | number, box: string | number } | [section: string | number, andamio: string | number, box: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/sections/{section}/andamios/{andamio}/boxes/{box}/archivos',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Storage\ArchivoController::index
 * @see app/Http/Controllers/Storage/ArchivoController.php:21
 * @route '/sections/{section}/andamios/{andamio}/boxes/{box}/archivos'
 */
index.url = (args: { section: string | number, andamio: string | number, box: string | number } | [section: string | number, andamio: string | number, box: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
                    section: args[0],
                    andamio: args[1],
                    box: args[2],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        section: args.section,
                                andamio: args.andamio,
                                box: args.box,
                }

    return index.definition.url
            .replace('{section}', parsedArgs.section.toString())
            .replace('{andamio}', parsedArgs.andamio.toString())
            .replace('{box}', parsedArgs.box.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Storage\ArchivoController::index
 * @see app/Http/Controllers/Storage/ArchivoController.php:21
 * @route '/sections/{section}/andamios/{andamio}/boxes/{box}/archivos'
 */
index.get = (args: { section: string | number, andamio: string | number, box: string | number } | [section: string | number, andamio: string | number, box: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Storage\ArchivoController::index
 * @see app/Http/Controllers/Storage/ArchivoController.php:21
 * @route '/sections/{section}/andamios/{andamio}/boxes/{box}/archivos'
 */
index.head = (args: { section: string | number, andamio: string | number, box: string | number } | [section: string | number, andamio: string | number, box: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Storage\ArchivoController::move
 * @see app/Http/Controllers/Storage/ArchivoController.php:45
 * @route '/sections/{section}/andamios/{andamio}/boxes/{box}/archivos/{block}/move'
 */
export const move = (args: { section: string | number, andamio: string | number, box: string | number, block: string | number } | [section: string | number, andamio: string | number, box: string | number, block: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: move.url(args, options),
    method: 'post',
})

move.definition = {
    methods: ["post"],
    url: '/sections/{section}/andamios/{andamio}/boxes/{box}/archivos/{block}/move',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Storage\ArchivoController::move
 * @see app/Http/Controllers/Storage/ArchivoController.php:45
 * @route '/sections/{section}/andamios/{andamio}/boxes/{box}/archivos/{block}/move'
 */
move.url = (args: { section: string | number, andamio: string | number, box: string | number, block: string | number } | [section: string | number, andamio: string | number, box: string | number, block: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
                    section: args[0],
                    andamio: args[1],
                    box: args[2],
                    block: args[3],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        section: args.section,
                                andamio: args.andamio,
                                box: args.box,
                                block: args.block,
                }

    return move.definition.url
            .replace('{section}', parsedArgs.section.toString())
            .replace('{andamio}', parsedArgs.andamio.toString())
            .replace('{box}', parsedArgs.box.toString())
            .replace('{block}', parsedArgs.block.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Storage\ArchivoController::move
 * @see app/Http/Controllers/Storage/ArchivoController.php:45
 * @route '/sections/{section}/andamios/{andamio}/boxes/{box}/archivos/{block}/move'
 */
move.post = (args: { section: string | number, andamio: string | number, box: string | number, block: string | number } | [section: string | number, andamio: string | number, box: string | number, block: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: move.url(args, options),
    method: 'post',
})
const archivos = {
    index: Object.assign(index, index),
move: Object.assign(move, move),
}

export default archivos