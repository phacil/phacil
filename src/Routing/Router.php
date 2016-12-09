<?php

namespace Phacil\Routing;

class Router {
    
    protected static $requestUri = null;

    protected static $routes = [];
    
    protected static $mapPrefix = '/';
    
    public static function routesCollection(){
        return self::$routes;
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
    
    public static function scope($prefix = null, $callbackmap = null){
        return self::map($prefix, $callbackmap);
    }

    public static function add($method, $route, $callback = null){
        
        self::$routes[] = new Route($method, self::$mapPrefix . $route, $callback);
        return end(self::$routes);
    }
}
