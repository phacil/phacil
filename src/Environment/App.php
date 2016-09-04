<?php
/**
 * Description of Core
 *
 * @author alisson
 */

namespace Phacil\Environment;

class App {
    
    protected static $__vars = array();
    
    public static function set($var, $value = ''){
        self::$__vars[$var] = $value;
    }
    
    public static function get($var){
        if(isset(self::$__vars[$var])){
            return self::$__vars[$var];
        }
        return false;
    }
    
    public static function debug($mode = false) {
        
        if ($mode) {
            $whoops = new \Whoops\Run;
            $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
            $whoops->register();
        } else {            
            ini_set('display_errors','Off');
            ini_set('log_errors', 'On');
            error_reporting(E_ALL & ~E_DEPRECATED);
            ini_set('error_log', ROOT.DS.'tmp'.DS.'logs'.DS.'error.log');
        }
    }
    
    public static function run($callbackRun){        
        
        call_user_func($callbackRun);
        \Phacil\Routing\Router::run();
    }
}
