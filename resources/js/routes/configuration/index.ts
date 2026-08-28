import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../wayfinder'
import theme from './theme'
import backup from './backup'
/**
* @see \App\Http\Controllers\Configuration\ConfigurationController::index
 * @see app/Http/Controllers/Configuration/ConfigurationController.php:22
 * @route '/configuracion'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/configuracion',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Configuration\ConfigurationController::index
 * @see app/Http/Controllers/Configuration/ConfigurationController.php:22
 * @route '/configuracion'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Configuration\ConfigurationController::index
 * @see app/Http/Controllers/Configuration/ConfigurationController.php:22
 * @route '/configuracion'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Configuration\ConfigurationController::index
 * @see app/Http/Controllers/Configuration/ConfigurationController.php:22
 * @route '/configuracion'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})
const configuration = {
    index: Object.assign(index, index),
theme: Object.assign(theme, theme),
backup: Object.assign(backup, backup),
}

export default configuration