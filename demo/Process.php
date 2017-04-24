<?php

/**
 * Email: 2284876299@qq.com
 * User: XiaoJm
 * Date: 2017/4/23
 * Time: 下午10:18
 * Desc: swoole 自定义工程实现
 */
class Process
{
    /**
     * @var swoole_server
     */
    private $server;

    private $processObj;

    /**
     * Process constructor.
     */
    public function __construct()
    {
        $this->server = $obj = new swoole_server('127.0.0.1', 9503, SWOOLE_BASE, SWOOLE_SOCK_TCP);
        $this->server->set([
            'worker_num' => 8,
            'daemonize' => false,
        ]);

        $this->server->on('Start', [$this, 'onStart']);
        $this->server->on('Connect', [$this, 'onConnect']);
        $this->server->on('Receive', [$this, 'onReceive']);
        $this->server->on('Close', [$this, 'onClose']);

        $this->processObj = new swoole_process(function($process) {

            $start_fd = 0;
            while (true) {
                $msg = $process->read();

                $conn_list = $this->server->connections;

                //file_put_contents('../tmp/swoole.log', json_encode($conn_list),FILE_APPEND);

                if ($conn_list === false or count($conn_list) === 0) {
                    echo "finish\n";
                    break;
                }
                foreach($conn_list as $conn) {
                    //file_put_contents('../tmp/swoole.log', "数据连接为:{$conn};内容为：".$msg."\n",FILE_APPEND);
                    $this->server->send($conn, $msg);
                }
            }
        });


        $this->server->addProcess($this->processObj);
        $this->server->start();
    }

    /**
     * @param swoole_server $server
     * @param string $fb
     * @param int $from_id
     */
    public function onStart(swoole_server $server, $fb = '', $from_id = 0)
    {
        echo "开始服务端....\n";
    }

    /**
     * @param swoole_server $server
     * @param $fd
     * @param int $from_id
     */
    public function onConnect(swoole_server $server, $fd, $from_id = 0)
    {
        echo "{$fd}连接上服务器\n";
        $server->send($fd, "你已经连接上服务器你的id为:{$fd}\n");
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
        if (empty($data)) {
            return false;
        }
        echo "客户端{$fd}发来消息为：{$data}";


        if ($data == 'exit') { //关闭客户端部分
            echo "bii\n";
            $server->stop();//断掉客户端的work，断掉一个客户端
            //$this->server->stop(); //断掉所有客户端
        } //停止work

        if($data == 'die'){ //关闭服务端开始
            echo  "你发送关闭服务端指令!";
            //也可以使用 这个代替（不明白什么意思） ： kill -15 主进程PID
            $server->shutdown();
        }


        $server->send($fd, "你发出的信息为:{$data}");

        //todo 为什么这个connection 使用不了？无法检测有这些数据
        $conn_list = $server->connections;

        file_put_contents('../tmp/swoole.log', json_encode($conn_list),FILE_APPEND);

        //$this->processObj->write($data);
    }

    /**
     * @param swoole_server $server
     * @param string $fd
     * @param int $from_id
     */
    public function onClose(swoole_server $server, $fd = '', $from_id = 0)
    {
        echo $fd . "客户端断开连接\n";
    }
}

$server = new  Process();
