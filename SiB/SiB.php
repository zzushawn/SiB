<?php
/**
 * Author: ShenLu  
 * Email: lusknight@gmail.com
 * Last modified: 2012-06-25 10:06
 * Filename: SiB.php
 * Description: SiB框架运行的起始类 
 */

abstract class SiB {

    /**
     * 控制器名称
     */
    public static $controller;

    /**
     * 动作名称
     */
    public static $action;


    public static function Run() {

        // 获取URL参数
        spl_autoload_register(array('SiB', '_autoload'));
        $urlParams = Router::Request();
        self::$controller = $urlParams['controller'];
        self::$action = $urlParams['action'];

        $controller = self::$controller . 'Controller';
        $action = self::$action . 'Action';

        if(is_file(APP_PATH . '/Controller/' . $controller . '.php')) {
            self::loadFile(APP_PATH . '/Controller/' . $controller . '.php');
        }

        // new一个控制器页面对象,并进行action的执行
        $controllerObject = new $controller();

        $controllerObject->$action();
    }

    /**
     * 加载文件
     */
    public static function loadFile($file) {

        if(!$file) {
            return false;
        }

        include_once $file;
        return true;
    }

    /**
     * 自动加载
     * @param $className string
     *
     * @return bool
     */
    protected static function _autoload($className) {

        $framework = array(
            'DB' => 'DB.php',
            'Mysql' => 'Mysql.php',
            'Controller' => 'Controller.php',
            'View' => 'View.php',
            'Page' => 'Page.php',
            'Router' => 'Controller/Router.php',
            'Request' => 'Controller/Request.php',
            'Response' => 'Controller/Response.php',
            'SendSmtpMail' => 'SendSmtpMail.php',
            'IpConvert' => 'IpConvert.php',
            'Service' => 'Service.php',
            'SiB' => 'SiB.php'
            );

        if(array_key_exists($className, $framework)) {
            $file = ROOT_PATH . '/' . $framework[$className];
            require_once $file;
            return true;
        }

        // 加载APP的类
        if(substr($className, -3) == 'DAO') {
            $file = APP_PATH . '/DAO/' . $className . '.php';
        } else if(substr($className, -7) == 'Service') {
            $file = APP_PATH . '/Service/' . $className . '.php';
        } else if(substr($className, -10) == 'Controller') {
            $file = APP_PATH . '/Controller/' . $className . '.php';
        } else {
            return false;
        }

        require_once $file;
        return true;
    }
}
