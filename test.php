<?php
header('Content-Type: application/json');
Header('Access-Control-Allow-Methods:GET,POST,PUT,DELETE');
header("Access-Control-Allow-Headers: Content-Type");
header('Access-Control-Allow-Origin:*');   //跨域
require BASE_PATH . '/config/mysql_init.php';
require BASE_PATH  . '/core/Db.php';
require BASE_PATH  . '/core/Utils.php';


use Core\Db;
$db = new Db;
use Core\Utils;
$utils = new Utils;

$redis = new Redis();
$redis->connect('127.0.0.1', 6379);
$redis->select(2);

//判断是否是get请求
if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    // $r=$db->insert_data_userVideoFavorites($pdo,46,15);
    // $r=$db->select_data_by_userid_videoid_userVideoFavorites($pdo,46,16);
    // $r=$db->delete_data_userVideoFavorites($pdo,46,15);
    // $r=$db->select_data_by_title_video($pdo,'秦时',['id','name', 'title', 'form', 'status']);
    // $r=$db->select_data_by_userid_userVideoFavorites($pdo,46);
    $r=$db->select_data_by_userid_youbiao_userVideoFavorites($pdo,46,'2026-10-29 11:40:54');

    var_dump($r);


    // //################################把视频id打乱顺序存入redis
    // $r=$db->select_all_video($pdo,['id']);
    // $ids = array_column($r['data']['data'], 'id');
    // shuffle($ids);
    // $redis->setex('video', 60000, json_encode($ids));    //每天更新音乐列表
    // //##########################################################

    ////################################把音乐id打乱顺序存入redis
    // $r=$db->select_all_music($pdo,['id']);
    // $ids = array_column($r['data']['data'], 'id');
    // shuffle($ids);
    // $redis->setex('music', 60000, json_encode($ids));    //每天更新音乐列表
    ////##########################################################

    // //################################导入视频数据到数据库
    // $csvPath = BASE_PATH.'/assets/video/output6.csv';
    // $fh = fopen($csvPath, 'r');
    // if (!$fh) {
    //     die("打不开 CSV 文件");
    // }
    // // 跳过表头
    // fgetcsv($fh);
    // $data=['name'=>'','title'=>''];
    // while (($row = fgetcsv($fh)) !== false) {
    //     $data['name']  = $row[0];
    //     $data['title'] = $row[1];
    //     $data['form'] = "movies";
    //     var_dump($data);
    //     $db->insert_data_video($pdo,$data);
    //     $data=['name'=>'','title'=>'','form'=>''];
    // }
    // fclose($fh);
    // //##################################################################

    // //##################################################导入音乐数据到数据库
    // $csvPath = BASE_PATH.'/assets/music/music_list.csv';
    // $fh = fopen($csvPath, 'r');
    // if (!$fh) {
    //     die("打不开 CSV 文件");
    // }
    // // 跳过表头
    // fgetcsv($fh);
    // $data=['name'=>'','title'=>'','artist'=>''];
    // while (($row = fgetcsv($fh)) !== false) {
    //     $data['name']  = $row[0];
    //     $data['title'] = $row[1];
    //     $data['artist'] = $row[2];
    //     // $db->insert_data_music($pdo,$data);
    //     $data=['name'=>'','title'=>'','artist'=>''];
    // }
    // fclose($fh);
    // //###################################################################

    $response = array('status' => 'ok','message' => 'get',);
    echo json_encode($response);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $json=file_get_contents('php://input');
    $post_json=json_decode($json,true);
    $response = array('status' => 'ok','message' => 'post',);
    echo json_encode($response);
}

?>