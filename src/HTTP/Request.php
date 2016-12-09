<?php

namespace Phacil\HTTP;

class Request {
    private static $module = null;
    private static $controller = null;
    private static $action = null;
    private static $params = [];
        
    private static $method = 'get';
    private static $url = null;
    private static $uri = null;
    private static $prefix = null;
    private static $args = [];
    private static $get = [];
    
    private static $data = [];
    
    private $request = array('url'=>'',
                             'prefix'=>'',
                             'module'=>'',
                             'controller'=>'',
                             'action'=>'',
                             'params'=>array(),
                             'args'=>array() );
    
    static function init(){
        Server::init($_SERVER);
        self::setMethod(Server::get('REQUEST_METHOD'));
        self::setUri(self::__parseUri(Server::get('REDIRECT_QUERY_STRING')));
        self::escapePOSTandFILESputData();
        self::__diffUrl();
    }
    
    private static function __parseUri($path){
        
        $path = ($path != '/' && !empty($path))
                ?filter_var(rtrim($path, '/'), FILTER_SANITIZE_STRING)
                :'/';
        return self::__escapeGetMethodSeparator($path);

    }
    
    private static function __escapeGetMethodSeparator($str = null){
        $pos_1_e_comercial  = strpos($str, '&');
        $subtring_antes = rtrim(substr($str, 0, $pos_1_e_comercial), '/');
        $subtring_depois = substr($str, $pos_1_e_comercial+1, strlen($str));
        return $subtring_antes . '/' . $subtring_depois;
    }
    
    private static function escapePOSTandFILESputData() {
        if ( get_magic_quotes_gpc() ) {
            $_POST   = stripSlashesDeep($_POST  );
            $_COOKIE = stripSlashesDeep($_COOKIE);
            $_FILES = stripSlashesDeep($_FILES);
        }
        self::setData(array_merge(Request::getData(), $_POST));
        self::setData(array_merge(Request::getData(), $_FILES));
        
        if(isset(self::getData()['_method'])){
            self::setMethod(self::getData()['_method']);
        }
    }
    
    private function __diffUrl(){
        $last = array_last(explode('/', self::getUri()));
        
        if(strpos($last, '&')){
            self::setUrl(str_replace($last, '', self::getUri()));
            $_get_args = explode('&', $last);
            Request::setGet(array_associate_key_value($_get_args));
        }else{
           self::setUrl(self::getUri()); 
        }
    }

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
    
    static function getGet() {
        return self::$get;
    }
    
    static function getUri() {
        return self::$uri;
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
        self::$data = is_array($data)?$data:(array)$data;
    }
    
    static function setGet($get) {
        self::$get = $get;
    }
    
    static function setUri($uri) {
        self::$uri = $uri;
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
                'uri' => self::$uri,
                'prefix' => self::$prefix,
                'args' => self::$args,
                'get' => self::$get,

                'data' => self::$data,
            );
        }else if(isset(self::${$key})){
            return self::${$key};
        }
        return false;
    }
    
    public static function is($method){
        if(is_array($method)){
            foreach ($method as $m){
                if (strtoupper($m) == self::$method){
                    return true;
                }
            }            
        }else{
            return strtoupper($method) == self::$method;
        }
        return false;        
    }
}
