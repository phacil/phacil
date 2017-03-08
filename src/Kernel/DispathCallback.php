<?php

namespace Phacil\Core\Kernel;

class DispathCallback {
    
    private $callback;
    private $matches = [];
    private $namedArgs = []; 
    
    public function __construct($callback, $matches, $namedArgs) {
        $this->callback = $callback;
        $this->matches = $matches;
        $this->namedArgs = $namedArgs;
    }

    private function __compareArgs($callback, $routeArgs) {
        $params = [];
        $ref = new \ReflectionFunction($callback);
        foreach( $ref->getParameters() as $param) {
            if(array_key_exists($param->name, $routeArgs)){
                $params[] = $routeArgs[$param->name];
            }
        }
        return $params;
    }
    
    private function __executeCallback($callback, $params = []){
        return call_user_func_array($callback, $this->__compareArgs($callback, $params));
    }

    public function run(){
        unset($this->matches[0]);
        return $this->__executeCallback($this->callback, array_combine($this->namedArgs, $this->matches));
    }
    
}
