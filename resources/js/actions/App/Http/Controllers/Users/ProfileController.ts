import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Users\ProfileController::show
 * @see app/Http/Controllers/Users/ProfileController.php:16
 * @route '/perfil'
 */
export const show = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/perfil',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Users\ProfileController::show
 * @see app/Http/Controllers/Users/ProfileController.php:16
 * @route '/perfil'
 */
show.url = (options?: RouteQueryOptions) => {
    return show.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Users\ProfileController::show
 * @see app/Http/Controllers/Users/ProfileController.php:16
 * @route '/perfil'
 */
show.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Users\ProfileController::show
 * @see app/Http/Controllers/Users/ProfileController.php:16
 * @route '/perfil'
 */
show.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(options),
    method: 'head',
})
const ProfileController = { show }

export default ProfileController