<?php

namespace Phacil\Routing;

use Phacil\Architecture\View;
use Phacil\Architecture\Theme;
use Phacil\Kernel\Request;
use Phacil\HTTP\Server;

class Router {
    
    protected static $requestUri = null;

    protected static $routes = [];
    
    protected static $mapPrefix = '/';
    
    protected static function __escapeGetMethodSeparator($str = null){
        $pos_1_e_comercial  = strpos($str, '&');
        $subtring_antes = rtrim(substr($str, 0, $pos_1_e_comercial), '/');
        $subtring_depois = substr($str, $pos_1_e_comercial+1, strlen($str));
        return $subtring_antes . '/' . $subtring_depois;    
    }

    protected static function __parseUri(){
        $redStr = Request::getKeyServer('REDIRECT_QUERY_STRING');        
        Request::setMethod(Server::get('REQUEST_METHOD'));
        self::$requestUri = ($redStr != '/' && !empty($redStr))
                ?filter_var(rtrim($redStr, '/'), FILTER_SANITIZE_STRING)
                :'/';
        self::$requestUri = self::__escapeGetMethodSeparator(self::$requestUri);
        Request::setUrl(self::$requestUri);
        
    }
    
    protected static function __combineCallbackMatches($callback = [], $matches = []) {
        
        if(count($matches) > 1 && !is_callable($callback)){
            $lastMatch = end($matches);
            
                $lastMatch = explode('/', $lastMatch);
                foreach($lastMatch as $part){
                    $callback[] = $part;
                }
                              
        }
        return $callback;
    }
    
    protected static function __matchRequestMethod($requestMethod = 'GET'){
        
        if(in_array($_SERVER['REQUEST_METHOD'], array_map('trim', explode('|', $requestMethod)))){
            return true;
        }        
        return false;
    }
    
    protected static function __diffParamsArgs($params = []){
        $_params = $_args = [];
        
        $last = array_last($params);
        if(strpos($last, '&')){
            array_pop_last($params);
            $_get_args = explode('&', $last);            
            Request::setData(array_associate_key_value($_get_args));
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

    protected static function __defineModuleControllerAction($match = null){
        
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
        
        //pr($parts);
        
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
            exit('ERRO');
        }
        
        list($_params, $_args) = self::__diffParamsArgs($parts);
        Request::setParams($_params);
        Request::setArgs($_args);
        //pr(Request::info());       
        return [$newparts, $_params];
    }   
    
    protected static function __discoverController($controller){
        return array_reverse(array_merge(array(''), explode('/', $controller)));
    }
      
    protected static function __compareArgs($callback, $routeArgs) {
        $params = [];
        $ref = new \ReflectionFunction($callback);
        foreach( $ref->getParameters() as $param) {
            if(array_key_exists($param->name, $routeArgs)){
                $params[] = $routeArgs[$param->name];
            }
        }
        return $params;
    }
    //
    protected static function __executeCallback($callback, $params = []){
        return call_user_func_array($callback, self::__compareArgs($callback, $params));
    }
    
    protected static function __render($callback, $params = []){

	$controllerPath = '\\' . BUSINESS_NAMESAPACE . "\\" . $callback[0] . '\\' . ucwords(Request::info('controller'));
                //pr($callback[0]); exit;
        $objController = new $controllerPath();
        $out = call_user_func_array(array($objController, Request::info('action')), $params);
        
        View::setName(!empty(View::getName())?View::getName():Request::info('action'));
	        
        View::setViewsPath(!empty(View::$viewsPath)?View::$viewsPath
                : BUSINESS_DIR 
                . ucwords(Request::info('module')) 
                . DS 
                . ucwords(Request::info('controller')) 
                . DS);
        
        Theme::includeLayoutViewOnTheme(View::getLayout(), View::getViewsPath() , View::getName(), View::getVars());
    }
    
    protected static function __renderOrExecute($callback = [], $namedParams = [], $matches = []) {
       
        if(is_array($callback)){
            list($callback, $params) = self::__defineModuleControllerAction($callback);
            return self::__render($callback, $params);
        }else{
            unset($matches[0]);
            return self::__executeCallback($callback, array_combine($namedParams, $matches));          
        }        
    }

    protected static function __send404($errorTrigger = null){
        echo '404 '. ' - ' . $errorTrigger;
    }
    
    public static function map($prefix = null, $callbackmap = null) {
        if($prefix){
            self::$mapPrefix = rtrim($prefix, '/');
            $callbackmap();
            self::$mapPrefix = '/';
        }else{
            //TODO
            return false;
        }
    }

    public static function add($method, $route, $callback = null){
        
        self::$routes[] = new Route($method, self::$mapPrefix . $route, $callback);
        return end(self::$routes);
    }
    
    public static function run(){
        
        unregisterGlobals();
        
        self::$routes[] = self::$routes[] = new Route('GET|POST|PUT', self::$mapPrefix . '/*', null);        
        $routes = self::$routes;
        
        self::__parseUri();
            
        foreach ($routes as $route) {
            
            $matches = null;
            $pattern = '/^' . str_replace('/','\\/', $route->getRoute()) . '$/i';
            //PR($pattern);
            //PR(self::$requestUri);
            if(preg_match($pattern, self::$requestUri, $matches) && self::__matchRequestMethod($route->getMethod())){
            //pr(explode('/',ltrim(self::$requestUri, '/')));
            //pr($route);
            $callback = self::__combineCallbackMatches($route->getCallback(), $matches);
            //pr($matches);
            //pr($callback);

            //exit;
            //unset($matches[0]);

            $route->insertHeaders();
            Request::escapePOSTandFILESputData();

            return self::__renderOrExecute($callback, $route->getNamedArgs(), $matches);
                
            }
        }
        // Route don't match
        self::__send404("URL não encontrada");        
    }
}
