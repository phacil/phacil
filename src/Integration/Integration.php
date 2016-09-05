<?php
namespace Phacil\Integration;

use Phacil\Environment\App;

class Integration {
        
    protected static $dbConfigs = array();
    public static $dbConfig = 'default';
    
    protected static function __setDbConfig($var = 'default'){        
        $dbConfig = App::get('datasources')[$var];        
        return new QueryBuilder($dbConfig);
    }
    
    protected static function __getConnection(){
        if(!isset(self::$dbConfigs[self::$dbConfig])){
            self::$dbConfigs[self::$dbConfig] = self::__setDbConfig(self::$dbConfig);
        }
        
        return self::$dbConfigs[self::$dbConfig];
    }

    public static function __callStatic($name, $arguments) {
        
        $connection = self::__getConnection();
        
        if(method_exists($connection, $name)){
            return call_user_func_array(array($connection, $name), $arguments);
        }else{
            $connection2 = call_user_func_array(array($connection,'table'), (array) $name);
        
            $args = !empty($arguments)?$arguments:array('1', '1');

            return call_user_func_array(array($connection2,'where'), $args);
        }
    } 
}
