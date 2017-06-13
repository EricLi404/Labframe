<?php
/**
 * Created by PhpStorm.
 * User: leif
 * Date: 2017/6/13
 * Time: 15:56
 */

namespace core;

// 使用配置类和路由类
use core\Config;
use core\Router;

/**
 *  框架启动类
 * Class App
 * @package core
 */
class App
{

    /**
     * 定义一个静态路由实例
     * @var
     */
    public static $router;


    /**
     * 框架启动，分析路由、分发路由。
     */
    private static function run()
    {
//        实例化路由类
        self::$router = new Router();
//        读取配置并设置路由类型
        self::$router->setUrlType(Config::get('Url_type'));
//        获取经路由类处理生成的路由数组
        $url_array = self::$router->getUrlArray();
//        根据数组分发路由
        self::dispath($url_array);
    }

//    路由分发
    private static function dispath()
    {
//        TODO ...
    }

}