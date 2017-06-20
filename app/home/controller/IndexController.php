<?php
/**
 * Created by PhpStorm.
 * User: leif
 * Date: 2017/6/20
 * Time: 18:19
 */

namespace home\controller;
use core\Controller;

class IndexController extends Controller
{
    public function index(){
        $this->display();
    }
}