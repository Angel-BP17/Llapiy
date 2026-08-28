import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Configuration\ConfigurationController::update
 * @see app/Http/Controllers/Configuration/ConfigurationController.php:66
 * @route '/configuracion/theme'
 */
export const update = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: update.url(options),
    method: 'post',
})

update.definition = {
    methods: ["post"],
    url: '/configuracion/theme',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Configuration\ConfigurationController::update
 * @see app/Http/Controllers/Configuration/ConfigurationController.php:66
 * @route '/configuracion/theme'
 */
update.url = (options?: RouteQueryOptions) => {
    return update.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Configuration\ConfigurationController::update
 * @see app/Http/Controllers/Configuration/ConfigurationController.php:66
 * @route '/configuracion/theme'
 */
update.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: update.url(options),
    method: 'post',
})
const theme = {
    update: Object.assign(update, update),
}

export default theme