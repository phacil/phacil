<?php

namespace Phacil\Form;

use Phacil\HTML\HTML as HTML;

class Form extends HTML {
    
    public static function __callStatic($name, $arguments=[]) {
        if(empty($arguments)){$arguments[0]='';}
        $elementObject = new FormElement($name);
        call_user_func_array([$elementObject, 'content'], $arguments);
        return $elementObject;
    }
    
    public static function open($content = null){
               
        if(is_callable($content) && $content instanceof \Closure){
           $form = new FormElement('form');

           call_user_func_array([$form, 'content'], [$content]);
           return $form;
        }else{
            $form = new FormElement('form', true);
            call_user_func_array([$form, 'content'], ['']);
        }
        //pr($form); exit;
        
        return $form;
    }
    
    public static function close(){
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


