<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Phacil\Core\Routing;

/**
 * Description of RouteResolver
 *
 * @author alisson
 */
class RouteResolver {
    
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
    
    public static function resolve(Route $route) {
        $route->setCallback(self::__combineCallbackMatches($route->getCallback(), $route->getMatches()));
        return $route;
    }
}
