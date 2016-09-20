<?php

namespace Phacil\Kernel;

use Phacil\Kernel\App;
use Phacil\Routing\RouteMatcher;
use Phacil\Kernel\Request;
use Phacil\Routing\RouteResolver;

class Dispatcher {
    
    private static function __escapeGetMethodSeparator($str = null){
        $pos_1_e_comercial  = strpos($str, '&');
        $subtring_antes = rtrim(substr($str, 0, $pos_1_e_comercial), '/');
        $subtring_depois = substr($str, $pos_1_e_comercial+1, strlen($str));
        return $subtring_antes . '/' . $subtring_depois;
    }
    
    private static function escapePOSTandFILESputData() {
        if ( get_magic_quotes_gpc() ) {
            $_POST   = stripSlashesDeep($_POST  );
            $_COOKIE = stripSlashesDeep($_COOKIE);
            $_FILES = stripSlashesDeep($_FILES);
        }
        Params::setData(array_merge(Params::getData(), $_POST));
        Params::setData(array_merge(Params::getData(), $_FILES));
        
        if(isset(Params::getData()['_method'])){
            self::setMethod(Params::getData()['_method']);
        }
    }

    private static function __parseUri($path){
        
        $path = ($path != '/' && !empty($path))
                ?filter_var(rtrim($path, '/'), FILTER_SANITIZE_STRING)
                :'/';
        return self::__escapeGetMethodSeparator($path);
        
//        Request::setUrl($path);
    }
        
    public static function run($request = null, $routesCollection = null, $response = null){ 
        
        Params::setMethod($request->server->get('REQUEST_METHOD'));
        Params::setUrl($request->getPathInfo());
        
//      pr($routesCollection);exit;
        
        $content = null;
        $path = self::__parseUri($request->getPathInfo());
       
        //try{
            $matchedRoute = RouteMatcher::match($routesCollection, Params::getUrl(), Params::getMethod());
            
            $resolvedRoute = RouteResolver::resolve($matchedRoute);
           // pr($resolvedRoute);exit;
            self::escapePOSTandFILESputData();
            
            
            $callback = $resolvedRoute->getCallback();
            $matches = $resolvedRoute->getMatches();
            $namedArgs = $resolvedRoute->getNamedArgs();
            
            if(is_callable($callback) && $callback instanceof \Closure){
                $dispathCallback = new DispathCallback($callback, $matches, $namedArgs);
                $content = $dispathCallback->run();
            }else if(is_array($callback)){
                $dispathRender = new DispatchRender($callback, $matches, $namedArgs);
                $content = $dispathRender->run();
            }else{
                throw new \Phacil\Exception\PhacilException('Error Callback Router');
            }
            $response->setContent($content);
        //}catch (\Phacil\Exception\PhacilException $e){
        //    $response->setContent($e->getMessage());
        //}catch (\Exception $e){
       //     $response->setContent('An Error Occured:'.$e->getMessage());
        //}
        
        $response->send();

        exit;
        //return \Phacil\Routing\Router::run();
    }
}
