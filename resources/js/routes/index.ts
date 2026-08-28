import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../wayfinder'
/**
* @see \App\Http\Controllers\Home\AuthController::login
 * @see app/Http/Controllers/Home/AuthController.php:21
 * @route '/login'
 */
export const login = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: login.url(options),
    method: 'get',
})

login.definition = {
    methods: ["get","head"],
    url: '/login',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Home\AuthController::login
 * @see app/Http/Controllers/Home/AuthController.php:21
 * @route '/login'
 */
login.url = (options?: RouteQueryOptions) => {
    return login.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Home\AuthController::login
 * @see app/Http/Controllers/Home/AuthController.php:21
 * @route '/login'
 */
login.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: login.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Home\AuthController::login
 * @see app/Http/Controllers/Home/AuthController.php:21
 * @route '/login'
 */
login.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: login.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Home\AuthController::logout
 * @see app/Http/Controllers/Home/AuthController.php:49
 * @route '/logout'
 */
export const logout = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: logout.url(options),
    method: 'post',
})

logout.definition = {
    methods: ["post"],
    url: '/logout',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Home\AuthController::logout
 * @see app/Http/Controllers/Home/AuthController.php:49
 * @route '/logout'
 */
logout.url = (options?: RouteQueryOptions) => {
    return logout.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Home\AuthController::logout
 * @see app/Http/Controllers/Home/AuthController.php:49
 * @route '/logout'
 */
logout.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: logout.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Home\DashboardController::dashboard
 * @see app/Http/Controllers/Home/DashboardController.php:23
 * @route '/'
 */
export const dashboard = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: dashboard.url(options),
    method: 'get',
})

dashboard.definition = {
    methods: ["get","head"],
    url: '/',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Home\DashboardController::dashboard
 * @see app/Http/Controllers/Home/DashboardController.php:23
 * @route '/'
 */
dashboard.url = (options?: RouteQueryOptions) => {
    return dashboard.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Home\DashboardController::dashboard
 * @see app/Http/Controllers/Home/DashboardController.php:23
 * @route '/'
 */
dashboard.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: dashboard.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Home\DashboardController::dashboard
 * @see app/Http/Controllers/Home/DashboardController.php:23
 * @route '/'
 */
dashboard.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: dashboard.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Users\ProfileController::profile
 * @see app/Http/Controllers/Users/ProfileController.php:16
 * @route '/perfil'
 */
export const profile = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: profile.url(options),
    method: 'get',
})

profile.definition = {
    methods: ["get","head"],
    url: '/perfil',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Users\ProfileController::profile
 * @see app/Http/Controllers/Users/ProfileController.php:16
 * @route '/perfil'
 */
profile.url = (options?: RouteQueryOptions) => {
    return profile.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Users\ProfileController::profile
 * @see app/Http/Controllers/Users/ProfileController.php:16
 * @route '/perfil'
 */
profile.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: profile.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Users\ProfileController::profile
 * @see app/Http/Controllers/Users/ProfileController.php:16
 * @route '/perfil'
 */
profile.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: profile.url(options),
    method: 'head',
})