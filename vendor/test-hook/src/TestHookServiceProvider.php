<?php

namespace TestHook;

use Illuminate\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;

class TestHookServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        $this->publishes([
            __DIR__.'/resources/assets/js' => public_path('vendor/purser'),
        ], 'public');

        $this->loadViewsFrom(__DIR__.'/resources/views/', 'purser');

        $this->publishes([
            __DIR__.'/resources/views/' => resource_path('views/vendor/voyager/'),
        ]);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

        // Add route endpoint for database create with migration
        app(Dispatcher::class)->listen('voyager.admin.routing', function ($router) {

            $namespacePrefix = '\\TestHook\\Http\\Controllers\\';

            $router->post('database/purser', ['uses' => $namespacePrefix.'PurserDatabaseController@purser']);
        });
    }
}
