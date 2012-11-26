<?php
/**
 * Author: ShenLu  
 * Email: lusknight@gmail.com
 * Last modified: 2012-05-30 14:43
 * Filename: Page.php
 * Description: 分页使用的类 
 */

class Page {

    /**
     * 当前页
     * @var int
     */
    private $page;

    /**
     * 总页数
     * @var int
     */
    private $total;

    /**
     * 每页代表的网址
     * @var string
     */
    private $url;

    /**
     * 每页条目数
     * @var int
     */
    private $offset;

    /**
     * 第一页
     * @var string
     */
    private $firstPage;

    /**
     * 最后一页
     * @var string 
     */
    private $finalPage;

    /**
     * 前一页
     * @var string
     */
    private $lastPage;

    /**
     * 后一页
     * @var string
     */
    private $nextPage;

    /**
     * 分页的CSS样式
     * @var array 
     */
    private $cssStyle = array('padding-top' => '15px',
                              'padding-bottom' => '6px');

    /**
     * 分页的class
     * @string
     */
    private $class = 'nextpages';

    /**
     * @params $total 总共多少条目
     * @params $offset 每页多少条目
     * @params $page 当前是第多少页
     * @params $option array 分页样式
     *  + class 
     *  + style
     */
    public function __construct($total, $offset = 10, $page = 1, $option = array()) {

        if(($total <= 0) || ($offset <= 0) || ($page <= 0)) {
            return false;
        }

        if($total < $offset) {
            $this->total = 1;
        } else {
            $temp = $total%$offset;
            if($temp) {
                $this->total = ($total - $temp)/$offset + 1;
            } else {
                $this->total = ($total - $temp)/$offset;
            }
        }

        $this->page = $page;
        if($this->page > $this->total) {
            $thi->page = $this->total;
        } 
        $this->firstPage = '首页';
        $this->finalPage = '最后一页';
        $this->lastPage = '前一页';
        $this->nextPage = '后一页';
        $this->class = 'nextpages';
        if(!$option) {
            $this->cssStyle = array('padding-top' => '15px',
                                    'padding-bottom' => '6px');
        }else {
            $this->cssStyle = $option;
        }
    }

    /**
     * 获取当前页数
     */
    public function getCurrentPage() {

        if(!isset($_GET['page'])) {
            $this->page = 1;
        } else {
            $this->page = $_GET['page'];
        }
    }

    public function getUrlForPage($url = null) {

        //当url为空时,自动获取url参数. 注:默认当前页的参数为page
        if(!$url) {
            // 当没有其他GET参数时
            if(!$_SERVER['QUERY_STRING']) {
                $this->url = $_SERVER['REQUEST_URI'] . '?page=';
            } else {
                if(stristr($_SERVER['QUERY_STRING'], 'page=') == false){
                    $this->url = $_SERVER['REQUEST_URI'] . '&page=';
                } else {
                    // 去除当前URL中的分页的参数page
                    $this->url = str_ireplace('page=' . $this->page, '', $_SERVER['REQUEST_URI']);
                    $endStr = substr($this->url, -1);
                    if ($endStr == '?' || $endStr == '&') {
                        $this->url .= 'page=';
                    } else {
                        $this->url .= '&page=';
                    }
                }
            }
            return $this;
        }

        $this->url = $url;
        return $this;
    }

    /**
     * 输出组合好的分页的HTML标签
     */
    public function output() {
        // HTML标签组合形式为
        // 首页  上一页--  列表 当前页 列表--- 下一页 最后一页 
        $this->getCurrentPage();
        $this->getUrlForPage();
        $output = '<div ' . $this->_getClass() . $this->_getStyle() . '>' . $this->_getFirstPage() . $this->_getForwardPage() . $this->_getPageList() . $this->_getNextPage() . $this->_getLastPage() . '</div>';
        return $output;
    }

    /**
     * 获取首页的HTML标签
     */
    private function _getFirstPage() {

        if($this->page == 1) {
            $string = null;
        } else {
            $string = '<a href="' . $this->url .'1">' . $this->firstPage . '</a>';
        }
        return $string;
    }

    /**
     * 获取上一页的HTML标签
     */
    private function _getForwardPage() {

        if($this->page >= 2) {
            $string = '<a href="' . $this->url . ($this->page - 1) . '">' . $this->lastPage . '</a>';
            return $string;
        }
        return null;
    }

    /**
     * 获取下一页的HTML标签
     */
    private function _getNextPage() {

        if($this->page < $this->total) {
            $string = '<a href="' . $this->url . ($this->page + 1) . '">' . $this->nextPage . '</a>';
            return $string;
        }
        return null;
    }

    /**
     * 获取最后一页的HTML标签
     */
    private function _getLastPage() {

        if($this->page != $this->total) {
        $string = '<a href="' . $this->url . $this->total . '">' . $this->finalPage. '</a>';
        return $string;
        }
        return null;
    }

    /**
     * 获取页数的列表显示
     */
    private function _getPageList() {
        
        $this->getCurrentPage();
        $string = null;
        for($i = 1; $i <= $this->total; $i++) {
            if($this->page != $i) {
                $string .= '<a href="' . $this->url . $i .'">' . $i . '</a>';
            } else {
                $string .= $i;
            }
        }
        file_put_contents('errlog.txt', $string);
        return $string;
    }

    private function _getStyle() {

        $string = null;
        foreach($this->cssStyle as $key => $value) {
            $string = $key . ':' . $value . '; '; 
        } 

        return 'style="' . $string . '"';
    }

    private function _getClass() {

        return 'class="' . $this->class . '"';
    }
}
