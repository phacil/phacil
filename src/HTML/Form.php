<?php

namespace Phacil\HTML;

class Form extends HTML {
    
    public static function __callStatic($name, $arguments=[]) {
        if(empty($arguments)){$arguments[0]='';}
        $elementObject = new FormElement($name);
        call_user_func_array(array($elementObject, 'setText'), $arguments);
        //$elementObject->setText($arguments[0],$arguments[1]);
        return $elementObject;
    }
    
    public function open($form_title = null){
        $form = new FormElement('form', true);
        $form->set('text', $form_title);
                
        return $form;
        //return self::_begin('form');
    }
    
    public function close(){
        $form = new FormElement('form_close', true);
        $form->set('text', '');
                
        return $form;
    }
    
//    public function input(){
//        
//    }
//    
//    public function text(){
//        
//    }
    
}


