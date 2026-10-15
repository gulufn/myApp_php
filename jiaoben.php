<?php
require '/var/www/html/config/mysql_init.php';         //定时任务脚本必须用绝对路径
require '/var/www/html/core/Db.php';


use Core\Db;
$db = new Db;

$redis = new Redis();
$redis->connect('127.0.0.1', 6379);
$redis->select(2);   //音乐和视频列表使用第2个redis库


//更新音乐列表
$r=$db->select_all_music($pdo,['id']);
$ids = array_column($r['data']['data'], 'id');
shuffle($ids);
$redis->setex('music', 60000, json_encode($ids));    //每天更新音乐列表

//更新视频列表
$r=$db->select_all_video($pdo,['id']);
$ids = array_column($r['data']['data'], 'id');
shuffle($ids);
$redis->setex('video', 60000, json_encode($ids));