<?php
/**
 * Created by PhpStorm.
 * User: weili
 * Date: 2021-01-23
 * Time: 15:45
 */

namespace SwoWorker\Server\Http;
use SwoWorker\Server\ServerBase;

class Server extends ServerBase
{
    protected function createServer()
    {
        $this->swooleServer = new \Swoole\Http\Server($this->host, $this->port);
    }
    protected function initEvents()
    {
        $this->serverEvents['sub'] = [
            'request' => 'onRequest'
        ];
    }
    protected function initConfig()
    {

    }

    public function onRequest(\Swoole\Http\Request $request, \Swoole\Http\Response $response)
    {
        p("http request");
        $response->end('success');
    }
}