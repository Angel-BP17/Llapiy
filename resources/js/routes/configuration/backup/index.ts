import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Configuration\ConfigurationController::exportMethod
 * @see app/Http/Controllers/Configuration/ConfigurationController.php:30
 * @route '/configuracion/backup/export'
 */
export const exportMethod = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportMethod.url(options),
    method: 'get',
})

exportMethod.definition = {
    methods: ["get","head"],
    url: '/configuracion/backup/export',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Configuration\ConfigurationController::exportMethod
 * @see app/Http/Controllers/Configuration/ConfigurationController.php:30
 * @route '/configuracion/backup/export'
 */
exportMethod.url = (options?: RouteQueryOptions) => {
    return exportMethod.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Configuration\ConfigurationController::exportMethod
 * @see app/Http/Controllers/Configuration/ConfigurationController.php:30
 * @route '/configuracion/backup/export'
 */
exportMethod.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportMethod.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Configuration\ConfigurationController::exportMethod
 * @see app/Http/Controllers/Configuration/ConfigurationController.php:30
 * @route '/configuracion/backup/export'
 */
exportMethod.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: exportMethod.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Configuration\ConfigurationController::importMethod
 * @see app/Http/Controllers/Configuration/ConfigurationController.php:43
 * @route '/configuracion/backup/import'
 */
export const importMethod = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: importMethod.url(options),
    method: 'post',
})

importMethod.definition = {
    methods: ["post"],
    url: '/configuracion/backup/import',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Configuration\ConfigurationController::importMethod
 * @see app/Http/Controllers/Configuration/ConfigurationController.php:43
 * @route '/configuracion/backup/import'
 */
importMethod.url = (options?: RouteQueryOptions) => {
    return importMethod.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Configuration\ConfigurationController::importMethod
 * @see app/Http/Controllers/Configuration/ConfigurationController.php:43
 * @route '/configuracion/backup/import'
 */
importMethod.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: importMethod.url(options),
    method: 'post',
})
const backup = {
    export: Object.assign(exportMethod, exportMethod),
import: Object.assign(importMethod, importMethod),
}

export default backup