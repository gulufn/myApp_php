<?php
// header('Content-Type: application/json');
Header('Access-Control-Allow-Methods:GET,POST,PUT,DELETE');
header("Access-Control-Allow-Headers: Content-Type");
header('Access-Control-Allow-Origin:*');   //跨域
header("Content-Type:image/png");

require BASE_PATH  . '/core/Crypto.php';
use Core\Crypto;
$crypto = new Crypto;


$token = $_GET['token'] ?? '';

$captcha=$crypto->decrypt($token);


function generateCaptcha($c) {
    $img=imagecreate(60,20);
    $color=imagecolorallocate($img,rand(0,255),rand(0,255),rand(0,255));     //画布背景颜色
    $white=imagecolorallocate($img,255,255,255);   //相当于画笔
    imageline($img,rand(0,60),rand(0,20),rand(0,60),rand(0,20),$white);         //画一条白线
    imageline($img,rand(0,60),rand(0,20),rand(0,60),rand(0,20),$white);         //画一条白线
    // imagearc($img,250,100,150,150,0,360,$white);   //画一个圆
    // imagerectangle($img,350,20,500,170,$white);   //画一个正方形

    imagestring($img,5,12,3,$c,$white);
    imagegif($img);
    imagedestroy($img);
}

generateCaptcha($captcha);



?>