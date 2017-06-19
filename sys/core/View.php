<?php
/**
 * Created by PhpStorm.
 * User: leif
 * Date: 2017/6/19
 * Time: 18:35
 */

namespace core;


class View
{
    /**
     * @var array  模板变量
     */
    public $vars = [];

    /**
     * View constructor.
     * @param array $vars
     */
    public function __construct(array $vars = [])
    {
//        判断目录是否存在
        if (!is_dir(Config::get('cache_path')) || !is_dir(Config::get('compile_path')) || !is_dir(Config::get('view_path'))) {
            die('cache or compile or view path does nor exist');
        }
        $this->vars = $vars;
    }

    /**
     * 模板展示方法
     * @param $file string 模板文件名
     * @return mixed
     */
    public function display($file)
    {
//       获取模板文件，判空
        $tpl_file = Config::get('view_path') . $file . Config::get('view_suffix');
        if (!file_exists($tpl_file)) {
            die('View template does not exist');
        }
//        编译文件， MD5文件名 + 文件名
        $parser_file = Config::get('compile_path') . md5($file) . $file . '.php';
//        缓存文件， 缓存前缀 + 文件名
        $cache_file = Config::get('cache_path') . Config::get('cache_prefix') . $file . '.html';
//        判断是否开启了自动缓存 , 如果开启，且存在符合条件的缓存文件，引入缓存文件
        if (Config::get('auto_cache')) {
            if (file_exists($cache_file) && file_exists($parser_file)) {
                if (filemtime($cache_file) >= filemtime($parser_file) && filemtime($parser_file) >= filemtime($tpl_file)) {
                    return include $cache_file;
                }
            }
        }
//        判断是否需要重新编译模板
        if (!file_exists($parser_file) || filemtime($tpl_file) > filemtime($parser_file)) {
            $parser = new Parser($tpl_file);
            $parser->compile($parser_file);
        }
//        引入编译文件
        include $parser_file;
//        如果开启了自动缓存，缓存模板
        if (Config::get('auto_cache')) {
            file_put_contents($cache_file, ob_get_contents());
            ob_end_clean();
        }
        /*
         * 上面的逻辑也挺简单。
         * 调用 display 方法需要传入一个模板文件名，然后根据传入的文件名到视图目录去寻找是否存在该模板，若不存在，退出程序。
         * 若存在，定义对应的编译文件和缓存文件。接下来判断在配置选项中是否开启了自动缓存：
         * 若开启了缓存，若对应的缓存文件存在且编译文件也存在，若缓存的文件的最后修改时间大于对应的编译文件且编译文件的最后修改时间大于模板文件的修改时间，则表明缓存的文件是最新的内容，直接可以引入缓存文件，函数返回。
         * 若不满足使用缓存文件的条件，则向下执行。若编译文件不存在或编译文件存在但是最后修改时间小于模板文件的修改时间，表明编译文件无效，需要重新编译模板文件。实例化一个编译类的对象，调用其编译方法（传入编译文件名）。
         * 做完上面的操作，就可以引入编译文件了。
         * 若开启了自动缓存，则生成缓存文件。这里用到了一个函数 ob_get_contents ，将本来输出在屏幕上的内容输入到缓冲区。再将缓冲区的内容写到缓存文件。
         * 这样就生成了缓存文件，下次就可以不用再经过编译的过程而直接展示。
         * */

    }


}