<?php
/**
 * Created by PhpStorm.
 * User: leif
 * Date: 2017/6/19
 * Time: 18:04
 */

namespace core;

use core\View;

/**
 * 控制器类
 * 所有的控制器集成该类
 * Class Controller
 * @package core
 */
class Controller
{
    /**
     * @var array  模板变量
     */
    protected $vars = [];
    /**
     * @var string 视图模板
     */
    protected $tpl;

    /**
     * 变量赋值
     * @param $name string|array 变量键值对数组或者单个变量名
     * @param string $value 单个变量值
     * @return $this
     */
    final protected function assign($name, $value = '')
    {
        if (is_array($name)) {
            $this->vars = array_merge($this->vars, $name);
            return $this;
        } else {
            $this->vars['name'] = $value;
        }
    }

    /**
     * 模板设置
     * @param string $tpl
     */
    final public function setTpl($tpl = '')
    {
        $this->tpl = $tpl;
    }

    /**
     *  视图设置
     *  调用视图类的 display 方法显示视图模板文件
     */
    final protected function display()
    {
        $view = new View($this->vars);
        $view->display($this->tpl);
    }


}