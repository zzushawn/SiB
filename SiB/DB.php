<?php
/**
 * Author: ShenLu  
 * Email: lusknight@gmail.com
 * Last modified: 2012-06-13 09:24
 * Filename: DB.php
 * Description: 数据库操作,主要拼接SQL语句的增删查改操作 
 *              该数据库操作都是单库单表的操作
 */

class DB {

    const NORMAL = 1;

    const REMOVE = 9;

    /**
     * 构造函数
     * @params $tableName 数据库表名
     * @params $row 数据库表的键值
     */
    public function __construct($tableName, $keys = array()) {


        //获取数据库连接对象
        $_dbConnect = Mysql::getInstance(); 
    }

    /**
     * 数据库的增加操作
     * @access public
     * @params $tableName 数据库表名
     * @params $rows array
     */
    public static function add($tableName, $rows = array()) {

        $_dbConnect = Mysql::getInstance();
        if (!$rows || !is_array($rows)) {
            throw new Exception("the inserting data is NULL!!"); 
            return false;
        }

        $fields = array();
        $params = array();

        foreach($rows as $key => $value) {
            $fields[] = trim($key);
            $params[] = '\'' . self::_escapeString(trim($value)) . '\'';
        }

        $fieldsString = implode(',', $fields);
        $paramsString = implode(',', $params);

        unset($fields);
        unset($params);

        $sql = 'INSERT INTO ' . $tableName . ' (' . $fieldsString . ') VALUES (' . $paramsString . ')';
        try {
            return $_dbConnect->sqlQuery($sql);
        } catch (Exception $e){
            throw new Exception($e->getMessage() . "inserting mysql has a little trouble!!"); 
        }
    }

    /**
     * 数据库的删除操作
     * @access public
     * @params $conditions  string 删除条件
     * @params $params array 删除条件的参数
     */
    public static function remove($tableName, $conditions = array(), $params = array()) {

        if (!$conditions || !is_array($params)) {
            return false;
        }

        //$rows 是行的标志
        //本操作不是真的删除数据，而是将标志位置为删除
        $rows['status'] = self::REMOVE;
        return self::update($tableName, $rows, $conditions, $params);
    }

    /**
     * 数据库的查找单行记录
     * @access pubilc
     * @params $tableName string
     * @params $conditions string
     * @params $params array
     * @params $order array
     * @params $limit array
     *
     * @return array  一维数组
     */
    public static function findOne($tableName, $conditions = NULL, $params = array()) {

        $_dbConnect = Mysql::getInstance();
        $sql = "SELECT * FROM $tableName ";

        if($conditions) {
            $where = self::_buildWhereSql($conditions, $params);
        } else {
            $where = null;
        }
        $sql = $sql . $where;

        try {
            return $_dbConnect->fetchOne($sql);
        } catch (Exception $e){
            throw new Exception($e->getMessage() . "findOne from mysql has a little trouble!!"); 
        }
    }

    /**
     * 数据库的查找多行记录
     * @access pubilc
     * @params $tableName string
     * @params $conditions array
     * @params $params array
     * @params $order array
     * @params $limit array
     *
     * @return array 多维数组
     */
    public static function findAll($tableName, $conditions = array(), $params = array(), $order = array(), $limit = array()) {

        $_dbConnect = Mysql::getInstance();
        $sql = "SELECT * FROM $tableName ";
        if($conditions) {
            $where = self::_buildWhereSql($conditions, $params, $order, $limit);
        } else {
            $where = null;
        }
        $sql = $sql . $where;

        try {
            return $_dbConnect->fetchAll($sql);
        } catch (Exception $e){
            throw new Exception($e->getMessage() . "findAll from mysql has a little trouble!!"); 
        }
    }

    /**
     * 计算记录总数
     *
     * @params $tableName string
     * @params $conditions array
     * @params $params array
     *
     * @return int
     */
    public static function countAll($tableName, $conditions = array(), $params = array()) {
    }

    /**
     * 数据库的更新操作
     *
     * @access public
     * @params $rows array
     * @params $conditions array 
     * @params $params array where子句的参数
     */
    public static function update($tableName, $rows = array(), $conditions = NULL, $params = array()) {

        $_dbConnect = Mysql::getInstance();
        if (!$rows || !is_array($rows)) {
            throw new Exception('update: the column is wrong');
            return false;
        }

        //组合合适的sql语句
        $updateValues = array();
        $sql = "UPDATE $tableName SET ";
        foreach($rows as $key => $value) {
            $updateValues[] = $key . ' = \'' . $value . '\''; 
        }

        $sql = $sql . implode(',', $updateValues);
        if($conditions) {
            $where = self::_buildWhereSql($conditions, $params);
        } else {
            $where = null;
        }

        $sql = $sql . $where;

        try{
            return $_dbConnect->sqlQuery($sql);
        } catch (Exception $e) {
            throw new Exception($e->getMessage() . 'DB: UPDATE');
        }
    }

    /**
     * 数据库的sql语句的组装
     *
     * @access private
     * @conditions string
     * @params  array
     * @params $order array
     * @params $limit array
     *  + start 列表开始记录
     *  + offset 列表偏移记录
     *
     * @return string
     */
    private static function _buildWhereSql($conditions = NULL, $params = array(), $order = array(), $limit = array()) {

        $sql = ' WHERE ';
        $fields = array();

        // 将参数的值转换为MySQL能够认识的字符串形式
        if(!$params) {
            return NULL;
        }
        foreach($params as $key => $value) {
            $fields[] = '\'' . self::_escapeString(trim($value)) . '\'';
        }

        $paramsCount = count($conditions) - 1;
        for($i = 0; $i <= $paramsCount; $i++) {
            if($i != $paramsCount) {
                $sql .= $conditions[$i] . '=' . $fields[$i] . ' AND ';
            } else {
                $sql .= $conditions[$i] . '=' . $fields[$i] . ' ';
            }
        }

        //  增加order by
        if($order) {
            $orders = array();
            foreach($order as $key => $value) {
                $orders[] = $key . ' ' . $value;
            }
            $sql .= implode(',', $orders);
        }

        // 增加限制条数，也就是分页列表
        if($limit) {
            $sql = $sql . "LIMIT {$limit['start']},{$limit['offset']}";
        }

        return $sql;
    }

    /**
     * 数据库中字符串转义
     * @access private
     * @params string
     */
    private static function _escapeString($string) {

        if(!$string)
            return false;
        return mysql_real_escape_string($string);
    }
}
