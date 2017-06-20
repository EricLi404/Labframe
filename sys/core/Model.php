<?php
/**
 * Created by PhpStorm.
 * User: leif
 * Date: 2017/6/20
 * Time: 10:16
 */

namespace core;

use core\Config;
use PDO;

class Model
{
    /**
     * @var PDO  数据库 PDO 对象
     */
    protected $db;
    /**
     * @var string  数据库表名
     */
    protected $table;

    /**
     * Model constructor.
     * @param $table
     */
    public function __construct($table = '')
    {
//        创建 PDO 连接
        $this->db = new PDO('mysql:host=' . Config::get('db_host') . ';dbname=' . Config::get('db_name') . ';charset=' . Config::get('db_charset'), Config::get('db_user'), Config::get('db_pwd'));
        $this->table = Config::get('db_table_prefix') . $table;
    }

    /**
     * 获取数据表字段
     * @return array
     */
    public function getFields()
    {
//        拼接 SQL 语句
        $sql = 'SHOW COLUMNS FROM `' . $this->table . '`';
        $pdo = $this->db->query($sql);
//        转换为索引数组
        $result = $pdo->fetchAll(PDO::FETCH_ASSOC);
        $info = [];
        if ($result) {
            foreach ($result as $key => $val) {
                $val = array_change_key_case($val);
                $info[$val['field']] = [
                    'name' => $val['field'],
                    'type' => $val['type'],
                    'default' => $val['default'],
                    'notnull' => (bool)('' === $val['null']),
                    'primary' => (strtolower($val['key']) == 'pri'),
                    'auto' => (strtolower($val['extra']) == 'auto_increment')
                ];
            }
            return $info;
        }
    }

    /**
     * 获取数据库所有表
     * @return array
     */
    public function getTables()
    {
        $sql = 'SHOW TABLES';
        $pdo = $this->db->query($sql);
        $result = $pdo->fetchAll(PDO::FETCH_ASSOC);
        $info = [];
        foreach ($result as $key => $val) {
            $info['key'] = current($val);
        }
        return $info;
    }

    /**
     *  释放 db
     */
    protected function free()
    {
        $this->db = null;
    }

