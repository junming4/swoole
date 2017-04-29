<?php

/**
 * Email: 2284876299@qq.com
 * User: XiaoJm
 * Date: 2017/4/29
 * Time: 下午2:47
 * Desc: 类简单描述一下
 */
class HttpServer
{
    /**
     * @var swoole_http_server
     */
    private $server;

    /**
     * HttpServer constructor.
     */
    public function __construct()
    {
        $this->server = new swoole_http_server('127.0.0.1', 9501);
        $this->server->on('request', [$this, 'onRequest']);
        $this->server->start();
    }

    /**
     * @param $request
     * @param $response
     */
    public function onRequest($request, $response)
    {
        $response->end('你访问得ip地址为:'.$request->server['remote_addr']."\n");
    }
}

$httpServer = new HttpServer();
