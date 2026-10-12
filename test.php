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

    $r=$db->select_all_music($pdo,['id']);
    $ids = array_column($r['data']['data'], 'id');
    shuffle($ids);
    $redis->setex('music', 60000, json_encode($ids));

    // echo time();

    // $data=$redis->get('music');
    // $cached = json_decode($redis->get('music'), true);
    // var_dump($cached);


    // $r=$db->select_data_by_title_music($pdo,'清',['id','name','title','artist']);
    // var_dump($r);



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






    // var_dump();

    // $response = array('status' => 'ok','message' => 'get',);
    // echo json_encode($response);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $json=file_get_contents('php://input');
    $post_json=json_decode($json,true);
    $response = array('status' => 'ok','message' => 'post',);
    echo json_encode($response);
}

?>