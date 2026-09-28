<?php
header('Content-Type: application/json');
Header('Access-Control-Allow-Methods:GET,POST,PUT,DELETE');
header("Access-Control-Allow-Headers: Content-Type");
header('Access-Control-Allow-Origin:*');   //跨域

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

    $response=$utils->judgeAll($json);
    if($response['status']==0){
        echo json_encode($response);    //数据不合法
        exit();
    }
    if($utils->judge_pwd($response['pwd'])['status'] == 0){
        echo json_encode(['status' => 0, 'message' => '密码不能为空或密码长度不合法']);    //数据不合法
        exit();
    }

    $email=$response['email'];
    $picture_captcha=$response['picture_captcha'];
    $pwd=$response['pwd'];
    $email_captcha=$response['email_captcha'];
    $key=$response['key'];

    $redis = new Redis();
    $redis->connect('127.0.0.1', 6379);
    $redis->select(1);

    if($picture_captcha != $redis->get($key)){
        $response = array('status' => 0,'message' =>'图片验证码错误，请换一张');
        echo json_encode($response);    
        exit();
    }
    $redis->del($key);
    $pwd_data=$db->select_data_by_meail_users($pdo,$email,['pwd']);
    if($pwd_data['code'] == 0){
        $response = array('status' => 0,'message' =>'查询密码失败');
        echo json_encode($response);    
        exit();
    }
    if(hash('sha256',$pwd.'jiaoyan') != $pwd_data['data']['pwd']){
        $response = array('status' => 0,'message' =>'密码错误');
        echo json_encode($response);    
        exit();
    }
    $token=$utils->get_Token();
    $now = (new DateTime())->format('Y-m-d H:i:s');
    $r = $db->update_data_users($pdo,$email,['token'=>$token,'token_update_time'=>$now]);
    if($r['code']==0){
        $response = array('status' => 0,'message' =>'登录sql失败');
        echo json_encode($response);    
        exit();
    }

    $response = array('status' => 1,'message' =>'登录成功','token'=>$token);
    echo json_encode($response);    
    exit();
    

}else{
    $response = array('status' => 0,'message' => '不是post请求');
    echo json_encode($response);
    exit();
}