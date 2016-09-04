<?php

namespace Phacil\Environment;

class Autoload {
    public static $loader;

    public static function register() {
        
        self::$loader = new self();
            
        return self::$loader;
    }

    public function __construct() {
        //spl_autoload_register(null, false);
        spl_autoload_register(array($this,'shortcut'));
        spl_autoload_register(array($this,'config'));        
        spl_autoload_register(array($this,'business'));
        spl_autoload_register(array($this,'core'));
        spl_autoload_register(array($this,'vendor'));
    }

    public function shortcut($class_name) {
        if(is_file(SHORTCUT_DIR . $class_name. '.php')){
            require(SHORTCUT_DIR . $class_name. '.php');
        }
    }
    
    public function business($class_name) {
        $class_name = str_replace('\\', DS, $class_name);
        $class_name = str_replace_first(BUSINESS_NAMESAPACE, BUSINESS_FOLDER, $class_name);
        if(is_file(ROOT . $class_name. '.php')){
            require(ROOT . $class_name . '.php');
        }
    }

    public function config($class_name) {
        if(is_file(CONFIG_DIR . $class_name. '.php')){
            require(CONFIG_DIR . $class_name. '.php');
        }
    }
    
    public function vendor($class_name) {
        $class_name = str_replace('\\', DS, $class_name);
        if(is_file(VENDOR_DIR . $class_name. '.php')){
            require(VENDOR_DIR . $class_name . '.php');
        }
    } 
    
    public function core($class_name) {
        $class_name = str_replace('\\', DS, $class_name);
        
        if(is_file( VENDOR_DIR. 'phacil'. DS . 'src'. DS . $class_name. '.php')){
            require(VENDOR_DIR. 'phacil'. DS . 'src'. DS . $class_name . '.php');
        }
    } 
}