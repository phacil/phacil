<?php

namespace Phacil\Core\Architecture;
use Phacil\HTML\HTML;
use Phacil\Core\Exception\PhacilException;

class View{
    
    use \Phacil\Core\Traits\InstanceTrait, 
        \Phacil\Core\Traits\StaticGetterSetter;
    
    protected static $name = false;
    protected static $viewsPath = '';
        
    public function __construct() {
        self::$instance = $this;
        return $this;
    }
    
    public static function name($name = false){
        if($name == false){
            return self::$name;
        }
        self::$name = filter_var($name, FILTER_SANITIZE_STRING);
    }
            
    public static function viewsPath($viewsPath = false){
        if($viewsPath == false){
            return self::$viewsPath;
        }
        self::$viewsPath = BUSINESS_DIR . filter_var($viewsPath, FILTER_SANITIZE_STRING);
    }

    public static function vars(){
        return self::$conteiner;
    }
    
    public function load($view, $vars = [], $viewPath = null){
       
        return HTML::buffer(function() use ($viewPath, $view, $vars){
                foreach($vars as $var => $value){
                if(!isset($$var)){
                    $$var = $value;  
                } 
            }
                        
            if(!$viewPath){
                $viewPath  = self::viewsPath();
            }else if($viewPath && !is_file(BUSINESS_DIR . $viewPath)){
                throw new PhacilException('View path '. $viewPath . ' not found');
            }else{
                $viewPath = BUSINESS_DIR . $viewPath;
            }
            
            if(!is_file($viewPath . $view . '.htp')){
                throw new PhacilException('View '. $view . ' not found');
            }
            
            include($viewPath . $view . '.htp');
        });
    }
}
