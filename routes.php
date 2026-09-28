<?php
$router->define([
    '/'      => 'controllers/index.php',
    '/video' => 'controllers/video.php',
    '/music' => 'controllers/music.php',
    '/user' => 'controllers/user.php',
    '/user/get_picture_captcha' => 'controllers/user/get_picture_captcha.php',
    '/user/picture_captcha_img' => 'controllers/user/picture_captcha_img.php',
    '/user/get_email_captcha' => 'controllers/user/get_email_captcha.php',
    '/user/register' => 'controllers/user/register.php',
    '/user/verify_token' => 'controllers/user/verify_token.php',
    '/user/login' => 'controllers/user/login_by_pwd.php',
    '/user/login_by_email' => 'controllers/user/login_by_email.php',
    '/test' => '/test.php',
]);