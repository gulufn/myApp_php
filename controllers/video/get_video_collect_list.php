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

    $youbiao = $response['youbiao'];
    if($youbiao == '0'){
        $r=$db->select_data_by_userid_userVideoFavorites($pdo,$user_id);
        if($r['code'==0]){
            $response = array('status' => 0,'message' => "数据查询失败");
            echo json_encode($response);
        }
        $video_collecp=[];
        $n=0;
        foreach ($r['data'] as $i){
            $r1=$db->select_data_by_id_video($pdo,$i['video_id'],['id','name', 'title', 'form', 'status']);

            $r1['data']['created_at'] = $i['created_at'];
            $r1['data']['collectd_id'] = $i['id'];
            $r1['data']['is_favorite'] = true;
            $video_collecp[$n]=$r1['data'];
            $n++;
        }
        $key = $crypto->encrypt((string)time());
        $response = array('status' => 1,'message' => "成功",'key'=>$key,'data'=>$video_collecp);
        echo json_encode($response);
    }else{
        $r=$db->select_data_by_userid_youbiao_userVideoFavorites($pdo,$user_id,$youbiao);
        if($r['code'==0]){
            $response = array('status' => 0,'message' => "数据查询失败");
            echo json_encode($response);
        }

        $video_collecp=[];
        $n=0;
        foreach ($r['data']['data'] as $i){
            $r1=$db->select_data_by_id_video($pdo,$i['video_id'],['id','name', 'title', 'form', 'status']);

            $r1['data']['created_at'] = $i['created_at'];
            $r1['data']['collectd_id'] = $i['id'];
            $r1['data']['is_favorite'] = true;
            $video_collecp[$n]=$r1['data'];
            $n++;
        }
        $key = $crypto->encrypt((string)time());
        $response = array('status' => 1,'message' => "成功",'key'=>$key,'data'=>$video_collecp);
        echo json_encode($response);
    }
}else{
    $response = array('status' => 0,'message' => '不是post请求');
    echo json_encode($response);
}








