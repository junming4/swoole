<?php

/**
 * Email: 2284876299@qq.com
 * User: XiaoJm
 * Date: 2017/4/23
 * Time: 上午12:25
 * Desc: http 方式
 */
class Http
{
    /**
     * @var swoole_http_server
     */
    private $http_server_obj;

    /**
     * Http constructor.
     */
    public function __construct()
    {
        $this->http_server_obj = new swoole_http_server('127.0.0.1', 9501);
        $this->http_server_obj->set([
            'worker_num' => 4,
            'daemonize' => false, //1表示在后台守护进程
            'backlog' => 128
        ]);
        $this->http_server_obj->on('Start', [$this, 'onStart']);
        $this->http_server_obj->on('Connect', [$this, 'onConnect']);
        $this->http_server_obj->on('Receive', [$this, 'onReceive']);
        $this->http_server_obj->on('Close', [$this, 'onClose']);
        $this->http_server_obj->start();
    }

    /**
     * @param $server
     */
    public function onStart($server, $fb)
    {
        echo "start\n";
    }

    /**
     * @param $server
     * @param $fd
     * @param int $from_id
     */
    public function onConnect($server, $fd, $from_id = 0)
    {
        $server->send($fd, "Hello {$fd}!");
    }

    /**
     * @param Swoole_http_server $server
     * @param $fd
     * @param $from_id
     * @param $data
     */
    public function onReceive(Swoole_http_server $server, $fd, $from_id, $data)
    {
        if(strlen($data)>0 ) echo "客户端发来数据为:{$data}\n";
        $server->send($fd, $data, $from_id);
    }

    /**
     * @param swoole_http_server $server
     * @param $fd
     * @param $from_id
     */
    public function onClose(swoole_http_server $server, $fd, $from_id)
    {
        echo "连接关闭{$fd}\n";
    }
}

$http = new Http();
