<?php
namespace Phacil\Core\Kernel;

use Phacil\Core\Kernel\Dispatcher;
use Phacil\Core\Routing\Router;
use Phacil\HTML\Form;
use Phacil\HTTP\Response;
use Phacil\HTTP\Request;
use Phacil\Integration\Integration;
use Phacil\Integration\ORM\ORMQuery;

class App {
    
    use \Phacil\Core\Traits\Setter,
        \Phacil\Core\Traits\Getter;
    
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
        Form::registry('params', "\\Phacil\\Component\\HTTP\\Request");
        Form::registry('data', "\\Phacil\\Component\\HTTP\\Request");
        
        Request::init();
        
        Integration::storeConnection([
            'driver'=>'mysql',
            'username'=>'root',
            'password'=>'asd123',
            'host'=>'localhost',
            'database'=>'testes'
        ], 'default');
        
        ORMQuery::$baseNamespace =  '\\'. BUSINESS_NAMESAPACE .'\\';
               
        call_user_func($callbackRun);
                          
        return Dispatcher::run( Router::routesCollection(),
                                new Response());
    }
}
