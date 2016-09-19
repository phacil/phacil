<?php

namespace Phacil\Kernel;

use Phacil\Kernel\App;
use Phacil\Routing\RouteMatcher;
use Phacil\Kernel\Request;
use Phacil\Routing\RouteResolver;

class Dispatcher {
    
    protected static function __escapeGetMethodSeparator($str = null){
        $pos_1_e_comercial  = strpos($str, '&');
        $subtring_antes = rtrim(substr($str, 0, $pos_1_e_comercial), '/');
        $subtring_depois = substr($str, $pos_1_e_comercial+1, strlen($str));
        return $subtring_antes . '/' . $subtring_depois;
    }

    protected static function __parseUri($path){
        
        $path = ($path != '/' && !empty($path))
                ?filter_var(rtrim($path, '/'), FILTER_SANITIZE_STRING)
                :'/';
        return self::__escapeGetMethodSeparator($path);
        
//        Request::setUrl($path);
    }
        
    public static function run($request = null, $routesCollection = null, $response = null){ 
        
        Request::setMethod($request->server->get('REQUEST_METHOD'));
        Request::setUrl($request->getPathInfo());
        Params::setMethod($request->server->get('REQUEST_METHOD'));
        Params::setUrl($request->getPathInfo());
        
//        pr($routesCollection);exit;
        
        $content = null;
        $path = self::__parseUri($request->getPathInfo());
       
        //try{
            $matchedRoute = RouteMatcher::match($routesCollection, Params::getUrl(), Params::getMethod());
            
            $resolvedRoute = RouteResolver::resolve($matchedRoute);
            pr($resolvedRoute);exit;
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
