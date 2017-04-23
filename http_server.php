<?php
/**
 * Email: 2284876299@qq.com
 * User: XiaoJm
 * Date: 2017/4/22
 * Time: 下午4:47
 * Desc: 请求server
 */

//访问直接 http://127.0.0.1:9501 去访问

$httpServer = new swoole_http_server('127.0.0.1', 9501);
$httpServer->set([
    'worker_num' => 4,
    /*设为true ：表示服务端不行展示在命令行中，会直接隐藏掉是不可见得，如果为false在命令行是可以见的*/
    'daemonize' => false,
    'backlog' => 128,
    'log_file' => './tmp/swoole.log'
]);
$httpServer->on('request', function ($request, $response) {
    $response->header("Content-Type", "text/html; charset=utf-8");
    $response->end("<h1>Hello Swoole. #".rand(1000, 9999)."</h1>");
});
$httpServer->start();
