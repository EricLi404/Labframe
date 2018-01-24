<?php

/**
 * Created by PhpStorm.
 * User: leif
 * Date: 2017/6/21
 * Time: 14:25
 */

namespace core\traits;

class Jump
{
    public static function success($msg = '', $url = '')
    {
        echo "<script>alert('$msg');location.href='$url'</script>";
    }

    public static function error($msg = '', $url = '')
    {
        echo "<script>alert('$msg');location.href='$url'</script>";
    }
}