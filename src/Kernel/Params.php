<?php

namespace Phacil\Kernel;

class Params {
    private static $module = null;
    private static $controller = null;
    private static $action = null;
    private static $params = [];
        
    private static $method = 'get';
    private static $url = null;
    private static $prefix = null;
    private static $args = [];
    
    private static $data = [];
    
    static function getModule() {
        return self::$module;
    }

    static function getController() {
        return self::$controller;
    }

    static function getAction() {
        return self::$action;
    }

    static function getParams() {
        return self::$params;
    }

    static function getMethod() {
        return self::$method;
    }

    static function getUrl() {
        return self::$url;
    }

    static function getPrefix() {
        return self::$prefix;
    }

    static function getArgs() {
        return self::$args;
    }

    static function getData() {
        return self::$data;
    }

    static function setModule($module) {
        self::$module = $module;
    }

    static function setController($controller) {
        self::$controller = $controller;
    }

    static function setAction($action) {
        self::$action = $action;
    }

    static function setParams($params) {
        self::$params = $params;
    }

    static function setMethod($method) {
        self::$method = $method;
    }

    static function setUrl($url) {
        self::$url = $url;
    }

    static function setPrefix($prefix) {
        self::$prefix = $prefix;
    }

    static function setArgs($args) {
        self::$args = $args;
    }

    static function setData($data) {
        self::$data = $data;
    }
    
    public static function info($key = null){
        if(!$key){
            return array(
                'module' => self::$module,
                'controller' => self::$controller,
                'action' => self::$action,
                'params' => self::$params,

                'method' => self::$method,
                'url' => self::$url,
                'prefix' => self::$prefix,
                'args' => self::$args,

                'data' => self::$data,
            );
        }else if(isset(self::${$key})){
            return self::${$key};
        }
        return false;
    }
}
