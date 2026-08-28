import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../wayfinder'
/**
* @see \App\Http\Controllers\Home\SystemController::clear_all
 * @see app/Http/Controllers/Home/SystemController.php:22
 * @route '/admin/clear-all'
 */
export const clear_all = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: clear_all.url(options),
    method: 'delete',
})

clear_all.definition = {
    methods: ["delete"],
    url: '/admin/clear-all',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Home\SystemController::clear_all
 * @see app/Http/Controllers/Home/SystemController.php:22
 * @route '/admin/clear-all'
 */
clear_all.url = (options?: RouteQueryOptions) => {
    return clear_all.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Home\SystemController::clear_all
 * @see app/Http/Controllers/Home/SystemController.php:22
 * @route '/admin/clear-all'
 */
clear_all.delete = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: clear_all.url(options),
    method: 'delete',
})
const admin = {
    clear_all: Object.assign(clear_all, clear_all),
}

export default admin