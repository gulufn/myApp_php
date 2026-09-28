<?php
header('Content-Type: application/json');
Header('Access-Control-Allow-Methods:GET,POST,PUT,DELETE');
header("Access-Control-Allow-Headers: Content-Type");
header('Access-Control-Allow-Origin:*');   //跨域

require BASE_PATH . '/config/send_email_init.php';
require BASE_PATH . '/config/mysql_init.php';
require BASE_PATH  . '/core/Utils.php';
require BASE_PATH  . '/core/Db.php';

use Core\Utils;
$utils = new Utils;
use Core\Db;
$db = new Db;


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $json=file_get_contents('php://input');
    $response = array('status' => 0,'message' => '初始化');

    $response=$utils->judgeEmail($json);
    if($response['status']===0){
        echo json_encode($response);    //数据不合法
        exit();
    }
    $email=$response['message'];                                  //收件人邮箱
    $select_email=$db->select_data_by_meail_users($pdo,$email,['email']);
    //有查到这个邮箱
    if($select_email['code']==0){
        $response = array('status' => 0,'message' => '查询sql失败');
        echo json_encode($response);
        exit();
    }
    //邮箱已发过验证码
    if($select_email['data']){
        $email_captcha_time= $db->select_data_by_meail_users($pdo,$email,['captcha_update_time']);
        if($email_captcha_time['code']=='0'){
            $response=array('status' => 0,'message' => '查询sql执行失败');
            echo json_encode($response);
            exit();
        }
        $seconds = time() - strtotime($email_captcha_time['data']['captcha_update_time']);
        if($seconds<60){
            $response=array('status' => 0,'message' => '获取频率过高，1分钟后重试');
            echo json_encode($response);
            exit();
        }
        $captcha=$utils->randomNumber(4);
        $send_count='您的验证码为 '.$captcha.',若非本人操作请忽略此消息';
        $nickname='诺言非';                                                 //发件人对外的昵称
        
        $data = send_to_email($send_count,$nickname,$email);          //发送邮件
        if($data==0){
            $response=array('status' => 0,'message' => '邮件发送失败');
            echo json_encode($response);
            exit();
        }
        if($data==1){
            $now = (new DateTime())->format('Y-m-d H:i:s');
            $r=$db->update_data_users($pdo,$email,['captcha'=>$captcha,'captcha_update_time'=>$now]);
            if($r['code']==0){
                $response=array('status' => 0,'message' => '更新sql执行失败');
                echo json_encode($response);
                exit();
            }
            $response=array('status' => 1,'message' => '邮箱验证码更新成功');
            echo json_encode($response);
            exit();
        }

    }else{
        $captcha=$utils->randomNumber(4);
        $send_count='您的验证码为 '.$captcha.',若非本人操作请忽略此消息';
        $nickname='诺言非';                                                 //发件人对外的昵称
        
        $data = send_to_email($send_count,$nickname,$email);          //发送邮件

        if($data===0){             //发送失败
            $response=array('status' => 0,'message' => '邮件发送失败');
            echo json_encode($response);
            exit();
        }
        if($data===1){
            $now = (new DateTime())->format('Y-m-d H:i:s');
            $r=$db->insert_data_users($pdo,['email'=>$email,'captcha'=>$captcha,'captcha_update_time'=>$now]);
            if($r['code']==1){
                $response=array('status' => 1,'message' => '插入成功');
                echo json_encode($response);
                exit();
            }else if($r==0){
                $response=array('status' => 1,'message' => '插入失败');
                echo json_encode($response);
                exit();
            }
        }
    }
}else{
    $response = array('status' => 0,'message' => '不是post请求');
    echo json_encode($response);
}