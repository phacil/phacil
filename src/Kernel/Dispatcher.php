<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Phacil\Kernel;

/**
 * Description of Dispatcher
 *
 * @author alisson
 */
class Dispatcher {
    
    public static function run($request = null, $routesCollection = null, $response = null){ 
        return \Phacil\Routing\Router::run();
    }
}
