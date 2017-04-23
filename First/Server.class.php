<?php
/**
 * Email: 2284876299@qq.com
 * User: XiaoJm
 * Date: 2017/4/22
 * Time: 上午9:57
 * Desc: 类简单描述一下
 */

/**
 * Class Server
 */
class Server
{
    /**
     * @var swoole_server
     */
    private $serv;

    /**
     * Server constructor.
     */
    public function __construct()
    {
        $this->serv = new swoole_server("0.0.0.0", 9501);
        $this->serv->set(array(
            'worker_num' => 8,
            'daemonize' => false,
        ));
        $this->serv->on('Start', array($this, 'onStart'));
        $this->serv->on('Connect', array($this, 'onConnect'));
        /*$this->serv->on('Task', 'onTask');
        $this->serv->on('Finish','onFinish');*/
        $this->serv->on('Receive', array($this, 'onReceive'));
        $this->serv->on('Close', array($this, 'onClose'));
        $this->serv->start();
    }

    /**
     * @param $serv
     */
    public function onStart($serv)
    {
        echo "Start\n";
    }

    /**
     * @param $serv
     * @param $fd
     * @param $from_id
     */
    public function onConnect($serv, $fd, $from_id)
    {
        $serv->send($fd, "Hello {$fd}!");
    }

    /**
     * @param swoole_server $serv
     * @param $fd
     * @param $from_id
     * @param $data
     */
    public function onReceive(swoole_server $serv, $fd, $from_id, $data)
    {
        echo "Get Message From Client {$fd}:{$data}\n";
        $serv->send($fd, $data);
    }

    /**
     * @param $serv
     * @param $fd
     * @param $from_id
     */
    public function onClose($serv, $fd, $from_id)
    {
        echo "Client {$fd} close connection\n";
    }
}

$server = new Server();
