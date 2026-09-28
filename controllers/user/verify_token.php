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

// $redis = new Redis();
// $redis->connect('127.0.0.1', 6379);
// $redis->select(2);   //token使用第2个redis库

$token = $_GET['token'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $json=file_get_contents('php://input');
    $response = array('status' => 0,'message' => '不要使用post请求');

}else{
    $r=$db->select_data_by_token_users($pdo,$token,['email','token_update_time']);
    if($r['code']==0){
        $response = array('status' => 0,'message' =>'token查询失败');
        echo json_encode($response);    
        exit();
    }
    $seconds = time() - strtotime($r['data']['token_update_time']);
    if($seconds > 3600){
        $response = array('status' => 0,'message' =>'token已过期，请重新登录');
        echo json_encode($response);    
        exit();
    }
    $rdata=['email'=>$r['data']['email']];
    $response = array('status' => 1,'message' => '成功','data'=>$rdata);
    echo json_encode($response);
}





