<?php

namespace Phacil\Kernel;

class Request{
    
    private static $module = null;
    private static $controller = null;
    private static $action = null;
    private static $params = array();
        
    private static $method = 'get';
    private static $url = null;
    private static $prefix = null;
    private static $args = array();
    
    public static $data = array();
    
    private $request = array('url'=>'',
                             'prefix'=>'',
                             'module'=>'',
                             'controller'=>'',
                             'action'=>'',
                             'params'=>array(),
                             'args'=>array() );

    public static function is($method){
        return strtolower($method) == self::$method;
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
    
    public static function getKeyServer($key) {
        if(isset($_SERVER[$key])){
            return $_SERVER[$key];
        }
        return false;
    }
    
    public static function getAllKeysServer() {
        return $_SERVER;
    }
    
    public static function setModule($module) {
        self::$module = $module;
    }

    public static function setController($controller) {
        self::$controller = $controller;
    }

    public static function setAction($action) {
        self::$action = $action;
    }

    public static function setParams($params) {
        self::$params = $params;
    }

    public static function setUrl($url) {
        self::$url = $url;
    }

    public static function setPrefix($prefix) {
        self::$prefix = $prefix;
    }

    public static function setArgs($args) {
        self::$args = $args;
    }

    public static function setMethod($method) {
        self::$method = strtolower($method);
    }

    public static function setData($data) {
        self::$data = $data;
    }
    
    public static function escapePOSTandFILESputData() {
        if ( get_magic_quotes_gpc() ) {
            $_POST   = stripSlashesDeep($_POST  );
            $_COOKIE = stripSlashesDeep($_COOKIE);
            $_FILES = stripSlashesDeep($_FILES);
        }
        self::$data = array_merge(self::$data, $_POST);
        self::$data = array_merge(self::$data, $_FILES);
        
        if(isset(self::$data['_method'])){
            self::setMethod(self::$data['_method']);
            unset(self::$data['_method']);
        }
    }
    
    public function __construct($url = '/') {
        $this->request['url'] = $url;
        return $this;
    }    
       
    private function initRequestInfo($parts = array()) {
        foreach($parts as $part){
            if(empty($this->request[$part])){
                $this->request[$part] = self::$$part;
            }
        }
    }    
 
    public function prefix($prefix = ''){
       $this->request['prefix'] = $prefix;
       return $this;
    } 
    
    public function module($module = ''){
        $this->initRequestInfo(array('prefix'));
        $this->request['module'] = $module;
        return $this;
    }
    
    public function controller($controller = ''){
        $this->initRequestInfo(array('prefix', 'module'));
        $this->request['controller'] = $controller;
        return $this;
    }
    
    public function action($action = ''){
        $this->initRequestInfo(array('prefix', 'module', 'controller'));
        $this->request['action'] = $action;
        return $this;
    }
    
    public function params($params = array()){
        $this->initRequestInfo(array('prefix', 'module', 'controller', 'action'));
        $this->request['params'] = $params;
        return $this;
    }
    
    public function args($args = array()){
        $this->initRequestInfo(array('prefix', 'module', 'controller', 'action', 'params'));
        $this->request['args'] = array_merge(Request::$args, $args);
        return $this;
    }
    
    public function output(){
        return $this->__toString();
    }

    public function __toString() {
        $out = array();
        foreach($this->request as $k => $part){
            if(!empty($part)){
                if($k == 'args'){
                    $out2 = array();
                    foreach($part as $idx => $value){
                        $out2[] = $idx . '='.$value;
                    }
                    $out[] = join('/', $out2);
                }else if($k == 'params'){
                    $out[] = join('/', $part);
                }else{
                    $part = ltrim($part, '/');
                    $out[] = $part;
                }
            }
        }
       
       return rtrim(ROOT_URL, '/') . join('/', $out);
    } 
}