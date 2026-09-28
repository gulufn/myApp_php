<?php
header('Content-Type: application/json');
Header('Access-Control-Allow-Methods:GET,POST,PUT,DELETE');
header("Access-Control-Allow-Headers: Content-Type");
header('Access-Control-Allow-Origin:*');   //跨域

session_start();

require BASE_PATH  . '/core/Crypto.php';
require BASE_PATH  . '/core/Utils.php';
use Core\Crypto;
use Core\Utils;
$crypto = new Crypto;
$utils = new Utils;

$redis = new Redis();
$redis->connect('127.0.0.1', 6379);
$redis->select(1);   //注册页面保存图片验证码使用第一个redis库

//图片的验证码，4位数
$captcha=$utils->randomNumber(4);
//图片验证码，加密
$token=$crypto->encrypt($captcha);
$url='/user/picture_captcha_img?token='.$token;

$key=hash('sha256',time().session_id());
$redis->set($key, $captcha, ['ex' => 600]);


//判断是否是get请求
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    //睡眠，防止恶意刷验证码消耗服务器资源 
    $response = array(
        'status' => 'ok',
        'message' => $url,
        'key' =>$key
    );

    echo json_encode($response);

}else{
    $response = array(
        'status' => 'fail',
        'message' => '请求方式错误',
        'key' => ''
    );
    echo json_encode($response);
}


?>