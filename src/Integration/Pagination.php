<?php
namespace Phacil\Integration;

use Phacil\Routing\Request as Request;
use Phacil\Routing\Route as Route;
use Phacil\HTML\HTML as Html;
use Phacil\Environment\App;

class Pagination {
        
    protected static $dbConfigs = array();
    
    private static $page = 1;
    private static $orderBy = '';
    private static $direction = 'ASC';
    public static $limit = 10;
    
    public static $records = 0;
    public static $total_records = 0;
    
    public static $container = array('tag'=>'ul', 'class'=>'');
    public static $list = array('tag'=>'li', 'class'=>'', 'classActive'=>'active', 'classDisabled'=>'disabled');    
   
    protected static function __setDbConfig($var = 'default'){
        $dbConfig = App::get('datasources')[$$var];
        return new QueryBuilderPagination($dbConfig);
    }
    
    protected static function __getConnection(){
        $dbConfig = \DB::$dbConfig;
        if(!isset(self::$dbConfigs[$dbConfig])){
            self::$dbConfigs[$dbConfig] = self::__setDbConfig($dbConfig);
        }       
        return self::$dbConfigs[$dbConfig];
    }

    public static function __callStatic($name, $arguments){
        
        $connection = self::__getConnection();
        
        if(method_exists($connection, $name)){
            return call_user_func_array(array($connection, $name), $arguments);
        }else{
            $connection2 = call_user_func_array(array($connection,'table'), (array) $name);
        
            $args = !empty($arguments)?$arguments:array('1', '1');

            return call_user_func_array(array($connection2,'where'), $args);
        }
    } 
    
    public static function pages() {
        $request = Request::info();
        $paging_options = array(
            'page'=> isset($request['args']['page'])?$request['args']['page']:1,
            'limit'=>isset($request['args']['limit'])?$request['args']['limit']:self::$limit,
            'records'=>self::$records,
            'total_records'=>self::$total_records,
        );
        
        return new Paging(self::$container, self::$list, $paging_options);
    }
    
    private static function __setDirection($field){
        $args = Request::info('args');
        
        if(isset($args['order']) && $args['order']==$field && $args['direction']=='ASC'){
            return 'DESC';
        }
        
        return 'ASC';
    }

    public static function order($field, $label = null) {
        $text = ($label)?$label:$field;
        $direction = self::__setDirection($field);
        $rota = Route::url()->args(array('order'=>$field, 'direction'=>  $direction));
        return Html::a($text)->href($rota)->output();
    }
}
