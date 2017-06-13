<?php
/**
 * Created by PhpStorm.
 * User: leif
 * Date: 2017/6/11
 * Time: 17:45
 */

namespace core;


/** 自动加载类
 * Class Loader
 * @package core
 */
class Loader
{


    /**
     * 一个关联数组————命名空间前缀与文件基目录对
     * @key 命名空间前缀
     * @value 该命名空间中类文件的基目录
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

    /**
     * 根据类名载入对应类文件
     *
     * @param $class string 包括命名空间前缀的完整的类名
     * @return bool|void 成功载入则返回载入的文件名，否则返回 bool false
     */
    public static function loadClass($class)
    {
//        当前命名空间前缀
        $prefix = $class;
//
        while (false !== $pos = strrpos($prefix, '\\')) {
//            保留命名空间前缀中尾部的分隔符
            $prefix = substr($class, 0, $pos + 1);
//            剩余的就是相对类名称
            $relative_class = substr($class, $pos + 1);
//            利用命名空间前缀和相对类名称来加载映射文件
            $mapped_file = self::loadMappedFile($prefix, $relative_class);
//            成功加载，返回文件
            if ($mapped_file) {
                return $mapped_file;
            }
//            删除命名空间前缀尾部的分割符，以便于下一次strtops()迭代
            $prefix = rtrim($prefix, '\\');
        }
//        找不到映射文件
        return false;
    }

    /**
     * 根据命名空间和相应的类来加载映射文件
     * @param $prefix string 命名空间前缀
     * @param $relative_class string 相对应的类名
     * @return bool|string     false：映射文件不能被加载或者已经被加载     file ：加载的文件
     */
    public static function loadMappedFile($prefix, $relative_class)
    {
//    判断命名空间前缀中是否有文件基目录,没有则返回false
        if (isset(self::$prefixes[$prefix]) === false) {
            return false;
        }
//        遍历命名空间前缀的基目录
        foreach (self::$prefixes[$prefix] as $base_dir) {
//            用 base 目录替代命名空间前缀
//            用目录分隔符 'DS' 来替换命名空间分隔符，追加'.php',造成 $file 的绝对路径
            $file = $base_dir . str_replace('\\', DS, $relative_class) . '.php';
//            TODO  为什么要换两次？？
            $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
//            文件存在时，载入
            if (self::requireFile($file)){
                return $file;
            }
        }
//        文件不存在，返回 false
        return false;
    }

    /**
     * 载入文件
     * @param $file string 需要载入的文件路径
     * @return bool 文件存在：true；文件不存在：false
     */
    public static function requireFile($file){
        if (file_exists($file)){
            require $file;
            return true;
        }
        return false;
    }

}