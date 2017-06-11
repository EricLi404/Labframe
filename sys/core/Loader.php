<?php
/**
 * Created by PhpStorm.
 * User: leif
 * Date: 2017/6/11
 * Time: 17:45
 */

namespace core;


/**
 * Class Loader
 * @package core
 */
class Loader
{


    /**
     * TODO 翻译实验楼的英文注释部分
     * @var array
     */
    protected static $prefixes = [];

    /**
     * 在 SPL 自动加载器栈中注册加载器
     *
     * @return void
     */
    public static function register()
    {
        spl_autoload_register('core\\Loader::loadClass');
    }

    /**
     * 添加命名空间前缀与文件基目录对
     *
     * @param $prefix string 命名空间前缀
     * @param $base_dir string 命名空间中类文件的基目录
     * @param bool $prepend 为 true 时，将基目录插到最前，这将让其第一个被搜索到；否则将被插到最后
     * @return void
     */
    public static function addNamespace($prefix, $base_dir, $prepend = false)
    {
//        规范化命名空间前缀
        $prefix = trim($prefix, '\\') . '\\';
//        规范化文件基目录
        $base_dir = rtrim($base_dir, '/') . DS;
        $base_dir = rtrim($base_dir, DS) . '/';
//        初始化命名空间前缀数组
        if (isset(self::$prefixes[$prefix]) === false) {
            self::$prefixes[$prefix] = [];
        }
//        将命名空间前缀与文件基目录对插入保存数组
        if ($prepend) {
            array_unshift(self::$prefixes[$prefix], $base_dir);
        } else {
            array_push(self::$prefixes[$prefix], $base_dir);
        }

    }

//    TODO 未完待续

}