<?php

namespace Phacil\Architecture;

class View{
    
    protected static $name = false;
    protected static $layout = 'default';
    protected static $viewsPath = '';
    
    protected static $vars = array();
    
    public static function setName($value = null){
        self::$name = $value;
    }
    
    public static function getName(){
        return self::$name;
    }
    
    public static function setLayout($value = null){
        self::$layout = $value;
    }
    
    public static function getlayout(){
        return self::$layout;
    }
    
    public static function setViewsPath($value = null){
        self::$viewsPath = $value;
    }
    
    public static function getViewsPath(){
        return self::$viewsPath;
    }

    public static function set($var, $value = null){
        self::$vars[$var] = $value;
    }
    
    public static function get($var){
        return self::$vars[$var];
    }
    
    public static function getVars(){
        return self::$vars;
    }
}
