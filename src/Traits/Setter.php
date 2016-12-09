<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Phacil\Traits;
/**
 * Description of SetterTrait
 *
 * @author alisson
 */
trait Setter {
    
//    public function __call($name, $args) {
//        if($name == 'set'){
//            call_user_func_array([$this, '_set'], $args);
//        }
//    }
    
    public static function set($var, $value = ''){
        self::$__vars[$var] = $value;
    }
    
//    public function _set($var, $value = ''){
//        $this->__vars[$var] = $value;
//    }
}
