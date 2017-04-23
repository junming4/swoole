<?php
/**
 * Email: 2284876299@qq.com
 * User: XiaoJm
 * Date: 2017/4/22
 * Time: 下午3:51
 * Desc: swoole 服务测试
 */


$serverObj = new swoole_server('127.0.0.1', 9501, SWOOLE_BASE, SWOOLE_SOCK_TCP);

$serverObj->set([
    'worker_num' => 4,
     /*设为true ：表示服务端不行展示在命令行中，会直接隐藏掉是不可见得，如果为false在命令行是可以见的*/
    'daemonize' => false,
    'backlog' => 128,
    'log_file' => './tmp/swoole.log'
]);

$serverObj->on('Connect', 'myConnect');
$serverObj->on('Receive', 'myReceiver');
$serverObj->on('Close', 'myClose');

function myConnect(swoole_server $server, $fd, $from_id)
{
    $server->send($fd, "Hello {$fd}!");
}

function myReceiver(swoole_server $serv, $fd, $from_id, $data)
{
    echo "接受数据为: {$fd}:{$data}\n";
    $serv->send($fd, $data . 'test tow');
}

function myClose(swoole_server $serv, $fd, $from_id)
{
    echo "Client {$fd} close connection\n";
}

$serverObj->start();
