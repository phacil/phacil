<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Phacil\Core\Routing;

use Phacil\HTTP\Request;

class RouteBuilder {
    
    private $url = [];
    private $request = [];

    public function __construct($url = '/') {
        $this->request = Request::info();
        //pr(Request::info()); exit;
        $this->url['url'] = $url;
        return $this;
    }    
       
    private function initRequestInfo($parts = array()) {
        foreach($parts as $part){
            if(empty($this->url[$part])){
                $this->url[$part] = $this->request[$part];
            }
        }
    }    
 
    public function prefix($prefix = ''){
       $this->url['prefix'] = $prefix;
       return $this;
    } 
    
    public function module($module = ''){
        $this->initRequestInfo(array('prefix'));
        $this->url['module'] = $module;
        return $this;
    }
    
    public function controller($controller = ''){
        $this->initRequestInfo(array('prefix', 'module'));
        $this->url['controller'] = $controller;
        return $this;
    }
    
    public function action($action = ''){
        $this->initRequestInfo(array('prefix', 'module', 'controller'));
        $this->url['action'] = $action;
        return $this;
    }
    
    public function params($params = array()){
        $this->initRequestInfo(array('prefix', 'module', 'controller', 'action'));
        $this->url['params'] = $params;
        return $this;
    }
    
    public function args($args = array()){
        $this->initRequestInfo(array('prefix', 'module', 'controller', 'action', 'params'));
        $this->url['args'] = array_merge($this->request['args'], $args);
        return $this;
    }
    
    public function output(){
        return $this->__toString();
    }

    public function __toString() {
        $out = [];
        
        foreach($this->url as $k => $part){
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
