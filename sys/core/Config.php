<?php
/**
 * Created by PhpStorm.
 * User: leif
 * Date: 2017/6/14
 * Time: 08:47
 */

namespace core;


/**
 * 配置类
 * Class Config
 * @package core
 */
class Config
{
    /**
     * 存放配置
     * @var array
     */
    private static $config = [];

    /**
     * 读取配置
     * @param null $name 配置名
     * @return array|mixed|null  若存在配置项，则返回配置值，否则返回 null
     */
    public static function get($name = null)
    {
        if (empty($name)) {
            return self::$config;
        }
//        若存在配置项，则返回配置值，否则返回 null
        return isset(self::$config[strtolower($name)]) ? self::$config[strtolower($name)] : null;
    }


    /**
     * 动态设置配置项
     * @param $name  string  配置名
     * @param null $value 配置值
     * @return array 配置数组
     */
    public static function set($name, $value = null)
    {
//        如果是字符串，直接设置;如果是数组，循环遍历设置
        if (is_string($name)) {
            self::$config[strtolower($name)] = $value;
        } elseif (is_array($name)) {
            if (!empty($value)) {
//                TODO 不理解
                self::$config[$value] = isset(self::$config[$value]) ? array_merge(self::$config[$value], $name) : self::$config[$value] = $name;
            } else {
                return self::$config = array_merge(self::$config, array_change_key_case($name));
            }
        } else {
//            配置方式错误，返回当前全部配置
            return self::$config;
        }
    }

    /**
     * 判断是否存在该配置项
     * @param $name string 配置项名
     * @return bool      true：存在   false: 不存在
     */
    public static function has($name)
    {
        return isset(self::$config[strtolower($name)]);
    }

    /**
     * 加载其他配置文件
     * @param $file string 配置文件
     * @return array  配置数组
     */
    public static function load($file)
    {
        if (is_file($file)) {
//            pathinfo(*,PATHINFO_EXTENSION)  返回文件的拓展名  ，具体参考 http://www.w3cschool.cn/php/func-filesystem-pathinfo.html
            $type = pathinfo($file, PATHINFO_EXTENSION);
//            如果拓展名是 php ，则动态设置配置项，反之返回当前全部配置项
            if ($type != 'php') {
                return self::$config;
            } else {
                return self::set(include $file);
            }
        } else {
            return self::$config;
        }
    }

}