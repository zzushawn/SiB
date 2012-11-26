<?php
/**
 * Author: ShenLu  
 * Email: lusknight@gmail.com
 * Last modified: 2012-06-27 17:06
 * Filename: Response.php
 * Description: Controller中用于HTTP响应的类 
 */

class Response{

    public function __Construct() {
    }

    /**
     * 设置cookie，过期时间默认为1个小时
     * @params $key string
     * @params $value mixed
     * @params $time int
     * @params $path string
     * @params $domain string
     *
     * @return boolean
     */
    public function createCookie($key, $value, $time = 0, $path = '/', $domain = null) {

        return setcookie($key, $value, $time, $path, $domain);
    }

    /**
     * 删除Cookie
     * @params $key string
     * @params $value mixed
     * @params $time int
     * @params $path string
     * @params $domain string
     *
     * @return boolean
     */
    public function removeCookie($key, $value, $time = 0, $path = '/', $domain = null) {

        return setcookie($key, $value, $time, $path, $domain);
    }
}
