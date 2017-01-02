<?php

namespace Phacil\Core\Architecture;

use Phacil\HTTP\Session;

class Flash {
    
    private $message = null;
    private $id = 0;
    private $class = 'alert';
    private $div = 'div';
    
    public function __construct($message) {
        $this->message = $message;
        $this->update();
        return $this;
    }
    
    public function id($id){
        $this->id = $id;
        $this->update();
        return $this;
    }
    
    public function div($div){
        $this->div = $div;
        $this->update();
        return $this;
    }
    
    public function _class($class){
        $this->class = $class;
        $this->update();
        return $this;
    }
    
    private function update(){        
        $divHtml = "<$this->div id='$this->id' class='$this->class'>$this->message</$this->div>";
        Session::set('Flash.'.$this->id, $divHtml);
    }
    
    public function __toString() {
        return Session::get('Flash.'.$this->id);
    }
    
    public function __call($name, $arguments) {
        if($name == 'class'){
            return $this->_class($arguments[0]);
        }
        return;
    }
    
    public static function message($message){
        return new self($message);
    } 

    public static function show($id = '0'){
        $msg = Session::get('Flash.'.$id);
        Session::delete('Flash.'.$id);
        return $msg;
    }    
}
