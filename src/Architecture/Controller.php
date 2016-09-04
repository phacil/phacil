<?php

namespace Phacil\Architecture;

use Phacil\Environment\App as App;

class Controller {
    
    public function __construct(){
        View::set('theme_title', App::get('controller'));
        \Phacil\Integration\Integration::$dbConfig = 'default';       
    }
        
//    protected function getDatabaseConfig($var = null){
//        
//        if(!$var){
//            $var = 'default'; 
//        }
//        
//        $databaseConfig = \Database();
//        
//        return $databaseConfig->{$var};        
//    }
//    
//    protected function setDatabaseConfig($dbConfig = array()){
//        $pdo = new PDO("{$dbConfig['driver']}:host={$dbConfig['host']};dbname={$dbConfig['database']}", $dbConfig['user'], $dbConfig['password']);
//
//        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//        $pdo->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);
//
//        return new FluentPDO($pdo);
//    }
}
