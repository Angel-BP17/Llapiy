import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Inbox\NotificationController::index
 * @see app/Http/Controllers/Inbox/NotificationController.php:19
 * @route '/notifications'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/notifications',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Inbox\NotificationController::index
 * @see app/Http/Controllers/Inbox/NotificationController.php:19
 * @route '/notifications'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Inbox\NotificationController::index
 * @see app/Http/Controllers/Inbox/NotificationController.php:19
 * @route '/notifications'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Inbox\NotificationController::index
 * @see app/Http/Controllers/Inbox/NotificationController.php:19
 * @route '/notifications'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Inbox\NotificationController::read
 * @see app/Http/Controllers/Inbox/NotificationController.php:29
 * @route '/notifications/{notification}'
 */
export const read = (args: { notification: string | number } | [notification: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: read.url(args, options),
    method: 'get',
})

read.definition = {
    methods: ["get","head"],
    url: '/notifications/{notification}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Inbox\NotificationController::read
 * @see app/Http/Controllers/Inbox/NotificationController.php:29
 * @route '/notifications/{notification}'
 */
read.url = (args: { notification: string | number } | [notification: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { notification: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    notification: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        notification: args.notification,
                }

    return read.definition.url
            .replace('{notification}', parsedArgs.notification.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Inbox\NotificationController::read
 * @see app/Http/Controllers/Inbox/NotificationController.php:29
 * @route '/notifications/{notification}'
 */
read.get = (args: { notification: string | number } | [notification: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: read.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Inbox\NotificationController::read
 * @see app/Http/Controllers/Inbox/NotificationController.php:29
 * @route '/notifications/{notification}'
 */
read.head = (args: { notification: string | number } | [notification: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: read.url(args, options),
    method: 'head',
})
const NotificationController = { index, read }

export default NotificationController