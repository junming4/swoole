<?php

/**
 * Email: 2284876299@qq.com
 * User: XiaoJm
 * Date: 2017/4/26
 * Time: 下午9:37
 * Desc: 连接数据
 */
class Conn
{
    /**
     * @var swoole_server
     */
    private $server;

    /**
     * Conn constructor.
     */
    public function __construct()
    {
        $this->server = new swoole_server('127.0.0.1', 9501, SWOOLE_BASE, SWOOLE_SOCK_TCP);
        $this->set([
            'worker_num' => 8,
            'daemonize' => false, //1表示在后台守护进程
        ]);
        $this->on('Start', [$this, 'onStart']);
        $this->on('Connect', [$this, 'onConnect']);
        $this->on('Receive', [$this, 'onReceive']);
        $this->on('Close', [$this, 'onClose']);
        $this->server->start();
    }

    /**
     * @param swoole_server $server
     * @param string $fd
     */
    public function onStart(swoole_server $server, $fd = '')
    {
        echo "服务器开始运行\n";
    }

    /**
     * @param swoole_server $server
     * @param $fd
     * @param int $from_id
     */
    public function onConnect(swoole_server $server, $fd, $from_id = 0)
    {
        echo "客户端为：{$fd}已经连接到服务器了\n";
        $server->send($fd, "你已经连接到服务器,并且id为{$fd}\n");
    }

    /**
     * @param swoole_server $server
     * @param $fd
     * @param int $from_id
     * @param $data
     * @return bool
     */
    public function onReceive(swoole_server $server, $fd, $from_id = 0, $data)
    {
        $data = trim($data);
        if (strlen($data) < 1) {
            return false;
        }
        echo "客户端ID为：{$fd}发来消息为:{$data}\n";
        $server->send($fd, $data . "\n");
    }

    /**
     * @param swoole_server $server
     * @param $fd
     * @param int $from_id
     */
    public function onClose(swoole_server $server, $fd, $from_id = 0)
    {
        echo "客户端为：{$fd}已经断开了。。。\n";
    }
}

$conn = new  Conn();
