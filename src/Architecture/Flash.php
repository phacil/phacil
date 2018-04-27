<?php

namespace Phacil\Architecture;

use Phacil\HTTP\Session;

class Flash {
    
    private $message = null;
    private $id = 0;
    private $class = 'alert';
    private $div = 'div';
    private $token = null;
    
    public function __construct() {
        $this->token = strtotime(date('Y-m-d H:i:s'));
        Session::set('Flash.'. $this->token, $this->id);
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
        $id = Session::get('Flash.'.$this->token);
        
        if($id == $this->id){
            Session::delete('Flash.'.$id);
            Session::set('Flash.'. $this->token, $this->id);
        }
        $divHtml = "<$this->div id='$this->id' class='$this->class'>$this->message</$this->div>";
        Session::set('Flash.'.$this->id, $divHtml);
    }
    
    public function message($message){
        $this->message = $message;
        $this->update();
        return $this;
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
    
    public static function show($id = '0'){
        $msg = Session::get('Flash.'.$id);
        
        if(!is_null(Session::get('Flash')) && is_array(Session::get('Flash'))){
            foreach(Session::get('Flash') as $k =>  $v){
                if($v == $id){
                    Session::delete('Flash.'.$k);
                }
            }
        }
        
        Session::delete('Flash.'.$id);
        return $msg;
    }
}
