<?php
/**
 * Author: ShenLu  
 * Email: lusknight@gmail.com
 * Last modified: 2012-07-02 15:24
 * Filename: Service.php
 * Description: 所有Service的父类 
 */

class Service {

    public static function &factory($serviceName) {

        if(!$serviceName) {
            return null;
        }
        $serviceName .= 'Service';

        $service = new $serviceName();
        return $service;
    }
}
