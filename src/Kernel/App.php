<?php
namespace Phacil\Kernel;

use Phacil\Kernel\Dispatcher;
use Phacil\Routing\Router;
use Phacil\Component\HTML\Form;
use Phacil\HTTP\Response;
use Phacil\HTTP\Request;

class App {
    
    use \Phacil\Traits\Setter,
        \Phacil\Traits\Getter;
    
    protected static $__vars = [];
    
    public static function debug($mode = false) {
        
        self::set('debug', $mode);
        
        if ($mode) {
            $whoops = new \Whoops\Run;
            $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
           
            //$whoops->pushHandler(new \Whoops\Handler\JsonResponseHandler());
            $whoops->register();
        } else {            
            ini_set('display_errors','Off');
            ini_set('log_errors', 'On');
            error_reporting(E_ALL & ~E_DEPRECATED);
            ini_set('error_log', ROOT.DS.'tmp'.DS.'logs'.DS.'error.log');
        }
    }
    
    public static final function run(\Closure $callbackRun){
        
        Form::registry('route', "\\Phacil\\Routing\\Route");
        Form::registry('params', "\\Phacil\\HTTP\\Request");
        Form::registry('data', "\\Phacil\\HTTP\\Request");
        
        Request::init();
               
        call_user_func($callbackRun);
                          
        return Dispatcher::run( Router::routesCollection(),
                                new Response());
    }
}
