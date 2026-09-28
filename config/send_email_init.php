<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


require_once BASE_PATH . '/PHPMailer/src/Exception.php';
require_once BASE_PATH . '/PHPMailer/src/PHPMailer.php';
require_once BASE_PATH . '/PHPMailer/src/SMTP.php';



//给指定的邮箱发消息,参数：发件人邮箱，发件人邮箱密码(授权码)，要发送的消息，发件人对外的昵称，收件人邮箱
function send_to_email($send_count,$nickname,$receive_email){
    $send_out_email='fnn_0506@163.com';      //发件人邮箱
    $send_out_email_pwd='WUfJDfW37KFKVq4i';    //发件人邮箱密码(授权码)

    $mail2 = new PHPMailer(true);                              // Passing `true` enables exceptions
    try {
        //服务器配置
        $mail2->CharSet ="UTF-8";                     //设定邮件编码
        $mail2->SMTPDebug = 0;                        // 调试模式输出
        $mail2->isSMTP();                             // 使用SMTP
        $mail2->Host = 'smtp.163.com';                // SMTP服务器
        $mail2->SMTPAuth = true;                      // 允许 SMTP 认证
        $mail2->Username = $send_out_email;                // SMTP 用户名  即邮箱的用户名
        $mail2->Password = $send_out_email_pwd;             // SMTP 密码  部分邮箱是授权码(例如163邮箱)
        $mail2->SMTPSecure = 'ssl';                    // 允许 TLS 或者ssl协议
        $mail2->Port = 465;                            // 服务器端口 25 或者465 具体要看邮箱服务器支持

        $mail2->setFrom($send_out_email, $nickname);  //发件人
        $mail2->addAddress($receive_email,'用户');  // 收件人
        $mail2->addReplyTo($send_out_email, '测试'); //回复的时候回复给哪个邮箱 建议和发件人一致
        $mail2->Subject = '【验证码】';
        $mail2->Body    = $send_count;

        $mail2->send();
        return 1;
    } catch (Exception $e) {
        return 0;
        // echo '邮件发送失败: ', $mail2->ErrorInfo;
    }
}
// $send_out_email='fnn_0506@163.com';      //发件人邮箱
// $send_out_email_pwd='WUfJDfW37KFKVq4i';    //发件人邮箱密码(授权码)
// $send_out_email='2427199500@qq.com';      //发件人邮箱
// $send_out_email_pwd='hbgepqioutrfeceb';    //发件人邮箱密码(授权码)


// $send_out_email='2183180991@qq.com';      //发件人邮箱
// $send_out_email_pwd='srnymnubqeaoebdd';    //发件人邮箱密码(授权码)
// $send_count='您的验证码为 45415 ,若非本人操作请忽略此消息';    //要发送的消息
// $nickname='诺言非';                                        //发件人对外的昵称
// $receive_email='2427199500@qq.com';                          //收件人邮箱

// send_to_email($send_out_email,$send_out_email_pwd,$send_count,$nickname,$receive_email);


?>
