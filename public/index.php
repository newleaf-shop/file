<?php
/**
 * Created by PhpStorm.
 * User: itwri
 * Date: 2019/8/4
 * Time: 16:55
 */
header('Access-Control-Allow-Origin:*'); //支持全域名访问，不安全，部署后需要固定限制为客户端网址
header('Access-Control-Allow-Methods:OPTIONS,GET,POST,PUT,DELETE'); // 允许option，get，post put delete请求
header('Access-Control-Allow-Headers:x-requested-with,content-type,Authorization,Ant-device,ant-device,sign,time,version,User-Token,Vendor-Token'); // 允许x-requested-with请求头

//OPTIONS类型请求直接断掉
if(strtoupper($_SERVER['REQUEST_METHOD']) == 'OPTIONS') {
    exit;
}

require_once __DIR__.'/../../../framework/src/App.php';

use \Jasmine\App;
use \Jasmine\helper\Config;
App::init()->web(function(App $app){
    /**
     * 项目目录
     */
    Config::set('PATH_APPS',__DIR__."/../app");

    /**
     * 全局配置目录
     */
    Config::set('PATH_CONFIG',__DIR__."/../config");

    /**
     * 缓存以及编译文件目录
     */
    Config::set('PATH_RUNTIME',__DIR__."/../runtime");
});