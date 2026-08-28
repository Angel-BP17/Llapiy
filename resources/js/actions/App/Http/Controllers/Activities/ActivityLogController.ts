import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Activities\ActivityLogController::index
 * @see app/Http/Controllers/Activities/ActivityLogController.php:22
 * @route '/actividades'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/actividades',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Activities\ActivityLogController::index
 * @see app/Http/Controllers/Activities/ActivityLogController.php:22
 * @route '/actividades'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Activities\ActivityLogController::index
 * @see app/Http/Controllers/Activities/ActivityLogController.php:22
 * @route '/actividades'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Activities\ActivityLogController::index
 * @see app/Http/Controllers/Activities/ActivityLogController.php:22
 * @route '/actividades'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Activities\ActivityLogController::pdf
 * @see app/Http/Controllers/Activities/ActivityLogController.php:44
 * @route '/actividades/pdf'
 */
export const pdf = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: pdf.url(options),
    method: 'get',
})

pdf.definition = {
    methods: ["get","head"],
    url: '/actividades/pdf',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Activities\ActivityLogController::pdf
 * @see app/Http/Controllers/Activities/ActivityLogController.php:44
 * @route '/actividades/pdf'
 */
pdf.url = (options?: RouteQueryOptions) => {
    return pdf.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Activities\ActivityLogController::pdf
 * @see app/Http/Controllers/Activities/ActivityLogController.php:44
 * @route '/actividades/pdf'
 */
pdf.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: pdf.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Activities\ActivityLogController::pdf
 * @see app/Http/Controllers/Activities/ActivityLogController.php:44
 * @route '/actividades/pdf'
 */
pdf.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: pdf.url(options),
    method: 'head',
})
const ActivityLogController = { index, pdf }

export default ActivityLogController