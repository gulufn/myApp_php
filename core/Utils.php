<?php

namespace Core;

class Utils
{
    //输出$length长度的随机数字符串
    public static function randomNumber(int $length):string{
        $captcha='';
        for($i=0;$i<$length;$i++){
            $captcha=$captcha.(string)mt_rand(0,9);
        }
        return $captcha;
    }
    //验证邮箱是否合法
    public static function judgeEmail(string $json){
        $post_json=json_decode($json,true);
        if (!is_array($post_json) || array_is_list($post_json)) {
            $response=array('status' => 0, 'message' => 'JSON 格式错误');
            return $response;
        }
        $email = $post_json['email'] ?? null;
         if($email === null || !is_string($email) || trim($email) === ''){
            $response=array('status' => 0, 'message' => 'email不能为空');
            return $response;
        }
        $email = trim($email);
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $response=array('status' => 0, 'message' => 'email不合法');
            return $response;
        }
        return array('status' => 1, 'message' => $email);
    }
    //验证邮箱、图片验证码、邮箱验证码key,是否合法
    public static function judgeAll(string $json){
        $post_json=json_decode($json,true);
        if (!is_array($post_json) || array_is_list($post_json)) {
            $response=array('status' => 0, 'message' => 'JSON 格式错误');
            return $response;
        }
        $email = $post_json['email'] ?? null;
        if($email === null || !is_string($email) || trim($email) === ''){
            $response=array('status' => 0, 'message' => 'email不能为空');
            return $response;
        }
        $email = trim($email);
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $response=array('status' => 0, 'message' => 'email不合法');
            return $response;
        }
        $picture_captcha=$post_json['picture_captcha'] ?? null;
        if($picture_captcha === null || !is_string($picture_captcha) || trim($picture_captcha) === '' || mb_strlen($picture_captcha) >32){
            $response=array('status' => 0, 'message' => '验证码不能为空或验证码不合法');
            return $response;
        }
        $pwd=$post_json['pwd'] ?? null;
        $email_captcha=$post_json['email_captcha'] ?? null;
        $key = $post_json['key'] ?? null;
        if($key === null || !is_string($key) || trim($key) === '' || mb_strlen($key) >128 || mb_strlen($key) < 4){
            $response=array('status' => 0, 'message' => 'key不合法');
            return $response;
        }
        return array('status' => 1, 'email' => $email,'picture_captcha' => $picture_captcha,'pwd' => $pwd,'email_captcha' => $email_captcha,'key' => $key);
    }
    //验证密码是否合法
    public static function judge_pwd(string $pwd){
        if($pwd === null || !is_string($pwd) || trim($pwd) === '' || mb_strlen($pwd) >32 || mb_strlen($pwd) < 6){
            $response=array('status' => 0, 'message' => '密码不能为空或密码长度不合法');
            return $response;
        }
        $response=array('status' => 1, 'message' => $pwd);
        return $response;
    }
    //验证邮箱验证码是否合法
    public static function judge_email_captcha(string $email_captcha){
        if($email_captcha === null || !is_string($email_captcha) || trim($email_captcha) === '' || mb_strlen($email_captcha) >32 || mb_strlen($email_captcha) < 4){
            $response=array('status' => 0, 'message' => '邮箱验证码不能为空或验证码长度不合法');
            return $response;
        }
        $response=array('status' => 1, 'message' => $email_captcha);
        return $response;
    }
    //生成token
    public static function get_Token(int $randomLength = 16): string{
        // 前段：时间戳哈希
        $timePart = hash('sha256', (string)microtime(true));

        // 后段：随机字符
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $max = strlen($chars) - 1;
        $randPart = '';
        for ($i = 0; $i < $randomLength; $i++) {
            $randPart .= $chars[random_int(0, $max)];
        }
        return $timePart . $randPart;
    }

    public static function judge_token(string $json): array {
        $post_json=json_decode($json,true);
        if (!is_array($post_json) || array_is_list($post_json)) {
            $response=array('status' => 0, 'message' => 'JSON 格式错误');
            return $response;
        }
        $token = $post_json['token'] ?? null;
         if($token === null || !is_string($token) || trim($token) === ''){
            $response=array('status' => 0, 'message' => 'token不能为空');
            return $response;
        }
        $index=$post_json['index'] ?? 0;
        if(!is_numeric($index)){
            $index=0;
        }
        $youbiao=$post_json['youbiao'] ?? '0';
        $is_favorite=$post_json['is_favorite'] ?? null;
        $music_id=$post_json['music_id'] ?? null;
        $video_id=$post_json['video_id'] ?? null;
        $title=$post_json['keyword'] ?? '';
        return array('status' => 1, 'message' => $token, 'index' => $index, 'youbiao'=>$youbiao,'is_favorite' => $is_favorite, 'music_id' => $music_id,'title'=>$title,'video_id'=>$video_id);
    }

}






