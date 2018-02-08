<?php
namespace Phacil\Core\Architecture;

use Phacil\Core\Kernel\App;

class Controller {
    
    public function __construct(){
        View::set('theme_title', App::get('controller'));
        //Integration::$dbConfig = 'default';       
    }
} 
