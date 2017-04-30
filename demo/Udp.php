<?php

/**
 * Email: 2284876299@qq.com
 * User: XiaoJm
 * Date: 2017/4/30
 * Time: 下午9:27
 * Desc: UDP服务器与TCP服务器不同，UDP没有连接的概念。
 * 启动Server后，客户端无需Connect，直接可以向Server监听的9502端口发送数据包。
 * 对应的事件为onPacket
 * 使用netcat连接： netcat -u 127.0.0.1 9502
 *
 * todo 貌似无法判断是否有用户在线，运用场景是什么？
 */
class Udp
{
    /**
     * @var swoole_server
     */
    private $server;

    /**
     * Udp constructor.
     */
    public function __construct()
    {
        $this->server = new swoole_server('127.0.0.1', 9502, SWOOLE_PROCESS, SWOOLE_SOCK_UDP);
        $this->server->on('packet', [$this, 'onPacket']);
        //$this->server->on('close', [$this, 'onClose']);
        $this->server->start();
    }

    /**
     * @param swoole_server $server
     * @param $data
     * @param $clientInfo
     */
    public function onPacket(swoole_server $server, $data, $clientInfo)
    {
        $server->sendto($clientInfo['address'], $clientInfo['port'], "Server " . $data);
    }
}

$udp = new Udp();
