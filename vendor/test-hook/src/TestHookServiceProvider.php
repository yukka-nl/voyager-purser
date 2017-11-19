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
        // Database actions
        app('router')->post('/admin/database', ['uses' => '\\TestHook\\Http\\Controllers\\PurserDatabaseController@storeDatabaseTable', 'as' => 'voyager.database.store'])->middleware(['web', 'TCG\Voyager\Http\Middleware\\VoyagerAdminMiddleware']);;

        app('router')->patch('/admin/database/{database}', ['uses' => '\\TestHook\\Http\\Controllers\\PurserDatabaseController@updateDatabaseTable', 'as' => 'voyager.database.update'])->middleware(['web', 'TCG\Voyager\Http\Middleware\\VoyagerAdminMiddleware']);

         app('router')->put('/admin/database/{database}', ['uses' => '\\TestHook\\Http\\Controllers\\PurserDatabaseController@updateDatabaseTable', 'as' => 'voyager.database.update'])->middleware(['web', 'TCG\Voyager\Http\Middleware\\VoyagerAdminMiddleware']);

         // Bread actions

         // Setting actions
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
