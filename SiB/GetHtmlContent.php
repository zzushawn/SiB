<?php
/**
 * Author: ShenLu  
 * Email: lusknight@gmail.com
 * Last modified: 2012-07-04 10:55
 * Filename: GetHtmlContent.php
 * Description: 网页信息获取 
 */

class GetHtmlContent {

    /**
     * 获取HTML内容的URL
     */
    private $_url;

    private $_curl;

    /**
     * HTML内容
     */
    private $_content;

    public function __Construct($url) {

        $this->_url = $url;
    }

    public function getCurlResource() {
    }
}
