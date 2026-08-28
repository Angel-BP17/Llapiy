import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Home\SystemController::clearAll
 * @see app/Http/Controllers/Home/SystemController.php:22
 * @route '/admin/clear-all'
 */
export const clearAll = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: clearAll.url(options),
    method: 'delete',
})

clearAll.definition = {
    methods: ["delete"],
    url: '/admin/clear-all',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Home\SystemController::clearAll
 * @see app/Http/Controllers/Home/SystemController.php:22
 * @route '/admin/clear-all'
 */
clearAll.url = (options?: RouteQueryOptions) => {
    return clearAll.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Home\SystemController::clearAll
 * @see app/Http/Controllers/Home/SystemController.php:22
 * @route '/admin/clear-all'
 */
clearAll.delete = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: clearAll.url(options),
    method: 'delete',
})
const SystemController = { clearAll }

export default SystemController