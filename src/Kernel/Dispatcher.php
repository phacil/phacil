<?php

namespace Phacil\Core\Kernel;

use Phacil\Core\Routing\RouteMatcher;
use Phacil\HTTP\Request;
use Phacil\Core\Routing\RouteResolver;
use Phacil\Core\Exception\PhacilException;

class Dispatcher {
    
    public static function run($routesCollection = null, $response = null){ 
        
        $content = null;
        $path = Request::uri();
//        pr($_SERVER);
//        pr(Server::getAll());exit;
       
        //try{
            $matchedRoute = RouteMatcher::match($routesCollection, $path, Request::method());
            
            $resolvedRoute = RouteResolver::resolve($matchedRoute);
            //pr($resolvedRoute);exit;
                        
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
                throw new PhacilException('Error Callback Router');
            }
            $response->setContent($content);
        //}catch (Phacil\Core\Exception\PhacilException $e){
        //    $response->setContent($e->getMessage());
        //}catch (\Exception $e){
       //     $response->setContent('An Error Occured:'.$e->getMessage());
        //}
        
        $response->send();

        exit;
        //return \Phacil\Routing\Router::run();
    }
}
