<?php

namespace Phacil\Core\Traits;

trait StaticGetterSetter {

    private static $varConteiner = 'conteiner';
    private static $conteiner = [];
    private static $extrenal = false;
    
    private static function setConteiner($conteiner = 'conteiner', $external = false)
    {
        self::$external = $external;
        self::$varConteiner = $conteiner;
    }    
    
    private static function getConteiner()
    {
        $conteiner = self::$varConteiner;
        if(self::$extrenal){
            return $$conteiner;
        }
        return self::$$conteiner;
    }
    
    public static function clean()
    {
        $conteiner = self::$varConteiner;
        if(self::$extrenal){
            $$conteiner = [];
        }
        self::$$conteiner = [];
    }
    
    public static function get($name)
    {
        $parsed = explode('.', $name);
        $result = self::getConteiner();
        while ($parsed) {
            $next = array_shift($parsed);
            if (isset($result[$next])) {
                $result = $result[$next];
            } else {
                return null;
            }
        }
        return $result;
    }
	
    public static function set($name, $value)
    {
        $c = self::$varConteiner;
        if(!self::$extrenal){
            
            $conteiner =& self::$$c;
        }else{
            $conteiner =& $$c;
        }    
        $parsed = explode('.', $name);
        while (count($parsed) > 1) {
            $next = array_shift($parsed);
            if ( ! isset($conteiner[$next]) || ! is_array($conteiner[$next])) {
                $conteiner[$next] = [];
            }
            $conteiner =& $conteiner[$next];
        }
        $conteiner[array_shift($parsed)] = $value;
    }
    
    public static function check($name)
    {
        return is_null(self::get($name))?false:true;
    }
    
    public static function delete($name)
    {
        $c = self::$varConteiner;
        if(!self::$extrenal){
            
            $conteiner =& self::$$c;
        }else{
            $conteiner =& $$c;
        } 
        $parsed = explode('.', $name);
        while (count($parsed) > 1) {
            $next = array_shift($parsed);
            if ( ! isset($conteiner[$next]) || ! is_array($conteiner[$next])) {
                $conteiner[$next] = [];
            }
            $conteiner =& $conteiner[$next];
        }
        unset($conteiner[array_shift($parsed)]);
    }
    
}
