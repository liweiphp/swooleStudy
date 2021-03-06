<?php
/**
 * Created by PhpStorm.
 * User: weili
 * Date: 2021-01-25
 * Time: 09:15
 */
namespace app\Providers;
use \SwoWorker\Route\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    protected $map;
    public function register()
    {
        $this->setHttpRoute();
        parent::register(); // TODO: Change the autogenerated stub
    }

    public function setHttpRoute()
    {
        $this->map['http'] = [
            'namespace' => 'app\Http\Controller',
            'path' => $this->app->getBasePath().'/route/http.php'
        ];
    }
}