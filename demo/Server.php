<?php

/**
 * Email: 2284876299@qq.com
 * User: XiaoJm
 * Date: 2017/4/23
 * Time: 下午3:43
 * Desc: 一般server
 */
class Server
{
    /**
     * @var swoole_server
     */
    private $serverObj;

    /**
     * Server constructor.
     */
    public function __construct()
    {
        $this->serverObj = new swoole_server('127.0.0.1', 9501, SWOOLE_BASE, SWOOLE_SOCK_TCP);
        $this->serverObj->set([
            'worker_num' => 8, //
            'daemonize' => false, // 可以设置是否在后台守护进程，1后台，0 前台
            'max_request' => 2, //此参数表示worker进程在处理完n次请求后结束运行
            'max_conn' => 2, //最多允许多少,使用telnet连接没有作用
            'heartbeat_check_interval' => 2, //心跳判断是否闲置，如果是闲置的客户端就自动关闭
            //'heartbeat_idle_time' => 2
        ]);
        $this->serverObj->on('Start', [$this, 'onStart']);
        $this->serverObj->on('Connect', [$this, 'onConnect']);
        $this->serverObj->on('Receive', [$this, 'onReceive']);
        $this->serverObj->on('Close', [$this, 'onClose']);
        $this->serverObj->start();
    }

    /**
     * @param swoole_server $server
     * @param $fb
     */
    public function onStart(swoole_server $server, $fb = '', $from_id = 0)
    {
        echo "开始服务进程{$fb}\n";
    }

    /**
     * @param swoole_server $server
     * @param $fb
     * @param int $from_id
     */
    public function onConnect(swoole_server $server, $fb, $from_id = 0)
    {
        echo "有客户端已经连接上服务了。。。{$fb}\n";
        $this->serverObj->send($fb, '你已经连接上服务了');
    }

    /**
     * @param swoole_server $server
     * @param $fb
     * @param $from_id
     * @param $data
     */
    public function onReceive(swoole_server $server, $fb, $from_id, $data)
    {
        $data = trim($data);
        if(empty($data)) return false;

        file_put_contents('../tmp/swoole.txt', 'data=>'.$data, FILE_APPEND);

        echo "你接受到客户端{$fb}数据为:{$data}\n";
        $this->serverObj->send($fb, $data."\n");
    }

    /**
     * @param swoole_server $server
     * @param $fb
     * @param int $from_id
     */
    public function onClose(swoole_server $server, $fb, $from_id = 0)
    {
        echo "客户端{$fb}已经断开服务了";
    }

}

$server = new Server();