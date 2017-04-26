<?php

/**
 * Email: 2284876299@qq.com
 * User: XiaoJm
 * Date: 2017/4/24
 * Time: 下午11:11
 * Desc: 定时器类
 */
class Ticker
{
    /**
     * @var swoole_server
     */
    private $server;

    /**
     * Ticker constructor.
     */
    public function __construct()
    {
        $this->server = new swoole_server('127.0.0.1', 9501, SWOOLE_BASE, SWOOLE_SOCK_TCP);
        $this->server->set([
            'worker_num' => 8,
            'daemonize' => false
        ]);
        $this->server->on('Start', [$this, 'onStart']);
        $this->server->on('Connect', [$this, 'onConnect']);
        $this->server->on('Receive', [$this, 'onReceive']);
        $this->server->on('Worker', [$this, 'onWorker']);
        $this->server->on('Close', [$this, 'onClose']);

        $this->server->start();
    }

    /**
     * @param swoole_server $server
     * @param string $fd
     */
    public function onStart(swoole_server $server, $fd = '')
    {
        echo "启动服务器中。。。\n";
    }

    /**
     * @param swoole_server $server
     * @param $fd
     * @param int $from_id
     */
    public function onConnect(swoole_server $server, $fd, $from_id = 0)
    {
        echo "客户端{$fd}连接到服务器中。。。\n";
        $server->send($fd, "你已经连接到服务器中了。。。\n");
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

        echo "客户端{$fd}发送{$data}过来\n";
        $server->send($fd, "你发送到服务器的数据为:{$data}\n");

        //加定时发送【会发送很多次】
        //todo 在onWorkerStart中使用 不太明白
        /*$server->tick(1000, function() use ($server, $fd) {
            $server->send($fd, "hello world");
        });*/

        return false;
    }

    /**
     * 执行多次任务worker
     * @param swoole_server $server
     * @param $worker_id
     */
    public function onWorker(swoole_server $server, $worker_id)
    {
        echo "这就是worker_id=".$worker_id."\n";
        if (!$server->taskworker) {
            $server->tick(1000, function ($id) {
                var_dump($id);
            });
        } else {
            $server->addtimer(1000);
        }
    }

    /**
     * @param swoole_server $server
     * @param $fd
     * @param int $from_id
     */
    public function onClose(swoole_server $server, $fd, $from_id = 0)
    {
        echo "客户端{$fd}断开服务器\n";
    }
}

$ticker = new Ticker();
