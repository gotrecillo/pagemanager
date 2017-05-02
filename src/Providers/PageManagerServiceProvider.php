<?php

namespace Gotrecillo\PageManager\Providers;

use Backpack\CRUD\CrudServiceProvider;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class PageManagerServiceProvider extends ServiceProvider
{
    protected $defer = false;

    public function boot()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/gotrecillo/page.php',
            'gotrecillo/page'
        );
        $this->loadTranslationsFrom(__DIR__ . '/../../resources/lang', 'gotrecillo');

        $this->publishMigrations();
        $this->publishes([__DIR__ . '/../../resources/views' => base_path('resources/views')], 'views');
        $this->publishes([__DIR__ . '/../../resources/lang' => base_path('resources/lang')], 'lang');
        $this->publishes([__DIR__ . '/../../config/' => config_path()], 'config');
        $this->publishes(
            [__DIR__ . '/../../vendor/cviebrock/eloquent-sluggable/resources/config/' => config_path()],
            'config'
        );
    }

    public function publishMigrations()
    {
        $timestamp = date('Y_m_d_His', time());
        $fileName = $timestamp . "_create_pages_table.php.stub";
        $destination = $this->app->databasePath() . "/migrations/stubs/{$fileName}";

        $stubLocation = __DIR__ . '/../../database/migrations/create_pages_table.php.stub';


        $this->publishes([
            $stubLocation => $destination
        ], 'migrations');
    }

    public function setupRoutes(Router $router)
    {
        $router->group(['namespace' => 'Gotrecillo\PageManager\Http\Controllers'], function (Router $router) {
            $router->group([
                'middleware' => config('gotrecillo.page.middleware')
                    ? array_merge(['web', 'admin'], config('gotrecillo.page.middleware'))
                    : ['web', 'admin'],
                'prefix' => config('backpack.base.route_prefix', 'admin')
            ], function (Router $router) {
                CrudServiceProvider::resource('page', 'PageCrudController');
                // Backpack\PageManager routes
                $router->get('page/create/{template}', 'PageCrudController@create');
                $router->get('page/{id}/edit/{template}', 'PageCrudController@edit');
            });
        });
    }

    public function register()
    {
        $this->setupRoutes($this->app->router);
    }
}
