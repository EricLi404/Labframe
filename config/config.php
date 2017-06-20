<?php
/**
 * Created by PhpStorm.
 * User: leif
 * Date: 2017/6/11
 * Time: 16:58
 */
return [
//    数据库相关配置
    'db_host' => '',
    'db_user' => '',
    'db_password' => '',
    'db_name' => '',
    'db_table_prefix' => '',
    'db_charset' => '',

//    默认模块
    'default_module' => 'home',
//    默认控制器
    'default_controller' => 'Index',
//    默认方法（操作）
    'default_action' => 'index',
//    URL 模式  {1：普通模式，采用传统的 url 参数模式；2：PATHINFO 模式，也是默认模式}
    'url_type' => '2',

//    缓存存放路径
    'cache_path' => RUNTIME_PATH . 'cache' . DS,
//    缓存文件前缀
    'cache_prefix' => 'cache_',
//    缓存类型（只实现 file 类型）
    'cache_type' => 'file',
//    编译文件存放路径
    'compile_path' => RUNTIME_PATH . 'compile' . DS,
//    模板路径
    'view_path' => APP_PATH . 'home' . DS . 'view' . DS,
//    模板后缀
    'view_suffix' => '.php',

//  开启自动缓存
    'auto_cache' => true,
//    URL伪静态后缀
    'url_html_suffix' => 'html',

];