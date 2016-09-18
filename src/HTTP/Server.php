<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Phacil\HTTP;
/**
 * Description of Server
 *
 * @author alisson
 */
class Server {
    
    public static function get($key) {
        if(isset($_SERVER[$key])){
            return $_SERVER[$key];
        }
        return false;
    }
    
    public static function getAll() {
        return $_SERVER;
    }
}
