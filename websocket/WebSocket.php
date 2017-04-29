<?php

/**
 * Email: 2284876299@qq.com
 * User: XiaoJm
 * Date: 2017/4/30
 * Time: 上午12:54
 * Desc: webSocket 数据
 */
class WebSocket
{
    /**
     * @var swoole_websocket_server
     */
    private $server;

    /**
     * WebSocket constructor.
     */
    public function __construct()
    {
        $this->server = new swoole_websocket_server("0.0.0.0", 9502, SWOOLE_BASE, SWOOLE_SOCK_TCP);
        $this->server->on('open', [$this, 'onOpen']);
        $this->server->on('message', [$this, 'onMessage']);
        $this->server->on('close', [$this, 'onClose']);
    }

    /**
     * @param swoole_websocket_server $server
     * @param $request
     */
    public function onOpen(swoole_websocket_server $server, $request)
    {
        echo "server: handshake success with fd{$request->fd}\n";
    }

    /**
     * @param swoole_websocket_server $server
     * @param $frame
     */
    public function onMessage(swoole_websocket_server $server, $frame)
    {
        echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";
        $server->push($frame->fd, "this is server");
    }

    /**
     * @param swoole_websocket_server $server
     * @param $fd
     */
    public function onClose(swoole_websocket_server $server, $fd)
    {
        echo "client {$fd} closed\n";
    }
}
