import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\Inbox\InboxController::index
 * @see app/Http/Controllers/Inbox/InboxController.php:20
 * @route '/bandeja'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/bandeja',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Inbox\InboxController::index
 * @see app/Http/Controllers/Inbox/InboxController.php:20
 * @route '/bandeja'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Inbox\InboxController::index
 * @see app/Http/Controllers/Inbox/InboxController.php:20
 * @route '/bandeja'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Inbox\InboxController::index
 * @see app/Http/Controllers/Inbox/InboxController.php:20
 * @route '/bandeja'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Inbox\InboxController::updateStorage
 * @see app/Http/Controllers/Inbox/InboxController.php:28
 * @route '/inbox/update-storage/{id}'
 */
export const updateStorage = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateStorage.url(args, options),
    method: 'put',
})

updateStorage.definition = {
    methods: ["put"],
    url: '/inbox/update-storage/{id}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Inbox\InboxController::updateStorage
 * @see app/Http/Controllers/Inbox/InboxController.php:28
 * @route '/inbox/update-storage/{id}'
 */
updateStorage.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    id: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        id: args.id,
                }

    return updateStorage.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Inbox\InboxController::updateStorage
 * @see app/Http/Controllers/Inbox/InboxController.php:28
 * @route '/inbox/update-storage/{id}'
 */
updateStorage.put = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateStorage.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Inbox\InboxController::deleteFile
 * @see app/Http/Controllers/Inbox/InboxController.php:50
 * @route '/inbox/delete-file/{id}'
 */
export const deleteFile = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deleteFile.url(args, options),
    method: 'delete',
})

deleteFile.definition = {
    methods: ["delete"],
    url: '/inbox/delete-file/{id}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Inbox\InboxController::deleteFile
 * @see app/Http/Controllers/Inbox/InboxController.php:50
 * @route '/inbox/delete-file/{id}'
 */
deleteFile.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    id: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        id: args.id,
                }

    return deleteFile.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Inbox\InboxController::deleteFile
 * @see app/Http/Controllers/Inbox/InboxController.php:50
 * @route '/inbox/delete-file/{id}'
 */
deleteFile.delete = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deleteFile.url(args, options),
    method: 'delete',
})
const inbox = {
    index: Object.assign(index, index),
updateStorage: Object.assign(updateStorage, updateStorage),
deleteFile: Object.assign(deleteFile, deleteFile),
}

export default inbox