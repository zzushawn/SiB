<?php
/**
 * Author: ShenLu  
 * Email: lusknight@gmail.com
 * Last modified: 2012-06-26 19:09
 * Filename: TestController.php
 * Description: 
 */

class TestController extends Controller {

    /**
     * Test控制器的index页面
     */
    public function indexAction(){

        // 显示测试页面
        $this->view->set('data', $row);
        $this->view->render('test'); 


        if(isset($_POST['submit'])) {

            $this->redirect('test/direct');
        }
        $indexService = Service::factory('Test');
        //var_dump($indexService->testDatabase());
    }

    public function directAction(){

    }
}
