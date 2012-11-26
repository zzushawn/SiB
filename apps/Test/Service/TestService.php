<?php
/**
 * Author: ShenLu  
 * Email: lusknight@gmail.com
 * Last modified: 2012-08-07 01:20
 * Filename: TestService.php
 * Description: 
 */

class TestService extends Service {

    public function testDatabase() {

        $rows = array(
            'IMEI' => '357207698216008',
            'nickname' => 'dog',
            'gender' => '1',
            'age'  => '25'
            );
        try {
            $results = TestDAO::findById();
        } catch(Exception $e) {
            echo $e->getMessage();
        }

        return $results;
    }

}
