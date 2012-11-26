<?php
/**
 * Author: ShenLu  
 * Email: lusknight@gmail.com
 * Last modified: 2012-06-19 18:54
 * Filename: Router.php
 * Description: 由URL进行路由功能 
 */

class Router {
    /**
     * 分析路由网址，并从中获取到Controller和Action名称
     * 路由网址的形式如：
     * www.example.com/index.php/controller/action?param1=yes&param2=no
     * 从以上网址中可以获取到controller的名称也可以获取到action的名称
     * 当开启Rewrite功能时，网址变为www.example.com/controller/action?param1=yes&param2=no
     * 本函数只针对以上两种网址进行分析
     * @access public
     * @return array
     *  +controller => ControllerName
     *  +action => actionName
     */
    public static function Request() {
        
        // 从用户输入的URL中提取分析出controller 和 action
        $res = array();
        if(isset($_SERVER['SCRIPT_NAME']) && isset($_SERVER['REQUEST_URI'])) {
           // if(REWRITE) {
           //     // 开启REWRITE功能
           // } else {
                // 没有开启REWRITE功能
                // 去除script name获取后边的字符串
            $urlString = str_replace($_SERVER['SCRIPT_NAME'], '', $_SERVER['REQUEST_URI']);
           // }
            if(!$urlString) {
                // 当前只有index.php时，返回的Controller 和 Action
                $res['controller'] = 'Index';
                $res['action'] = 'index';
                return $res;
            }

            // 去除GET参数
            $pos = strpos($urlString, '?');
            if($pos !== false) {
                $urlString = substr($urlString, 0, $pos);
            }

            $urlInfo = explode('/', $urlString);
            // 获取controller名称
            $res['controller'] = ucfirst(strtolower($urlInfo[1]));
            // 获取action名称
            if(!$urlInfo[2]) {
                $res['action'] = 'index';
            } else {
                $res['action'] = strtolower($urlInfo[2]);
            }
        }

        return $res;
    }
}
