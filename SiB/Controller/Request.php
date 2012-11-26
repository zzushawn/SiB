<?php
/**
 * Author: ShenLu  
 * Email: lusknight@gmail.com
 * Last modified: 2012-06-27 15:17
 * Filename: Request.php
 * Description: SiB框架中用于获取HTTP的请求参数 
 */

/**
 * HTTP请求中的主要参数有：
 * $_GET,$_POST,$_SERVER,$_COOKIE,$_ENV等
 * 根据这些参数变量来得到HTTP服务器需要使用的值
 */

class Request {

    public function __Construct() {
    }

    /**
     * 获取单个key的get参数
     *
     * @params $key string
     * @params $default mixed
     *
     * return mixed
     */
    public function get($key, $default = null) {

        if(array_key_exists($key, $_GET)){
            return $_GET[$key];
        }

        return $default;
    }

    /**
     * 获取多个key的get参数
     */
    public function gets() {
    }

    /**
     * 获取单个key值的post参数
     *
     * @params $key string
     * @params $default mixed
     *
     * return mixed
     */
    public function post($key, $default = null) {

        if(array_key_exists($key, $_GET)){
            return $_GET[$key];
        }

        return $default;
    }

    /**
     * 获取多个key的post参数
     */
    public function posts() {
    }

    /**
     * 获取Cookie
     */
    public function getCookie($key) {

        if(array_key_exists($key, $_COOKIE)){
            return $_COOKIE[$key];
        }

        return null;
    }
}
