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
            'daemonize' => false,
            'task_worker_num' => 2, //有任务时必须指定这个，指定这个必须绑定任务
        ];
        $this->server->set($config);
        $this->server->on('Start', [$this, 'onStart']);
        $this->server->on('Connect', [$this, 'onConnect']);
        $this->server->on('Receive', [$this, 'onReceive']);
        $this->server->on('Task', [$this, 'onTask']);
        $this->server->on('Finish', [$this, 'onFinish']);
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
        $server->send($fd, "你已经连接上客户端，id号为:{$fd}\n");
    }

    public function onReceive(swoole_server $server, $fd, $from_id = 0, $data)
    {
        echo "有客户端id为：{$fd}发送数据为{$data}";
        $server->send($fd, "{$data}");
        $server->task("some_data");

        //$connections属性是一个迭代器对象，不是PHP数组，所以不能用var_dump或者数组下标来访问，只能通过foreach进行遍历操作
        foreach($server->connections as $fd)
        {
            $server->send($fd, "hello:{$fd}\n");
        }

        //echo "当前服务器共有 ".count($server->connections). " 个连接\n";

    }

    /**
     * @param swoole_server $server
     * @param $task_id
     * @param int $from_id
     * @param $data
     * @return mixed
     */
    public function onTask(swoole_server $server, $task_id, $from_id = 0, $data)
    {
        //这个值会返回到onFinish 中去
        return  $task_id.'-'.$data;
    }

    public function onFinish(swoole_server $server, $task_id, $data)
    {
        list($str, $fd) = explode('-', $data);
        $server->send($fd, 'Send Data To FD[' . $fd . ']');
        echo "Task Finish: result=" . $data . ". PID=" . $server->worker_pid.PHP_EOL;
    }

    public function onClose(swoole_server $server, $fd, $from_id = 0)
    {
        echo "客户端为{$fd}已经断开\n";
    }
}

$task = new Task();
