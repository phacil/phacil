<?php
namespace Phacil\Architecture;

use Phacil\HTML\HTML as Html;
use Phacil\Exception\PhacilException;

class Theme{
    
    use \Phacil\Common\Traits\InstanceTrait;
    
    protected static $name = 'default';
    protected static $layout = 'default';
    protected static $asset_dir = 'assets';
    
    public function __construct() {
        self::$instance = $this;
        return $this;
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
    
    public static function name($name = null){
        if(is_null($name)){
            return self::$name;
        }
        self::$name = $name;
    }
    
    public static function layout($layout = null){
        if(is_null($layout)){
            return self::$layout;
        }
        self::$layout = $layout;
    }
    
    public static function assets($asset_dir = null){
        if(is_null($asset_dir)){
            return self::$asset_dir;
        }
        self::$asset_dir = $asset_dir;
    }
    
    public static function piece($piece, $vars = []){
        return html()->buffer(function() use ($piece, $vars){
            foreach($vars as $var => $value){
                if(!isset($$var)){
                    $$var = $value;  
                }
            }
           
            if(!is_dir(THEMES_DIR . theme()->name())){
                throw new PhacilException('Theme '. theme()->name() . ' not found');
            }
            
            if(!is_file(THEMES_DIR . theme()->name() . DS . 'pieces' . DS . $piece. '.php')){
                throw new PhacilException('Piece '. $piece . ' not found');
            }
            
            include THEMES_DIR . theme()->name() . DS . 'pieces' . DS . $piece. '.php';
        });
    }
}
