import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Home\DashboardController::index
 * @see app/Http/Controllers/Home/DashboardController.php:23
 * @route '/'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Home\DashboardController::index
 * @see app/Http/Controllers/Home/DashboardController.php:23
 * @route '/'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Home\DashboardController::index
 * @see app/Http/Controllers/Home/DashboardController.php:23
 * @route '/'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Home\DashboardController::index
 * @see app/Http/Controllers/Home/DashboardController.php:23
 * @route '/'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Home\DashboardController::stats
 * @see app/Http/Controllers/Home/DashboardController.php:45
 * @route '/dashboard/stats'
 */
export const stats = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: stats.url(options),
    method: 'get',
})

stats.definition = {
    methods: ["get","head"],
    url: '/dashboard/stats',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Home\DashboardController::stats
 * @see app/Http/Controllers/Home/DashboardController.php:45
 * @route '/dashboard/stats'
 */
stats.url = (options?: RouteQueryOptions) => {
    return stats.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Home\DashboardController::stats
 * @see app/Http/Controllers/Home/DashboardController.php:45
 * @route '/dashboard/stats'
 */
stats.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: stats.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Home\DashboardController::stats
 * @see app/Http/Controllers/Home/DashboardController.php:45
 * @route '/dashboard/stats'
 */
stats.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: stats.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Home\DashboardController::notifications
 * @see app/Http/Controllers/Home/DashboardController.php:61
 * @route '/notifications/api'
 */
export const notifications = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: notifications.url(options),
    method: 'get',
})

notifications.definition = {
    methods: ["get","head"],
    url: '/notifications/api',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Home\DashboardController::notifications
 * @see app/Http/Controllers/Home/DashboardController.php:61
 * @route '/notifications/api'
 */
notifications.url = (options?: RouteQueryOptions) => {
    return notifications.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Home\DashboardController::notifications
 * @see app/Http/Controllers/Home/DashboardController.php:61
 * @route '/notifications/api'
 */
notifications.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: notifications.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Home\DashboardController::notifications
 * @see app/Http/Controllers/Home/DashboardController.php:61
 * @route '/notifications/api'
 */
notifications.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: notifications.url(options),
    method: 'head',
})
const DashboardController = { index, stats, notifications }

export default DashboardController