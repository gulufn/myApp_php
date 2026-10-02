<?php
header('Content-Type: application/json');
Header('Access-Control-Allow-Methods:GET,POST,PUT,DELETE');
header("Access-Control-Allow-Headers: Content-Type");
header('Access-Control-Allow-Origin:*');   //跨域

require BASE_PATH . '/config/send_email_init.php';
require BASE_PATH . '/config/mysql_init.php';
require BASE_PATH  . '/core/Utils.php';
require BASE_PATH  . '/core/Db.php';
require BASE_PATH  . '/core/Crypto.php';

use Core\Utils;
$utils = new Utils;
use Core\Db;
$db = new Db;
use Core\Crypto;
$crypto = new Crypto;

$redis = new Redis();
$redis->connect('127.0.0.1', 6379);
$redis->select(2);   //保存music列表id使用第2个redis库

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $json=file_get_contents('php://input');
    $response = array('status' => 0,'message' => '初始化');

    $response=$utils->judge_token($json);
    if($response['status']===0){
        echo json_encode($response);    //数据不合法
        exit();
    }
    $token=$response['message'];
    $r=$db->select_data_by_token_users($pdo,$token,['id','token','token_update_time','is_activated']);
    $user_id=$r['data']['id'];
    if($r['code']==0){
        $response = array('status' => 0,'message' => "数据库查询失败");
        echo json_encode($response);    //数据库查询失败
        exit();
    }
    $seconds = time() - strtotime($r['data']['token_update_time']);
    if($seconds > 60*60*24){
        $response = array('status' => 0,'message' =>'token已过期，请重新登录');
        echo json_encode($response);    
        exit();
    }
    if($r['data']['is_activated'] === 0){
        $response = array('status' => 0,'message' =>'账号未激活，请先激活账号');
        echo json_encode($response);    
        exit();
    }
    $index=$response['index'];
    
    $ids=json_decode($redis->get('music'), true);
    if($index >= count($ids)){
        $index=random_int(0, count($ids)-10);
    }
    if($ids === null){
        $response = array('status' => 0,'message' =>'redis中没有music的id数据');
        echo json_encode($response);    
        exit();
    }

    $music_list_ids=array_slice($ids,$index,10);
    $musics=[];
    for($i=0;$i<count($music_list_ids);$i++){
        $r=$db->select_data_by_id_music($pdo,$music_list_ids[$i],['id','name','title','artist']);
        if($r['code']==0){
            $response = array('status' => 0,'message' => "数据库查询失败");
            echo json_encode($response);    //数据库查询失败
            exit();
        }
        $music_id=$r['data']['id'];
        $is_favorite=$db->select_data_by_userid_musicid_userMusicFavorites($pdo,$user_id,$music_id);

        $musics[$i]=$r['data'];
        $musics[$i]['is_favorite']=$is_favorite['data']['data'];
    }
    $key = $crypto->encrypt((string)time());

    $rdata=['key'=>$key,'data'=>$musics];
    $response = array('status' => 1,'message' => "成功",'data'=>$rdata,'index'=>$index);
    echo json_encode($response);
}else{
    $response = array('status' => 0,'message' => '不是post请求');
    echo json_encode($response);
}