<?php

namespace Phacil\Traits;
/**
 * Description of GetterTrait
 *
 * @author alisson
 */
trait Getter {
    
//    public function __call($name, $args) {
//        if($name == 'get'){
//            call_user_func_array([$this, '_get'], $args);
//        }
//    }
    
    public static function get($var = null){
        if(!$var){
           return self::$__vars;
        }else if(isset(self::$__vars[$var])){
           return self::$__vars[$var];
        }
        return false;
    }
    
//    public function _get($var = null){
//        if(!$var){
//           return $this->__vars;
//        }else if(isset($this->__vars[$var])){
//           return $this->__vars[$var];
//        }
//        return false;
//    } 
}
