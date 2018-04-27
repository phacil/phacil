<?php
namespace Phacil\Architecture;

use Phacil\Kernel\App;

class Controller {
    
    public function __construct(){
        View::set('theme_title', App::get('controller'));
        //Integration::$dbConfig = 'default';
    }
} 
