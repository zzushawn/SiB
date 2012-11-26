<?php
/**
 * Author: ShenLu  
 * Email: lusknight@gmail.com
 * Last modified: 2012-09-06 03:09
 * Filename: apps/Test/DAO/TestDAO.php
 * Description: 
 */

class TestDAO extends DB {

    /**
     * 表的名字就是test
     */
    private static $_table = 'health_user';

    /**
     * 主键
     */
    private static $_pk = 'id';

    /**
     * 允许的字段
     */
    private static $_allowed = array('id', 'IMEI', 'nickname', 'gender', 'age', 'photo_path');

    /**
     * 必须的字段
     */
    private static $_required = array('id', 'IMEI', 'nickname');

    /**
     * add 插入一条新记录
     *
     * $data 新记录数据
     *  + id 健康用户数据的主键
     *  + IMEI 健康用户手机的IMEI号
     *  + nickname 健康用户的昵称
     *  + gender 健康用户的性别
     *  + age 健康用户的年龄
     *  + photo_path 健康用户的头像路径
     */
    public static function add($data) {

        return parent::add(self::$_table, $data);
    }

    public static function findById() {

        $conditions = array('id');
        $params = array('5');

        return parent::findOne(self::$_table, $conditions, $params);
    }
}
