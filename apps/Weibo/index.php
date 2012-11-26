<?php
/**
 * Author: ShenLu  
 * Email: lusknight@gmail.com
 * Last modified: 2012-06-27 10:01
 * Filename: index.php
 * Description:SiB框架的入口文件 
 */

define('ROOT_PATH', dirname(dirname(dirname(__FILE__))) . '/SiB');
define('APP_PATH', dirname(__FILE__));

require_once(ROOT_PATH . '/SiB.php');

SiB::Run();
