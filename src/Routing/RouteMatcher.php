<?php

namespace Phacil\Core\Routing;

class RouteMatcher {
    
    protected static function __matchRequestMethod($requestMethod = 'GET', $method = null){        
        if(in_array($method, array_map('trim', explode('|', $requestMethod)))){
            return true;
        }        
        return false;
    }
    
    public static function match($routesCollection, $path, $method, $exception = true){
        //$path = rtrim($path, '/');
        foreach ($routesCollection as $route) {
            $matches = null;
            $pattern = '/^' . str_replace('/','\\/', $route->getRoute()) . '$/i';
//            PR($pattern);
//            pr($method);
//            exit;
            if(preg_match($pattern, $path, $matches) && self::__matchRequestMethod($route->getMethod(), $method)){
                $route->setMatches($matches);
                return $route;
            }
        }
        if($exception){
            throw new \Phacil\Core\Exception\PhacilException('Rota não encontrada');
        }
        return false;        
    } 
}
