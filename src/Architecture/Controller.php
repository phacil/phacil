<?php
namespace Phacil\Architecture;

use Phacil\Environment\App as App;
use Phacil\Integration\Integration as Integration;

class Controller {
    
    public function __construct(){
        View::set('theme_title', App::get('controller'));
        Integration::$dbConfig = 'default';       
    }
}