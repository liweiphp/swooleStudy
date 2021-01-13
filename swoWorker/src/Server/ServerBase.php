<?php
/**
 * Created by PhpStorm.
 * User: weili
 * Date: 2021-01-23
 * Time: 15:16
 */

namespace SwoWorker\Server;

use SwoWorker\Foundation\Application;
use SwoWorker\Support\Log;

abstract class ServerBase
{
    protected $swooleServer;
    protected $host = '0.0.0.0';
    protected $port = '9999';
    protected $app;
    protected $serverConfig = [
        'worker_num' => 1,
        'task_worker_num' => 0
    ];
    protected $serverEvents = [
        'server' => [
            'start' => 'onStart'
        ],
        'sub' => [],
        'ext' => []
    ];
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->initConfig();
        $this->createServer();
        $this->initEvents();
        $this->setEvents();
    }
    abstract protected function createServer();
    //server 自己的方法
    abstract protected function initEvents();
    abstract protected function initConfig();
    public function start()
    {
        $this->swooleServer->set($this->serverConfig);
        $this->swooleServer->start();
    }
    public function onStart(){
        Log::p($this->host.":".$this->port, "服务启动");
    }
    protected function setEvents()
    {
        foreach ($this->serverEvents as $events){
            foreach ($events as $evnet => $func){
                $this->swooleServer->on($evnet, [$this, $func]);
            }
        }
    }
}