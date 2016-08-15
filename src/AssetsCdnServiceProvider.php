<?php


namespace AmrSoliman\AssetsCdn;


use Illuminate\Support\ServiceProvider;

class AssetsCdnServiceProvider extends ServiceProvider {

    public function boot()
    {
        $configPath = __DIR__ . '/../config/assets-cdn.php';
        $this->publishes([
            $configPath => config_path('assets-cdn.php')
        ], 'config');
    }

    public function register()
    {
        $configPath = __DIR__ . '/../config/assets-cdn.php';
        $this->mergeConfigFrom($configPath, 'assets-cdn');

        $this->app['command.assets-cdn.update'] = $this->app->share(
            function ()
            {
                return new AssetsCdnUpdateCommand();
            }
        );

        $this->commands('command.assets-cdn.update');

        if (config('assets-cdn.active'))
            $this->registerUrlGenerator();

    }

    /**
     * Register the URL generator service.
     *
     * @return void
     */
    protected function registerUrlGenerator()
    {
        $this->app['url'] = $this->app->share(function($app)
        {
            $routes = $app['router']->getRoutes();

            // The URL generator needs the route collection that exists on the router.
            // Keep in mind this is an object, so we're passing by references here
            // and all the registered routes will be available to the generator.
            $app->instance('routes', $routes);

            $url = new UrlGenerator(
                $routes, $app->rebinding(
                'request', $this->requestRebinder()
            )
            );

            $url->setSessionResolver(function()
            {
                return $this->app['session'];
            });

            // If the route collection is "rebound", for example, when the routes stay
            // cached for the application, we will need to rebind the routes on the
            // URL generator instance so it has the latest version of the routes.
            $app->rebinding('routes', function($app, $routes)
            {
                $app['url']->setRoutes($routes);
            });

            return $url;
        });
    }

    protected function requestRebinder()
    {
        return function($app, $request)
        {
            $app['url']->setRequest($request);
        };
    }

}