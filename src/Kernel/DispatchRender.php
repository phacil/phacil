<?php

namespace Phacil\Core\Kernel;

use Phacil\HTTP\Request;
use Phacil\Core\Exception\PhacilException;

class DispatchRender {
    
    private $callback;
    
    public function __construct($callback) {
        $this->callback = $callback;
    }
    
    private function __diffRequestArgs($params = []){
        $_params = $_args = [];
              
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
    
    private function __discardGetArgs(&$params){
        $last = array_last($params);
        if(strpos($last, '&')){
            array_pop_last($params);      
        }       
    }

    private function __defineModuleControllerAction($match = null){
        
        $parts = $newparts = [];
        
        //pr($match);exit;
               
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
        
        $this->__discardGetArgs($parts);
                
        //pr($parts);exit;
        //Verifica se é parte de um prefixo
        
        //Verifica se é parte de um modulo
        if(isset($parts[1]) &&  is_file(BUSINESS_DIR . DS . ucfirst($parts[0]) . DS. ucfirst($parts[1]) . DS . ucfirst($parts[1]) . '.php')){
            $newparts[] = ucfirst($parts[0]) . '\\' . ucfirst($parts[1]);
            $newparts[] = isset($parts[2])?$parts[2]:'index';
            
            Request::module($parts[0]);
            Request::controller($parts[1]);
            Request::action(isset($parts[2])?$parts[2]:'index');
            
            unset($parts[0]);
            unset($parts[1]);
            unset($parts[2]);
        }
        //Verifica se é um controle direto
        else if(is_file(BUSINESS_DIR . DS . ucfirst($parts[0]) . DS . ucfirst($parts[0]) . '.php')){
            $newparts[] = ucfirst($parts[0]);
            $newparts[] = isset($parts[1])?$parts[1]:'index';
           
            Request::controller($parts[0]);
            Request::action(isset($parts[1])?$parts[1]:'index');
            
            unset($parts[0]);
            unset($parts[1]);
        }else{
            throw new PhacilException('Controller not found');
        }
        
        list($_params, $_args) = $this->__diffRequestArgs($parts);
        Request::params($_params);
        Request::args($_args);
        //pr(Request::info());       
        return [$newparts, $_params];
    }
    
    private function loadView($viewPath, $view, $vars){
       
        return html()->buffer(function() use ($viewPath, $view, $vars){
                foreach($vars as $var => $value){
                if(!isset($$var)){
                    $$var = $value;  
                } 
            }
            
            if(!is_file($viewPath . $view . '.htp')){
                throw new PhacilException('View '. $view . ' not found');
            }
            
            include($viewPath . $view . '.htp');
        });
    }
	
    private function includeLayout($layout = '', $content = '', $vars = []){
        //return html()->buffer(function() use ($layout, $vars){
            foreach($vars as $var => $value){
                if(!isset($$var)){
                    $$var = $value;  
                }
            }

            if(!is_dir(THEMES_DIR . theme()->name())){
                throw new PhacilException('Theme '. theme()->name() . ' not found');
            }
            
            if(!is_file(THEMES_DIR . theme()->name() . DS . 'layouts' . DS . $layout. '.php')){
                throw new PhacilException('Layout '. $layout . ' not found');
            }
            
            include THEMES_DIR . theme()->name() . DS . 'layouts' . DS . $layout. '.php';
        //});        
    }
    
    private function includeLayoutViewOnTheme($layout = null, $viewPath = null, $view = null, $vars = array()){
        return html()->buffer(function() use ($layout,$viewPath, $view, $vars){
            $content = $this->loadView($viewPath, $view, $vars);
            $this->includeLayout($layout, $content, $vars);
        });
    }
    
    private function __render($callback, $params = []){

	$controllerPath = '\\' . BUSINESS_NAMESAPACE . "\\" . $callback[0] . '\\' . ucwords(Request::controller());
        
        $objController = new $controllerPath();
        
        if(!method_exists($objController, Request::action())){
            throw new PhacilException('Action '. Request::action() . ' not found');
        }
        
        view()->name(!empty(view()->name())?view()->name():Request::action());
	        
        view()->viewsPath(!empty(view()->viewsPath())?view()->viewsPath()
                : (!is_null(Request::module())?ucwords(Request::module()).DS:'')                
                . ucwords(Request::controller())
                . DS);
        
        call_user_func_array(array($objController, Request::action()), $params);
        
        unset($objController);
        
        return $this->includeLayoutViewOnTheme(theme()->layout(), 
                                        view()->viewsPath(), 
                                        view()->name(), 
                                        view()->vars());
    }
    
    public function run(){
        
        list($callback, $params) = $this->__defineModuleControllerAction($this->callback);        
        return $this->__render($callback, $params);
    }
}
