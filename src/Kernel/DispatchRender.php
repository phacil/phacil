<?php

namespace Phacil\Kernel;

use Phacil\Architecture\Theme;
use Phacil\Architecture\View;

class DispatchRender {
    
    private $callback;
    
    public function __construct($callback) {
        $this->callback = $callback;
    }
    
    private function __diffRequestArgs($params = []){
        $_params = $_args = [];
        
        $last = array_last($params);
        if(strpos($last, '&')){
            array_pop_last($params);
            $_get_args = explode('&', $last);            
            Request::setGet(array_associate_key_value($_get_args));
        }        
        
        foreach ($params as $param) {
            if(strpos($param, '=')){
                list($k, $v) = explode('=', $param);
                $_args[$k] = $v;
            }else{
                $_params[] = $param;
            }
        }
        return array($_params, $_args);
    }
    
    private function __defineModuleControllerAction($match = null){
        
        $parts = $newparts = [];
        
        if(!is_array($match)){
            $parts = explode('/', ltrim($match, '/'));
        }else{
            if(strpos($match[0], '/')){
                list($parts[0], $parts[1]) = explode('/', $match[0]);         
            }else{
               $parts[0] = $match[0];
            }            
            unset($match[0]);
            
            foreach($match as $param){
                $parts[] = $param;
            }
        }
        
        //pr($parts);exit;
        
        if(isset($parts[1]) &&  is_file(BUSINESS_DIR . DS . ucfirst($parts[0]) . DS. ucfirst($parts[1]) . DS . ucfirst($parts[1]) . '.php')){
            $newparts[] = ucfirst($parts[0]) . '\\' . ucfirst($parts[1]);
            $newparts[] = isset($parts[2])?$parts[2]:'index';
            
            Request::setModule($parts[0]);
            Request::setController($parts[1]);
            Request::setAction(isset($parts[2])?$parts[2]:'index');
            
            unset($parts[0]);
            unset($parts[1]);
            unset($parts[2]);
        }
       
        else if(is_file(BUSINESS_DIR . DS . ucfirst($parts[0]) . DS . ucfirst($parts[0]) . '.php')){
            $newparts[] = ucfirst($parts[0]);
            $newparts[] = isset($parts[1])?$parts[1]:'index';
           
            Request::setController($parts[0]);
            Request::setAction(isset($parts[1])?$parts[1]:'index');
            
            unset($parts[0]);
            unset($parts[1]);
        }else{
            throw new \Phacil\Exception\PhacilException('Controller not found');
        }
        
        list($_params, $_args) = $this->__diffRequestArgs($parts);
        Request::setParams($_params);
        Request::setArgs($_args);
        //pr(Request::info());       
        return [$newparts, $_params];
    } 
    
    private function __render($callback, $params = []){

	$controllerPath = '\\' . BUSINESS_NAMESAPACE . "\\" . $callback[0] . '\\' . ucwords(Request::getController());
        
        $objController = new $controllerPath();
        
        if(!method_exists($objController, Request::getAction())){
            throw new \Phacil\Exception\PhacilException('Action '. Request::getAction() . ' not found');
        }
        
        call_user_func_array(array($objController, Request::getAction()), $params);
        
        View::setName(!empty(View::getName())?View::getName():Request::getAction());
	        
        View::setViewsPath(!empty(View::getViewsPath())?View::getViewsPath()
                : BUSINESS_DIR 
                . ucwords(Request::getModule()) 
                . DS 
                . ucwords(Request::getController()) 
                . DS);
        
        return Theme::includeLayoutViewOnTheme(View::getLayout(), 
                                        View::getViewsPath() , 
                                        View::getName(), 
                                        View::getVars());
    }
    
    public function run(){
        
        list($callback, $params) = $this->__defineModuleControllerAction($this->callback);        
        return $this->__render($callback, $params);
    }
}