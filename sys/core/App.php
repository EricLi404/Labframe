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
    public static function run()
    {
//        实例化路由类
        self::$router = new Router();
//        读取配置并设置路由类型
        self::$router->setUrlType(Config::get('Url_type'));
//        获取经路由类处理生成的路由数组
        $url_array = self::$router-> getRouteUrl();
//        根据数组分发路由
        self::dispath($url_array);
    }


    /**
     * 路由分发
     * 根据路由数组分发到具体的模块、控制器、方法。
     * @param array $url_array 路由数组
     */
    public static function dispath($url_array = [])
    {
        $module = '';               //模块
        $controller = '';           //控制器
        $action = '';               //操作
//        若路由数组中有 module ，设置当前模块为该值；没有的话，设置为默认 module
        if (isset($url_array['module'])) {
            $module = $url_array['module'];
        } else {
            $module = Config::get('default_module');
        }
//        若路由数组中有 controller ，设置当前controller为该值；没有的话，设置为默认 controller
        if (isset($url_array['controller'])) {
            $controller = $url_array['controller'];
        } else {
            $controller = Config::get('default_controller');
        }
//        若路由数组中有 action ，设置当前action 为该值；没有的话，设置为默认的 action
        if (isset($url_array['action'])) {
            $action = $url_array['action'];
        } else {
            $action = Config::get('default_action');
        }
//        拼接控制器文件路径
        $controller_file = APP_PATH . $module . DS . 'controller' . DS . $controller . 'Controller.php';
//        判断控制器文件是否存在，存在继续执行，不存在 die()
        if (file_exists($controller_file)) {
//            引入控制器
            require $controller_file;
//            命名空间字符串示例
            $className = 'module\controller\IndexController';
//            使用字符串替换功能，替换对应的模块名和控制器名
            $className = str_replace('module', $module, $className);
            $className = str_replace('IndexController', $controller . 'Controller', $className);
//            实例化具体的控制器
            $controller = new $className;
//            判断访问的方法是否存在，存在则执行该方法，不存在 die()
            if (method_exists($controller, $action)) {
//                设置方法对应的视图模板
                $controller->setTpl($action);
//                执行该方法
                $controller->$action();
            } else {
                die('The method does not exist.');
            }
        } else {
            die('The controller does not exist.');
        }
    }

}