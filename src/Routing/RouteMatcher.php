<?php

namespace Phacil\Routing;

use Phacil\Kernel\Request;

class RouteMatcher {
    
    protected static function __matchRequestMethod($requestMethod = 'GET', $method){        
        if(in_array($method, array_map('trim', explode('|', $requestMethod)))){
            return true;
        }        
        return false;
    }
    
    public static function match($routesCollection, $path, $method){
        //$path = rtrim($path, '/');
        foreach ($routesCollection as $route) {
            $matches = null;
            $pattern = '/^' . str_replace('/','\\/', $route->getRoute()) . '$/i';
            //PR($pattern);
            //PR(self::$requestUri);
            if(preg_match($pattern, $path, $matches) && self::__matchRequestMethod($route->getMethod(), $method)){
                $route->setMatches($matches);
                return $route;
            }
        }
        throw new \Phacil\Exception\PhacilException('Rota não encontrada');
    } 
}
