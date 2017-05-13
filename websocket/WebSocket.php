<?php

/**
 * Email: 2284876299@qq.com
 * User: XiaoJm
 * Date: 2017/4/30
 * Time: 上午12:54
 * Desc: webSocket 数据
 */
define('ROOT_PATH', dirname(__DIR__));
require_once(ROOT_PATH.'/predis/autoload.php');

class WebSocket
{
    /**
     * @var swoole_websocket_server
     */
    private $server;

    private $redis;

    const REDIS_KEY_PREFIX = 'web_im_';

    const REDIS_TO_FD_ID = 'redis_fd_key_';

    /**
     * WebSocket constructor.
     */
    public function __construct()
    {
        $this->redis = new Predis\Client([
            'scheme' => 'tcp',
            'host' => '127.0.0.1',
            'port' => 6379,
            'password' => '123456',
            'database' => 0
        ]);

        $this->server = new swoole_websocket_server("0.0.0.0", 9502, SWOOLE_BASE, SWOOLE_SOCK_TCP);
        $this->server->on('open', [$this, 'onOpen']);
        $this->server->on('message', [$this, 'onMessage']);
        $this->server->on('close', [$this, 'onClose']);
        $this->server->start();
    }

    /**
     * @param swoole_websocket_server $server
     * @param $request
     */
    public function onOpen(swoole_websocket_server $server, $request)
    {
        //file_put_contents('/Users/laraveljun/xiao/site/swoole_test.io/tmp/sms.log', json_encode($request), FILE_APPEND);
        echo "{$request->get['from_id']}server: handshake success with fd{$request->fd}\n";

        $this->redis->set(self::REDIS_TO_FD_ID.$request->fd, $request->get['from_id']);
        $this->redis->sadd(self::REDIS_KEY_PREFIX.$request->get['from_id'], array($request->fd));

    }

    /**
     * @param swoole_websocket_server $server
     * @param $frame
     */
    public function onMessage(swoole_websocket_server $server, $frame)
    {
        echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";



        file_put_contents('/Users/laraveljun/xiao/site/swoole_test.io/tmp/sms.log', json_encode($frame), FILE_APPEND);

        $from_id = $from_id = $this->redis->get(self::REDIS_TO_FD_ID.$frame->fd);
        $allUser = $this->redis->smembers(self::REDIS_KEY_PREFIX.$from_id);

        if(!is_array($allUser)) $allUser = [];

        //广播所有在线的
        foreach ($allUser as $conn) {
            if($conn != $frame->fd){
                $server->push($conn, $frame->fd."发来的信息为:".$frame->data);
            }

        }

        //$server->push($frame->fd, "this is server");
    }

    /**
     * @param swoole_websocket_server $server
     * @param $fd
     */
    public function onClose(swoole_websocket_server $server, $fd)
    {
        echo "client {$fd} closed\n";

        $from_id = $this->redis->get(self::REDIS_TO_FD_ID.$fd);
        $this->redis->srem(self::REDIS_KEY_PREFIX.$from_id, $fd);
        $this->redis->del(self::REDIS_TO_FD_ID.$fd);
        //$this->redis->sadd(self::REDIS_KEY_PREFIX.$request->get['from_id'], array($request->fd));

    }
}

$webSocket = new WebSocket();
