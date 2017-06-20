<?php
/**
 * Created by PhpStorm.
 * User: leif
 * Date: 2017/6/20
 * Time: 09:43
 */

namespace core;


/**
 * 模板解析类
 * Class Parser
 * @package core
 */
class Parser
{
    /**
     * @var bool|string 模板内容
     */
    private $content;

    /**
     * Parser constructor.
     * @param $content
     */
    public function __construct($file)
    {
        $this->content = file_get_contents($file);
        if (!$this->content) {
            die('Template file read failed');
        }
    }

    /**
     * 解析变量，并将解析结果存入 $content
     */
    private function parVar()
    {
        $patter = '/\{\$([\w]+)\}/';
        $repVar = preg_match($patter, $this->content);
        if ($repVar) {
            $this->content = preg_replace($patter, "<?php echo \$this->vars['$1']; ?>", $this->content);
        }
    }

    /**
     * 编译模板，将解析内容存入编译后文件
     * @param $parser_file
     */
    public function compile($parser_file)
    {
        $this->parVar();
        file_put_contents($parser_file, $this->content);
    }

//    TODO 其他模板解析语法，参考 ThinkPHP 、Laravel
/*
 * 上面的内容给大家做了一个示例，只定义了解析普通变量的方法。
 * 这里使用了正则表达式来解析，首先获取模板文件的内容，然后使用正则表达式去寻找符合条件的内容，找到之后执行内容替换操作，就换成了我们所熟悉的编写方式。
 * 此方法的处理效果：将混在 html 代码中的形如 {$var} 的内容，替换为<?php echo $this->vars['var']; ?> ，
 * 这样就可以将模板变量在模板文件中展示出来，而不用每次都写很多重复的代码。
 *
 * 由于这个类只是负责解析模板中的特定语法，而不是真正渲染模板内容，所以不需要使用模板变量。
 * 真正的渲染过程将会在 View 中执行。我们这里默认约定的模板语法：普通模板变量，使用 {$var} 标识。
 * 当然，这不是固定的写法，你可以自行设计模板语法或直接在配置文件中设定，然后在解析的时候做一些匹配修改就行。
 * 一个完整的模板引擎所做的功能远远不止这一点，还包括了解析条件判断语法，循环语法，系统变量语法，函数使用方法等等，
 * 大家完全可以仿照上面解析普通变量的方法继续完善其他模板语法的解析：parIf()，parWhile()，parSys()，parFunc() 等。
 *
 * */


}