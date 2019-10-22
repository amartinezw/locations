<?php

require_once __DIR__.'/../vendor/autoload.php';

(new Laravel\Lumen\Bootstrap\LoadEnvironmentVariables(
    dirname(__DIR__)
))->bootstrap();

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Here we will load the environment and create the application instance
| that serves as the central piece of this framework. We'll use this
| application as an "IoC" container and router for this framework.
|
*/

$app = new Laravel\Lumen\Application(
    dirname(__DIR__)
);

$app->withFacades();

$app->withEloquent();

$app->configure('auth');

$app->configure('apidoc');

$app->configure('phpunit');
$app->configure('session');

/*
|--------------------------------------------------------------------------
| Register Container Bindings
|--------------------------------------------------------------------------
|
| Now we will register a few bindings in the service container. We will
| register the exception handler and the console kernel. You may add
| your own bindings here if you like or you can make another file.
|
*/

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton('cookie', function () use ($app) {
    return $app->loadComponent('session', 'Illuminate\Cookie\CookieServiceProvider', 'cookie');
});

$app->bind('Illuminate\Contracts\Cookie\QueueingFactory', 'cookie');
/*
|--------------------------------------------------------------------------
| Register Middleware
|--------------------------------------------------------------------------
|
| Next, we will register the middleware with the application. These can
| be global middleware that run before and after each request into a
| route or middleware that'll be assigned to some specific routes.
|
*/

// $app->middleware([
//     App\Http\Middleware\ExampleMiddleware::class
// ]);
$app->middleware([
    App\Http\Middleware\CorsMiddleware::class
 ]);

// $app->routeMiddleware([
//     'auth' => App\Http\Middleware\Authenticate::class,
// ]);

$app->routeMiddleware([
    'auth' => App\Http\Middleware\Authenticate::class,
    'client' => \Laravel\Passport\Http\Middleware\CheckClientCredentials::class,
    'permission' => Spatie\Permission\Middlewares\PermissionMiddleware::class,
    'role'       => Spatie\Permission\Middlewares\RoleMiddleware::class,
]);

$app->middleware([
    'Illuminate\Session\Middleware\StartSession'
]);
/*
|--------------------------------------------------------------------------
| Register Service Providers
|--------------------------------------------------------------------------
|
| Here we will register all of the application's service providers which
| are used to bind services into the container. Service providers are
| totally optional, so you are not required to uncomment this line.
|
*/

$app->bind(Illuminate\Session\SessionManager::class, function ($app) {
    return $app->make('session');
});
// $app->register(App\Providers\AppServiceProvider::class);
$app->register(App\Providers\AuthServiceProvider::class);
$app->register(Flugg\Responder\ResponderServiceProvider::class);
// $app->register(App\Providers\EventServiceProvider::class);
$app->register(\App\Providers\Passport\LumenPassportServiceProvider::class);
$app->register(\Mpociot\ApiDoc\ApiDocGeneratorServiceProvider::class);
$app->register(Illuminate\Session\SessionServiceProvider::class);
$app->configure('permission');
$app->alias('cache', \Illuminate\Cache\CacheManager::class);  // if you don't have this already
$app->register(Spatie\Permission\PermissionServiceProvider::class);
/*
|--------------------------------------------------------------------------
| Load The Application Routes
|--------------------------------------------------------------------------
|
| Next we will include the routes file so that they can all be added to
| the application. This will provide all of the URLs the application
| can respond to, as well as the controllers that may handle them.
|
*/

$app->router->group([
    'namespace' => 'App\Http\Controllers',
], function ($router) {
    require __DIR__.'/../routes/web.php';
});

return $app;
