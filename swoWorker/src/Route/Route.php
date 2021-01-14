<?php
/**
 * Created by PhpStorm.
 * User: weili
 * Date: 2021-01-24
 * Time: 08:12
 */

namespace SwoWorker\Route;


use Couchbase\Cluster;

class Route
{
    protected static $instance = null;
    protected $verbs = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];
    protected $routes = [];
    protected $flag;
    protected $map = [
        'http' => [
            'namespace' => 'app\\http\\'
        ]
    ];
    public function __construct()
    {

    }

    /**
     * 单例获取route对象
     * @return Route|null
     */
    public static function getInstance()
    {
        if (!self::$instance){
            self::$instance = new static();
        }
        return self::$instance;
    }
    public function get($uri, $action)
    {
        $uri = trim($uri, '\/');
        $this->addRoute('GET', $uri, $action);
    }
    public function post($uri, $action)
    {
        $uri = trim($uri, '\/');
        $this->addRoute('POST', $uri, $action);
    }
    public function registerRoute($path)
    {
        $route_path = $path."/app/route";
        $files = scandir($route_path);
        $data = null;
        // 2. 读取文件信息
        foreach ($files as $key => $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            // 2.1 获取文件名
            $filename = \stristr($file, ".php", true);
            $this->flag = $filename;
            // 2.2 读取文件信息
            require_once $route_path."/".$file;
        }
        p($this->routes, "route");
    }
    /**
     * 添加路由映射
     * @param $method
     * @param $uri
     * @param $action
     */
    protected function addRoute($method, $uri, $action)
    {
        if (in_array($method, $this->verbs)) {
            $this->routes[$this->flag][$method][$uri] = $action;
        }
    }

    public function match($flag, $request)
    {
        p($request, "request对象");
        if ($action = $this->routes[$flag][$request->method][$request->uri]) {
            return $this->runAction($action, $flag);
        }
        p("方法不存在");
        return null;
    }
    public function runAction($action, $flag)
    {
        if ($action instanceof \Closure) {
            return call_user_func($action);
        } else {
            list($class, $method) = explode("@", $action);
            return call_user_func([$this->map[$flag]['namespace'].$class(), $method]);
        }
    }
}