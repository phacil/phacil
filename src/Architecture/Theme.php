<?php
namespace Phacil\Architecture;

use Phacil\Component\HTML as Html;

class Theme{
    
    protected static $name = 'default';
    protected static $asset_dir = 'assets';
    
    public static function getName(){
        return self::$name;
    }
        	
    public static function loadView($viewPath, $view, $vars){
       
        return Html::buffer(function() use ($viewPath, $view, $vars){
                foreach($vars as $var => $value){
                if(!isset($$var)){
                    $$var = $value;  
                } 
            }

            include($viewPath . $view . '.htp');
        });        
        
    }
	
    public static function includeLayout($layout = '', $content = '', $vars = ''){
        foreach($vars as $var => $value){
            if(!isset($$var)){
                $$var = $value;  
            }
        }
        
        include THEMES_DIR . self::$name . DS . $layout. '.htp';
    }
    
    public static function includeLayoutViewOnTheme($layout = null, $viewPath = null, $view = null, $vars = array()){
        $content = self::loadView($viewPath, $view, $vars);
        self::includeLayout($layout, $content, $vars);
    }
    
    public static function css($param = array()) {
        $assets = null;
        
        if(!is_array($param)){
            $param = (array) $param;
        }
        
        foreach ($param as $asset) {
            
            if(empty($asset)) { continue;}
            
            $asset = ($asset[0] == '/')?$asset: '/css/' . $asset;
            
            $assets .= Html::link()->href(THEMES_URL. self::$name . '/' . self::$asset_dir . $asset . '.css')
                                    ->rel('stylesheet')
                                    ->type('text/css')
                                    ->output(); 
            $assets .= "\n";
        }        
        return $assets;
    }
    
    public static function js($param = array()) {
        $assets = null;
        
        if(!is_array($param)){
            $param = (array) $param;
        }
        
        foreach ($param as $asset) {
            
            if(empty($asset)) { continue;}
            
            $asset = ($asset[0] == '/')?$asset: '/js/' . $asset;
            $assets .= Html::script()->src(THEMES_URL. self::$name . '/' . self::$asset_dir . $asset . '.js')
                    ->type('text/javascript')
                    ->output(); 
            $assets .= "\n";
        }        
        return $assets;
    }
    
    public static function image($param = '') {
        return Html::img()->src(THEMES_URL. self::$name . '/' . self::$asset_dir . '/images/' . $param);
    }
}
