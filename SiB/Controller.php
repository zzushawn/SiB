<?php
/**
 * Author: ShenLu  
 * Email: lusknight@gmail.com
 * Last modified: 2012-06-25 19:31
 * Filename: Controller.php
 * Description: SiB控制器的父类 
 */

class Controller {

    /**
     * 视图类对象
     * @var object
     */
    protected $view;

    /**
     * HTTP请求类对象实例
     * @var object
     */
    protected $request;

    /**
     * HTTP响应类对象实例
     * @var object
     */
    protected $response;

    public function __Construct() {

        $this->view = new View();
        $this->request = new Request();
        $this->response = new Response();
    }

    /**
     * URL 重定向
     *
     * @param string $url
     *
     * return void
     */
    public function redirect($url, $params = array()) {

        //参数分析
        // $url 的参数为  controller/action
        // e.g  www.example.com/index.php/test/index
        // 则 $url = test/index
        if (!$url) {
            return false;
        }

        $getParams = NULL;
        if($params) {
            $getParams = '?' . http_build_query($params);
        }

        $urlArray = explode('/' , $url);

        if (!headers_sent()) {
            Header("Location:" . $this->createUrl($urlArray[0], $urlArray[1], $getParams));
        } else {
            echo '<script type="text/javascript">location.href="' . $this->createUrl($urlArray[0], $urlArray[1], $getParams) . '";</script>';
        }

        exit();
    }

    /**
     * URL 组装
     *
     * @param string $controller
     * @param string $action
     *
     * return string
     */
    public function createUrl($controller, $action = null, $getParams = null) {

        $prefixUrl = $_SERVER['SCRIPT_NAME'];
        $url = $prefixUrl . '/' . $controller . '/' . $action . $getParams;

        return $url;
    }

    /**
     * 显示提示信息(错误和正确的都可以)
     *
     * @param string $message
     * @param string $url
     * @param int $time
     *
     * return void
     */
    public function showMessage($message, $url, $time) {
    }

    /**
     * 开启session
     */
    public function startSession() {

        session_start();
    }
}
