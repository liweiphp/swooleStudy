<?php
/**
 * Created by PhpStorm.
 * User: weili
 * Date: 2021-01-23
 * Time: 14:04
 */
namespace SwoWorker\Foundation;

use SwoWorker\Container\Container;
use SwoWorker\Server\Http\Server as httpServer;
use SwoWorker\Support\Log;


class Application extends Container
{

    const WELCOME = "                      __      __             __                 
  ________  _  ______/  \    /  \___________|  | __ ___________ 
 /  ___/\ \/ \/ /  _ \   \/\/   /  _ \_  __ \  |/ // __ \_  __ \
 \___ \  \     (  <_> )        (  <_> )  | \/    <\  ___/|  | \/
/____  >  \/\_/ \____/ \__/\  / \____/|__|  |__|_ \\___  >__|   
     \/                     \/                   \/    \/     ";
    protected $server;
    protected $bootstraps = [];

    public function __construct()
    {
        Log::p(self::WELCOME);
    }

    public function run($argv)
    {
        Log::p($argv);

        switch ($argv[1]){
            case "start":
                self::setInstance($this);
                $this->server = new httpServer(self::getInstance());
        }

        $this->server->start();

    }

    public function bootstrap()
    {

    }

}