<?php
/**
 * Created by PhpStorm.
 * User: leif
 * Date: 2017/6/11
 * Time: 16:59
 */
//框架入口文件

//定义目录分隔符
define('DS', DIRECTORY_SEPARATOR);
//定义框架根目录
define('ROOT_PATH', __DIR__ . DS);
//引入框架启动文件
require 'sys/start.php';
//启动框架
core\App::run();