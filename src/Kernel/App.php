<?php
namespace Phacil\Kernel;

use Phacil\Kernel\Dispatcher;

class App {
    
    use \Phacil\Traits\Setter,
         \Phacil\Traits\Getter;
    
    protected static $__vars = [];
    
    public static function debug($mode = false) {
        
        self::set('debug', $mode);
        
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
    
    public static final function run(\Closure $callbackRun){ 
        call_user_func($callbackRun);
        pr(self::$__vars);
        exit;
        return Dispatcher::run();
    }
}
