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

    '/music/get_music_list' => 'controllers/music/get_music_list.php',
    '/music/toggle_favorites' => 'controllers/music/toggle_favorites_music.php',
    '/music/search_music' => 'controllers/music/search_music.php',
    '/video/get_video_list' => 'controllers/video/get_video_list.php',
    '/video/toggle_favorites' => 'controllers/video/toggle_favorites_video.php',

    
    '/test' => '/test.php',
]);