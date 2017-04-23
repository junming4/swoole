<?php
/**
 * Email: 2284876299@qq.com
 * User: XiaoJm
 * Date: 2017/4/23
 * Time: 上午12:48
 * Desc: 普通服务
 */

$commServerObj = new swoole_server('127.0.0.1', 9501, SWOOLE_BASE, SWOOLE_SOCK_TCP);
$commServerObj->on('Start', 'onStart');
