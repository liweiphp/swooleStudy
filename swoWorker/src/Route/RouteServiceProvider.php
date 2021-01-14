<?php
/**
 * Created by PhpStorm.
 * User: weili
 * Date: 2021-01-24
 * Time: 08:09
 */

namespace SwoWorker\Route;
use SwoWorker\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    protected $map = [
        'http' => [
            'namespace' => 'app\\http\\',
            'path' => 'app/http/',
        ]
    ];

    public function register()
    {
        // TODO: Implement register() method.
        $this->app->bind('Route', Route::getInstance());

    }
    public function boot()
    {
        // TODO: Implement boot() method.
        $this->app->make('Route')->registerRoute($this->app->getBasePath());
    }

}