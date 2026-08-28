import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../wayfinder'
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

/**
* @see \App\Http\Controllers\Configuration\ConfigurationController::updateTheme
 * @see app/Http/Controllers/Configuration/ConfigurationController.php:66
 * @route '/configuracion/theme'
 */
export const updateTheme = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updateTheme.url(options),
    method: 'post',
})

updateTheme.definition = {
    methods: ["post"],
    url: '/configuracion/theme',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Configuration\ConfigurationController::updateTheme
 * @see app/Http/Controllers/Configuration/ConfigurationController.php:66
 * @route '/configuracion/theme'
 */
updateTheme.url = (options?: RouteQueryOptions) => {
    return updateTheme.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Configuration\ConfigurationController::updateTheme
 * @see app/Http/Controllers/Configuration/ConfigurationController.php:66
 * @route '/configuracion/theme'
 */
updateTheme.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updateTheme.url(options),
    method: 'post',
})

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
const ConfigurationController = { index, updateTheme, exportMethod, importMethod, export: exportMethod, import: importMethod }

export default ConfigurationController