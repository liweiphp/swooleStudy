<?php
/**
 * Created by PhpStorm.
 * User: weili
 * Date: 2021-01-24
 * Time: 08:48
 */
namespace SwoWorker\Event;
use \SwoWorker\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    public function register()
    {
        // TODO: Implement register() method.
        return new Event();
    }
    public function boot()
    {
        // TODO: Implement boot() method.
    }
}