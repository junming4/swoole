<?php

/**
 * Email: 2284876299@qq.com
 * User: XiaoJm
 * Date: 2017/4/28
 * Time: 下午8:57
 * 客户端类
 */
class Client
{
    /**
     * @var swoole_client
     */
    private $clientObj;

    /**
     * Client constructor.
     */
    public function __construct()
    {
        $this->clientObj = new swoole_client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_ASYNC);

        $this->clientObj->on("connect", [$this, 'onConnect']);
        $this->clientObj->on("receive", [$this, 'onReceive']);

        //TODO 这个错误需要，调用需要不然就会报错
        $this->clientObj->on("error", [$this, 'onError']);
        $this->clientObj->on('close', [$this, 'onClose']);

        //$flag参数在TCP类型,$flag=1表示设置为非阻塞socket，connect会立即返回。
        //如果将$flag设置为1，那么在send/recv前必须使用swoole_client_select来检测是否完成了连接
        $this->clientObj->connect('127.0.0.1', 9501 ,0.1, 1);
    }

    /**
     * @param swoole_client $clientObj
     */
    public function onConnect(swoole_client $clientObj)
    {
        $clientObj->send("GET / HTTP/1.1\r\n\r\n");
    }

    /**
     * @param swoole_client $clientObj
     * @param $data
     */
    public function onReceive(swoole_client $clientObj, $data)
    {
        echo "Receive: $data";
        $clientObj->send(str_repeat('A', 100) . "\n");
        sleep(1);
    }

    /**
     * @param swoole_client $clientObj
     */
    public function onError(swoole_client $clientObj)
    {
        echo "error\n";
    }

    /**
     * @param swoole_client $clientObj
     */
    public function onClose(swoole_client $clientObj)
    {
        echo "Connection close\n";
    }
}

$client = new Client();