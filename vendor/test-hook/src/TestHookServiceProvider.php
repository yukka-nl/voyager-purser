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
        // Todo: add user to middleware (?)
        app('router')->post('/admin/database', ['uses' => '\\TestHook\\Http\\Controllers\\PurserDatabaseController@purser', 'as' => 'voyager.database.store'])->middleware(['web', 'TCG\Voyager\Http\Middleware\\VoyagerAdminMiddleware']);;
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
         
    }
}
