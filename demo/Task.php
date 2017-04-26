<?php

/**
 * Email: 2284876299@qq.com
 * User: XiaoJm
 * Date: 2017/4/26
 * Time: 下午11:41
 * Desc: 类简单描述一下
 */
class Task
{
    private $server;

    public function __construct()
    {
        $this->server = new swoole_server('127.0.0.1', 9501, SWOOLE_BASE, SWOOLE_SOCK_TCP);
        $config = [
            'worker_num' => 8,
            'daemonize' => false
        ];
        $this->server->set($config);
        $this->server->on('Start', [$this, 'onStart']);
        $this->server->on('Connect', [$this, 'onConnect']);
        $this->server->on('Receive', [$this, 'onReceive']);
        $this->server->on('Close', [$this, 'onClose']);
        $this->server->start();
    }

    public function onStart(swoole_server $server, $fd = '')
    {
        echo "开始启动服务器。。。\n";
    }

    public function onConnect(swoole_server $server, $fd, $from_id = 0)
    {
        echo "有客户端已经连接上了服务，id号为：{$fd}\n";
        $this->server->send($fd, "你已经连接上客户端，id号为:{$fd}\n");
    }

    public function onReceive(swoole_server $server, $fd, $from_id = 0, $data)
    {
        echo "有客户端id为：{$fd}发送数据为{$data}";
        $this->server->send($fd, "{$data}");
    }

    public function onClose(swoole_server $server, $fd, $from_id = 0)
    {
        echo "客户端为{$fd}已经断开\n";
    }
}

$task = new Task();
