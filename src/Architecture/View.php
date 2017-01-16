<?php

namespace Phacil\Core\Architecture;

class View{
    
    use \Phacil\Core\Traits\InstanceTrait, 
        \Phacil\Core\Traits\StaticGetterSetter;
    
    protected static $name = false;
    protected static $layout = 'default';
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
            
    public static function layout($layout = false){
        if($layout == false){
            return self::$layout;
        }
        self::$layout = filter_var($layout, FILTER_SANITIZE_STRING);
    }
    
    public static function viewsPath($viewsPath = false){
        if($viewsPath == false){
            return self::$viewsPath;
        }
        self::$viewsPath = filter_var($viewsPath, FILTER_SANITIZE_STRING);
    }

    public static function vars(){
        return self::$conteiner;
    }
}
