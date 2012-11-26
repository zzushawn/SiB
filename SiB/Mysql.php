<?php
/**
 * Author: ShenLu  
 * Email: lusknight@gmail.com
 * Last modified: 2012-06-13 09:17
 * Filename: Mysql.php
 * Description: 连接Mysql数据库代码,完全利用PHP提供的Mysql函数完成 
 */

class Mysql {

    /**
     * 单例模式实例化对象
     *
     * @var object
     */
    public static $instance;

    /**
     * 数据库连接资源
     *
     * @var object
     */
    private $dbLink;

    /**
     * 事务处理开启状态
     *
     * @var boolean
     */
    private $transactions;

    /**
     * 构造函数
     *
     * 用于初始化运行环境,或对基本变量进行赋值
     * @param array $params 数据库连接参数,如主机名,数据库用户名,密码等
     * @return boolean
     */
    private function __construct() {
        
        $params = array(
            'host' => '127.0.0.1',
            'username'    => 'root',
            'password'    => 'root',
            'dbname'      => 'dev',
            'port'        => 3306,
            'charset'     => 'utf8'
        );

        //检测参数信息是否完整
        if (!$params['host'] || !$params['username'] || !$params['password'] || !$params['dbname']) {
            throw new Exception('Mysql Server HostName or UserName or Password or DatabaseName is error in the config file!');
            return false;
        }

        //处理数据库端口
        //如果数据库端口不是3306，在主机后加上端口号
        if ($params['port'] && $params['port'] != 3306) {
            $params['host'] .= ':' . $params['port'];
        }

        //实例化mysql连接ID
        $this->dbLink = @mysql_connect($params['host'], $params['username'], $params['password']);

        if (!$this->dbLink) {
            // 如果连接没成功
            $message = 'Mysql Server connect fail! <br/>Error Message:' . mysql_error() . '<br/>Error Code:' . mysql_errno() . 'Warning';
            throw new Exception($message);
            return false;
        } else {
            if (mysql_select_db($params['dbname'], $this->dbLink)) {
                //设置数据库编码
                mysql_query("SET NAMES {$params['charset']}", $this->dbLink);
            } else {
                //连接错误,提示信息
                $message = 'Mysql Server can not connect database table. Error Code:' . mysql_errno() . ' Error Message:' . mysql_error() . 'Warning';
                throw new Exception($message);
                return false;
            }
        }
        return true;
    }

    /**
     * 执行SQL语句
     *
     * SQL语句执行函数.
     * @access public
     * @param string $sql SQL语句内容
     * @return mixed
     */
    public function sqlQuery($sql) {

        //参数分析
        if (!$sql || !$this->dbLink) {
            return false;
        }

        try {
            $result = mysql_query($sql, $this->dbLink);
        } catch(Exception $e) {
            throw new Exception($e->getMessage());
        }

        return $result;
    }

    /**
     * 获取一行记录（字段型）
     * result 为一维数组
     *
     * @access public
     * @param string $sql SQL语句内容
     * @return array
     */
    public function fetchOne($sql) {

        //参数分析
        if(!$sql) {
            return false;
        }
 
        try {
            $result = $this->sqlQuery($sql);
        } catch(Exception $e) {
            throw new Exception($e->getMessage());
        }

        if (!$result) {
            return false;
        }

        $row = mysql_fetch_assoc($result);
        mysql_free_result($result);

        return $row;
    }

    /**
     * 获取多行记录（字段型）
     * result 为二维数组
     * @access public
     * @param string $sql SQL语句内容
     * @return array 二维数组
     */
    public function fetchAll($sql) {

        //参数分析
        if(!$sql) {
            return false;
        }

        try {
            $result = $this->sqlQuery($sql);
        } catch(Exception $e) {
            throw new Exception($e->getMessage());
        }

        if (!$result) {
            return false;
        }
        //通过循环取出结果集中的所有数据存到数组中
        $rows = array();
        while( $row = mysql_fetch_array($result) ) {
            $rows[] = $row;
        }
        //释放内存，仅当返回结果集很大的时候用
        mysql_free_result($result);

        return $rows;
    }

    /**
     * 析构函数
     *
     * @access public
     * @return void
     */
    public function __destruct() {

        if($this->dbLink) {
            @mysql_close($this->dbLink);
        }
    }

    /**
     * 单例模式
     *
     * @access public
     * @param array $params 数据库连接参数
     * @return object
     */
    public static function getInstance() {

        //如果没有Mysql的实例，就构造一个，否则直接传回当前的实例
        if(!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}
