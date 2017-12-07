<?php

return [
    // 视图输出字符串内容替换
    'view_replace_str'       => [
        '__CSS__'    => STATIC_PATH . 'home/css',
        '__JS__'     => STATIC_PATH . 'home/js',
        '__IMG__'    => STATIC_PATH . 'home/images',
        '__PLUG__'    => STATIC_PATH . 'home/plug',
        '__UPLOADS__'=> STATIC_PATH . 'uploads/'
    ],

    //验证码

    'captcha'  => [
        // 验证码字符集合
        'codeSet'  => '2345678abcdefhijkmnpqrstuvwxyzABCDEFGHJKLMNPQRTUVWXY', 
        // 验证码字体大小(px)
        'fontSize' => 50,
        // 是否画混淆曲线
        'useCurve' => false,
         // 验证码图片高度
        'imageH'   => 30,
        // 验证码图片宽度
        'imageW'   => 120,
        // 验证码位数
        'length'   => 5,
        // 验证成功后是否重置        
        'reset'    => true
    ],

    //伪静态
    'url_html_suffix' => false,
    // 'user_auth_key'     => 'Astonep@tp-admin!@#$',
];