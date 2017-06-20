<?php
/**
 * Created by PhpStorm.
 * User: leif
 * Date: 2017/6/11
 * Time: 16:59
 */

//框架启动文件

//定义应用程序目录
define('APP_PATH', ROOT_PATH . 'app' . DS);
//定义缓存文件目录
define('RUNTIME_PATH', ROOT_PATH . 'runtime' . DS);
//定义全局配置文件目录
define('CONFIG_PATH', ROOT_PATH . 'config' . DS);
//定义框架核心目录
define('CORE_PATH', ROOT_PATH . 'sys' . DS . 'core' . DS);


//引入自动加载文件
require CORE_PATH . 'Loader.php';

//实例化自动加载类
$loader = new core\Loader();
//添加命名空间对应的 base 目录
$loader->addNamespace('core', ROOT_PATH . 'sys' . DS . 'core');
$loader->addNamespace('home', ROOT_PATH . 'app' . DS . 'home');
//注册命名空间
$loader->register();

//加载全局配置
\core\Config::set(include CONFIG_PATH . 'config.php');

