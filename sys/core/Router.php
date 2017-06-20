<?php
/**
 * Created by PhpStorm.
 * User: leif
 * Date: 2017/6/19
 * Time: 15:57
 */

namespace core;


/**
 * 路由类
 * Class Router
 * @package core
 */
class Router
{
    /**
     * @var  array URL串
     */
    public $url_query;
    /**
     * @var  integer URL模式
     */
    public $url_type;
    /**
     * @var array URL数组
     */
    public $route_url = [];

    /**
     * Router constructor.
     */
    public function __construct()
    {
//        使用 $_SERVER['REQUEST_URI']是取得当前URL的 路径地址。再使用 parse_url 解析 url：主要分为路径信息 [path] 和 参数信息 [query] 两部分。
        $this->url_type = parse_url($_SERVER['REQUEST_URI']);
    }

    /**
     * 设置 URL 模式
     * @param string $url_type URL模式，默认为2（pathinfo 模式）
     */
    public function setUrlType($url_type = 2)
    {
        if ($url_type > 0 && $url_type < 3) {
            $this->url_type = $url_type;
        } else {
            die('The specified URL does not exist!');
        }
    }

    /**
     * 获取经过处理的 URL 数组
     * @return array   URL 数组
     */
    public function getRouteUrl()
    {
        $this->makeUrl();
        return $this->route_url;
    }

    /**
     * 根据 URL 模式的不同选择不同的方式处理 URL 数组
     */
    public function makeUrl()
    {
        switch ($this->url_type) {
            case 1:
                $this->queryToArray();
                break;
            case 2:
                $this->pathinfoToArray();
                break;
        }
    }

    /**
     * 将参数模式的 URL 字符串转换为 URL数组
     */
    public function queryToArray()
    {
//        将 URL 字符串拆分成数组
        $arr = !empty($this->url_query['query']) ? explode('&', $this->url_query['query']) : [];
        $array = $tmp = [];
        if (count($arr) > 0) {
//            将 URL 数组变成键值对的形式
            foreach ($arr as $item) {
                $tmp = explode('=', $item);
                $array[$tmp[0]] = $tmp[1];
            }
//            将 modeule controller action 参数写入 route_url
            if (isset($array['module'])) {
                $this->route_url['module'] = $array['module'];
                unset($array['module']);
            }
            if (isset($array['controller'])) {
                $this->route_url['controller'] = $array['controller'];
                unset($array['controller']);
            }
            if (isset($array['action'])) {
                $this->route_url['action'] = $array['action'];
                unset($array['action']);
            }
//              判断 url action 的后缀，如果有后缀，且后缀合法，则将其去掉
            if (isset($this->route_url['action']) && strpos($this->route_url['action'], '.')) {
                if (explode('.', $this->route_url['action'])[1] != Config::get('url_html_suffix')) {
                    die('suffix error');
                } else {
                    $this->route_url['action'] = explode('.', $this->route_url['action'])[0];
                }
            }
        } else {
            $this->route_url = [];
        }
    }

    /**
     *  将 pathinfo 转换为 URL数组
     */
    public function pathinfoToArray()
    {
//        将 URL 参数字符串 拆分成数组
        $arr = !empty($this->url_query['path']) ? explode('/', $this->url_query['path']) : [];
//        数组长度大于 0 则执行操作，反之返回空 url 数组
        if (count($arr) > 0) {
//            以'index.php'开始
            if ($arr[1] == 'index.php') {
                if (isset($arr[2]) && !empty($arr[2])) {
                    $this->route_url['module'] = $arr[2];
                }
                if (isset($arr[3]) && !empty($arr[3])) {
                    $this->route_url['controller'] = $arr[3];
                }
                if (isset($arr[4]) && !empty($arr[4])) {
                    $this->route_url['action'] = $arr[4];
                }
            } else {
//                直接以'module' 开始
                if (isset($arr[1]) && !empty($arr[1])) {
                    $this->route_url['module'] = $arr[1];
                }
                if (isset($arr[2]) && !empty($arr[2])) {
                    $this->route_url['controller'] = $arr[2];
                }
                if (isset($arr[3]) && !empty($arr[3])) {
                    $this->route_url['action'] = $arr[3];
                }
            }
//            TODO 修改了函数块顺序   https://www.shiyanlou.com/courses/607/labs/2032/document
//              判断 url action 的后缀，如果有后缀，且后缀合法，则将其去掉
            if (isset($this->route_url['action']) && strpos($this->route_url['action'], '.')) {
                if (explode('.', $this->route_url['action'])[1] != Config::get('url_html_suffix')) {
                    die('suffix error');
                } else {
                    $this->route_url['action'] = explode('.', $this->route_url['action'])[0];
                }
            }
        }else{
            $this->route_url = [];
        }
    }

    /*
     * 若服务器开启了rewrite 模块，可以隐藏 index.php。
     * 在本课程中，若要添加 url 后缀名，则必须以 'localhost:8080/index.php' 开头。
     * 若以 'localhost:8080' 开头，则末尾不能添加 '.html' 或 '.php' 等后缀名。
     * */


}