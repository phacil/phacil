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
    
    public static function set($var, $value = ''){
        self::$__vars[$var] = $value;
    }
}