    /**
     * 获取客户端的真实 IP
     * @return array|false|string  IP 地址
     */
    protected function getIp()
    {
        if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), 'unknown')) {
            $ip = getenv("HTTP_CLIENT_IP");
        } else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FOTWARDED_FOR"), 'unknown')) {
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        } else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), 'unknoen')) {
            $ip = getenv("REMOTE_ADDR");
        } else if (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        } else {
            $ip = 'unknown';
        }
        return $ip;
    }

    /**
     * 新增数据
     * @param array $data 数据
     * @return bool 是否增加成功
     */
    public function save($data = [])
    {
//        sql示例： INSERT INTO `JSP`.`final_login` (`id`, `stuid`) VALUES ('1', '2');
        $keys = '';
        $values = '';
        foreach ($data as $key => $val) {
            $keys .= '`' . $key . '`,';
            $values .= "'" . $val . "',";
        }
        $keys = substr($keys, 0, strlen($keys) - 1);
        $values = substr($values, 0, strlen($values) - 1);
        $sql = 'INSERT INTO `' . $this->table . '`(' . $keys . ') VALUES (' . $values . ')';
        $res = $this->db->query($sql);
        if ($res) {
            return true;
        } else {
            $this->log_err('save_error', $sql);
            return false;
        }
    }

    /**
     * 更新数据
     * @param array $data 数据
     * @param array $where where 信息
     * @param string $options and 或者 or
     * @return bool 是否更新成功
     */
    public function update($data = [], $where = [], $options = 'and')
    {
//        sql 示例： UPDATE `JSP`.`final_login` SET `stuid`='777', `password`='888' WHERE `id`='1';
        $keys = '';
        $wheres = '';
        foreach ($data as $key => $val) {
            $keys .= "` " . $key . "`='" . $val . "',";
        }
        $keys = substr($keys, 0, strlen($keys) - 1);
        if (count($where) > 1) {
            foreach ($where as $key => $val) {
                $wheres .= "``" . $key . "`='" . $val . "'" . $options . " ";
            }
//            TODO 下边这句把 -2 改成了 -1    ???????
            $wheres = substr($wheres, 0, strlen($wheres) - strlen($options) - 1);
        } else {
            foreach ($where as $key => $val) {
//                TODO 下边这句把 . 去掉了
                $wheres = "`" . $key . "`='" . $val . "'";
            }
        }
        $sql = 'UPDATE ' . $this->table . ' SET ' . $keys . ' WHERE ' . $wheres;
        $res = $this->db->query($sql);
        if ($res) {
            return true;
        } else {
            $this->log_err('update_error', $sql);
            return false;
        }
    }

    /**
     * @param $field
     * @param array $where
     * @param string $options
     * @return bool
     */
    public function select($field, $where = [], $options = 'and')
    {
//        sql示例：  SELECT `username`,`password` FROM `login` WHERE `id` = '1' AND `username` = 'admin';
        $fields = '';
        $wheres = '';
        if (is_string($field)) {
            $fields = $field;
        } else if (is_array($field)) {
            foreach ($field as $k => $v) {
                $fields .= "`" . $v . "`,";
            }
            $fields = substr($fields, 0, strlen($fields) - 1);
        } else {
            $this->log_err('fields error', $field);
            return false;
        }
        if (count($where) > 1) {
            foreach ($where as $k => $v) {
                $wheres .= " $k = $v" . $options;
            }
            $wheres = substr($wheres, 0, strlen($wheres) - strlen($options) - 1);
        } else {
            if (count($where) > 0) {
                foreach ($where as $k => $v) {
                    $wheres = " $k = $v";
                }
            } else {
                $this->log_err('where length error ', $where);
            }
        }
        $sql = "SELECT $fields FROM $this->table WHERE $wheres";
        $res = $this->db->query($sql);
        if ($res) {
            return true;
        } else {
            $this->log_err('select_error', $sql);
            return false;
        }

    }

    /**
     * @param array $where
     * @param string $options
     * @return bool
     */
    public function delete($where = [], $options = 'and')
    {
//        sql 示例：  DELETE FROM `admin` WHERE `id`='4';
        $wheres = '';
        if (count($where) > 1) {
            foreach ($where as $k => $v) {
                $wheres .= " $k = $v" . $options;
            }
            $wheres = substr($wheres, 0, strlen($wheres) - strlen($options) - 1);
        } else {
            if (count($where) > 0) {
                foreach ($where as $k => $v) {
                    $wheres = " $k = $v";
                }
            } else {
                $this->log_err('where length error ', $where);
            }
        }
        $sql = "DELETE FROM $this->table WHERE $wheres";
        $res = $this->db->query($sql);
        if ($res) {
            return true;
        } else {
            $this->log_err('delete_error', $sql);
            return false;
        }
    }

    /**
     * @param string $message
     * @param string $sql
     */
    protected function log_err($message = '', $sql = '')
    {
        $ip = $this->getip();
        $time = date("Y-m-d H:i:s");
        $message = $message . "\r\n$sql" . "\r\n客户IP:$ip" . "\r\n时间 :$time" . "\r\n\r\n";
        $server_date = date("Y-m-d");
        $filename = $server_date . "_SQL.txt";
        $file_path = RUNTIME_PATH . 'log' . DS . $filename;
        $error_content = $message;
        //$error_content="错误的数据库，不可以链接";
        $file = RUNTIME_PATH . 'log'; //设置文件保存目录
        //建立文件夹
        if (!file_exists($file)) {
            if (!mkdir($file, 0777)) {
                //默认的 mode 是 0777，意味着最大可能的访问权
                die("upload files directory does not exist and creation failed");
            }
        }
        //建立txt日期文件
        if (!file_exists($file_path)) {
            //echo "建立日期文件";
            fopen($file_path, "w+");
            //首先要确定文件存在并且可写
            if (is_writable($file_path)) {
                //使用添加模式打开$filename，文件指针将会在文件的开头
                if (!$handle = fopen($file_path, 'a')) {
                    echo "Cannot open $filename";
                    exit;
                }
                //将$somecontent写入到我们打开的文件中。
                if (!fwrite($handle, $error_content)) {
                    echo "Cannot write $filename";
                    exit;
                }
                //echo "文件 $filename 写入成功";
                echo "Error has been saved!";
                //关闭文件
                fclose($handle);
            } else {
                echo "File $filename cannot write";
            }
        } else {
            //首先要确定文件存在并且可写
            if (is_writable($file_path)) {
                //使用添加模式打开$filename，文件指针将会在文件的开头
                if (!$handle = fopen($file_path, 'a')) {
                    echo "Cannot open $filename";
                    exit;
                }
                //将$somecontent写入到我们打开的文件中。
                if (!fwrite($handle, $error_content)) {
                    echo "Cannot write $filename";
                    exit;
                }
                //echo "文件 $filename 写入成功";
                echo "Error has been saved!";
                //关闭文件
                fclose($handle);
            } else {
                echo "File $filename cannot write";
            }
        }

    }


}