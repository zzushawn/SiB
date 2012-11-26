<?php
/**
 * Author: ShenLu  
 * Email: lusknight@gmail.com
 * Last modified: 2012-06-25 19:32
 * Filename: View.php
 * Description: SiB框架的视图类,利用Smarty作为模板
 * 本类的作用就是Smarty的一个客户端 
 */

require_once(dirname(__FILE__) . '/View/Smarty/libs/Smarty.class.php');

class View {

    /**
     * 模板的实例变量
     */
    public $tpl; 

    /**
     * 模板中的变量的值
     * @var array
     */
    protected $_data;

    /**
     * 获取Smarty实例
     */
    public function &getSmarty() {

        $smarty = new Smarty();

        // 设置Smarty的template目录
        $smarty->template_dir = APP_PATH . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR;
        $smarty->compile_dir = APP_PATH . DIRECTORY_SEPARATOR . 'templates_c' . DIRECTORY_SEPARATOR;
        //$smarty->caching = true;
        //$smarty->cache_dir = APP_PATH . DIRECTORY_SEPARATOR . 'templates_c' . DIRECTORY_SEPARATOR;

        return $smarty;
    }

    /**
     * 给模板变量赋值
     */
    public function set($key, $value) {

        $this->_data[$key] = $value;
    }

    /**
     * 模板的渲染
     * 显示或者返回处理结果
     */
    public function render($tpl = null, $return = false) {

        $smarty = $this->getSmarty();

        if($this->_data) {
            foreach($this->_data as $key => $value) {
                $smarty->assign($key, $value);
            }
        }

        if($tpl) {
            $tpl .= '.tpl';
        } else {
            $tpl = $this->tpl . '.tpl';
        }

        $content = $smarty->fetch($tpl);
        $smarty->display($tpl);
        if(!$return) {
            return $content;
        } else {
            return false;
        }
    }
}
