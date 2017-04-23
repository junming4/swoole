<?php

/**
 * Email: 2284876299@qq.com
 * User: XiaoJm
 * Date: 2017/4/22
 * Time: 上午10:03
 * Desc: 客户端
 */
class Client
{
    private $client;
    public function __construct()
    {
        $this->client = new swoole_client(SWOOLE_SOCK_TCP);
    }

    public function connect()
    {
        if (!$this->client->connect("127.0.0.1", 9501, 1)) {
            echo "Error: {$this->client->errMsg}[{$this->client->errCode}]\n";
        }
        fwrite(STDOUT, "请输入消息：");
        $msg = trim(fgets(STDIN));
        $this->client->send($msg);
        $message = $this->client->recv();
        echo "Get Message From Server:{$message}\n";
    }
}
$client = new Client();
$client->connect();
