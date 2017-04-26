<?php
/**
 * Email: 2284876299@qq.com
 * User: XiaoJm
 * Date: 2017/4/26
 * Time: 下午12:52
 * Desc: after 类测试
 */

class After
{

    /**
     * @var swoole_server
     */
    private $server;

    /**
     * After constructor.
     */
    public function __construct()
    {
        $this->server = new swoole_server('127.0.0.1', 9501, SWOOLE_BASE, SWOOLE_SOCK_TCP);
        $this->server->set([
            'worker_num' => 8,
            'daemonize' => false,
        ]);
        $this->server->on('Start', [$this,'onStart']);
        $this->server->on('Connect', [$this,'onConnect']);
        $this->server->on('Receive', [$this,'onReceive']);
        $this->server->on('Task', [$this,'onTask']);
        $this->server->on('Finish', [$this,'onFinish']);
        $this->server->on('Close', [$this,'onClose']);
        $this->server->start();
    }

    /**
     * @param swoole_server $server
     * @param string $fd
     * @param int $from_id
     */
    public function onStart(swoole_server $server, $fd='', $from_id =0)
    {
        echo "服务器开始启动...\n";
    }

    /**
     * @param swoole_server $server
     * @param $fd
     * @param int $from_id
     */
    public function onConnect(swoole_server $server, $fd, $from_id =0)
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
        if (strlen($data) <1) {
            return false;
        }
        echo "客户端ID为：{$fd}发来消息为:{$data}\n";
        $server->send($fd, $data."\n");
        //$server->task($data . '-' . $fd);
        //$server->after(1000, [$this,'afterSend']);

        //延迟执行函数
       /* $fd = $server->defer(function () use ($fd){
            return $fd*2;
        });
        echo $fd."\n";*/


        //清除定时器
        $timer_id = $server->tick(1000, function ($id) use ($server) {
            echo "数据id：{$id}\n";
            /*if($id >10) {
                echo "清除定时器\n";
                $server->clearTimer($id);
            }*/
        });
        echo $timer_id."\n";
    }


    /**
     * @param swoole_server $serv
     * @param $task_id
     * @param $from_id
     * @param $data
     * @return mixed
     */
    public function onTask(swoole_server $serv, $task_id, $from_id, $data)
    {
        list($str, $fd) = explode('-', $data);
        if ($serv->exist($fd)) {
            echo 'FD[' . $fd . '] exist' . PHP_EOL ;
        } else {
            echo 'FD[' . $fd . '] not exist' . PHP_EOL;
        }
        echo "Task[PID=".$serv->worker_pid."]: task_id=$task_id.".PHP_EOL;
        return $data;
    }

    public function onFinish(swoole_server $serv, $task_id, $data)
    {
        list($str, $fd) = explode('-', $data);
        $serv->send($fd, 'Send Data To FD[' . $fd . ']');
        echo "Task Finish: result=" . $data . ". PID=" . $serv->worker_pid.PHP_EOL;
    }

    public function afterSend()
    {
        $this->server->send(1, '测试推送');
    }

    /**
     * @param swoole_server $server
     * @param $fd
     * @param int $from_id
     */
    public function onClose(swoole_server $server, $fd, $from_id =0)
    {
        echo "客户端为：{$fd}已经断开了。。。\n";
    }
}

$after = new After();
