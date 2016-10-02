<?php

namespace Phacil\Kernel;

class Request {
    private static $module = null;
    private static $controller = null;
    private static $action = null;
    private static $params = [];
        
    private static $method = 'get';
    private static $url = null;
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
    
    static function setGet($get) {
        self::$get = $get;
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
        $this->request['args'] = array_merge(self::$args, $args);
        return $this;
    }
    
    public function output(){
        return $this->__toString();
    }

    public function __toString() {
        $out = array();
        //pr($this->request);
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
                    if($part == '/'){
                        continue;
                    }
                    $part = ltrim($part, '/');
                    $out[] = $part;
                }
            }
        }
       
        return ROOT_URL . join('/', $out);
    } 
}
