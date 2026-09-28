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
    if($utils->judge_email_captcha($response['email_captcha'])['status'] == 0){
        echo json_encode(['status' => 0, 'message' => '邮箱验证码不能为空或验证码长度不合法']);    //数据不合法
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
    $captcha_data=$db->select_data_by_meail_users($pdo,$email,['captcha','captcha_update_time','token']);
    if($captcha_data['data']['token'] != ''){
        $response = array('status' => 0,'message' =>'当前邮箱已被注册过了');
        echo json_encode($response);    
        exit();
    }
    if($captcha_data['code']==0){
        $response = array('status' => 0,'message' =>'查询验证码失败');
        echo json_encode($response);    
        exit();
    }
    $seconds = time() - strtotime($captcha_data['data']['captcha_update_time']);
    if($seconds>90){
        $response = array('status' => 0,'message' =>'邮箱验证码过期，请重新获取验证码');
        echo json_encode($response);    
        exit();
    }
    if($email_captcha != $captcha_data['data']['captcha']){
        $response = array('status' => 0,'message' =>'邮箱验证码错误');
        echo json_encode($response);    
        exit();
    }
    $pwd=hash('sha256',$pwd.'jiaoyan');
    $token=$utils->get_Token();
    $now = (new DateTime())->format('Y-m-d H:i:s');
    if($email_captcha == $captcha_data['data']['captcha']){
        $r = $db->update_data_users($pdo,$email,['pwd'=>$pwd,'token'=>$token,'token_update_time'=>$now,'register_time'=>$now]);
        if($r['code']==0){
            $response = array('status' => 0,'message' =>'注册sql失败');
            echo json_encode($response);    
            exit();
        }

        $response = array('status' => 1,'message' =>'注册成功','token'=>$token);
        echo json_encode($response);    
        exit();
    }
    
}else{
    $response = array('status' => 0,'message' => '不是post请求');
    echo json_encode($response);
}




?>