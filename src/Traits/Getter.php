<?php

namespace Phacil\Traits;
/**
 * Description of GetterTrait
 *
 * @author alisson
 */
trait Getter {
    
    public static function get($var = null){
        if($var){
           return self::$__vars;
        }else if(isset(self::$__vars[$var])){
           return self::$__vars[$var];
        }
        return false;
    }
}
