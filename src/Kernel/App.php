<?php
namespace Phacil\Kernel; 

use Phacil\Kernel\Dispatcher;
use Phacil\Routing\Router;
use Phacil\HTML\Form;
use Phacil\HTTP\Response;
use Phacil\HTTP\Request;
use Phacil\Integration\Integration;
use Phacil\Integration\ORM\ORMQuery;
use Phacil\Integration\ORM\Validator;

class App {
    
    use \Phacil\Common\Traits\StaticGetterSetter,
        \Phacil\Common\Traits\InstanceTrait;

    public function __construct(){
        self::$instance = $this;
        return $this;
    }
    
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
    
    private static function ModifieidSalt($salt){
        return $salt != 'WPMNxcytVVPquMWMU6Hf56';
    }

    public static final function run(\Closure $callbackRun){
        
        Form::registry('route', "\\Phacil\\Core\\Routing\\Route");
        Form::registry('params', "\\Phacil\\HTTP\\Request");
        Form::registry('data', "\\Phacil\\HTTP\\Request");
        
        Request::init();
        
        ORMQuery::$baseNamespace =  '\\'. BUSINESS_NAMESAPACE .'\\';
               
        call_user_func($callbackRun);
        
        $saltModified = self::ModifieidSalt(App::get('Config.Salt'));        
        App::set('Salt.modified',$saltModified);
        
        Validator::setErrorsFolderValidate(App::get('Validator.folder'));
        Validator::setDefaultLang(App::get('Validator.lang'));
        
        foreach (App::get('Config.datasources') as $config => $source) {
            Integration::storeConfig($source, $config);
        }
                                  
        return Dispatcher::run( Router::routesCollection(),
                                new Response());
    }
}
