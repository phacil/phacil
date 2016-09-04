<?php
/**
 * HTML Class
 *
 * @author Alisson Nascimento <alisson.sa.nascimento@gmail.com>
 * @date 2015-11-27
 * 
 */

namespace Phacil\HTML;

class HTML {
        
    public static function __callStatic($name, $arguments=array()) {
        if(empty($arguments)){$arguments[0]='';}
        $elementObject = new HTMLElement($name);
        call_user_func_array(array($elementObject, 'content'), $arguments);
        //$elementObject->setText($arguments[0],$arguments[1]);
        return $elementObject;
    }
    
    public static function begin($tag){
        $elementObject = new HTMLElement($tag, true);
        $elementObject->set('text', '');
        return $elementObject;
    }
    
    public static function end($tag){
        return "</$tag>";
    }
    
    public static function escape($txt){
        return htmlspecialchars($txt);
    }
    
    /*
    Especial Tags Function    
     */ 
    
    public static function select($opcoes = array(), $selected = array(), $empty = false){
        
        $selected = is_array($selected)?$selected:array($selected);
        
        $elementObject = new HTMLElement('select');
        $elementObject->set('text', '');    
        if(!is_bool($empty)){
            $option = new HTMLElement('option');
            $stringOption = $option->value('')->set('text', $empty);
            $elementObject->inject($stringOption);
        }else if($empty){
            $option = new HTMLElement('option');
            $stringOption = $option->value('')->set('text', '');
            $elementObject->inject($stringOption);
        }
        
        foreach($opcoes as $value => $text){
            if(is_array($text)){
                $optgroup = new HTMLElement('optgroup');
                $stringOptionGroup = $optgroup->label($value)->text('');
                
                foreach ($text as $key_value => $text_value) {
                    
                   if(!empty($selected) && (in_array($key_value, $selected))){
                        $option = new HTMLElement('option');
                        $stringOption = $option->value($key_value)->set('text', $text_value)->set('selected', 'selected');
                        $stringOptionGroup->inject($stringOption);
                    }else{
                        $option = new HTMLElement('option');
                        $stringOption = $option->value($key_value)->set('text', $text_value);
                        $stringOptionGroup->inject($stringOption);
                    } 
                }
                $elementObject->inject($stringOptionGroup);
            }else{
                if(!empty($selected) && (in_array($value, $selected))){
                    $option = new HTMLElement('option');
                    $stringOption = $option->value($value)->set('text', $text)->set('selected', 'selected');
                    $elementObject->inject($stringOption);
                }else{
                    $option = new HTMLElement('option');
                    $stringOption = $option->value($value)->set('text', $text);
                    $elementObject->inject($stringOption);
                }   
            }
            
        }
        
        return $elementObject;
    }
    
    public static function radio($list = array(), $checked = array()){
        
        $elementObject = new HTMLElement('radio');
        $elementObject->list = $list;
        $elementObject->listChecked = is_array($checked)?$checked:array($checked);
        return $elementObject;
    }
    
    public static function checkbox($list = array(), $checked = array()){
      
        $elementObject = new HTMLElement('checkbox');
        $elementObject->list = $list;
        $elementObject->listChecked = is_array($checked)?$checked:array($checked);
        return $elementObject;
    }
    
    public static function style($styles = array(), $style = "<style>\n", $close = true){
        
        foreach($styles as $tag => $attrs){
            $style .= $tag . '{' . "\n";
            foreach ($attrs as $attr => $value) {
               if(!is_array($value)){
                   $style .= "\t" . $attr .':'. $value . ";\n";
               }else{
                   $v = array($tag . $attr=>$value);
                  // print_r( $v);
                   $style .= '}'. "\n";
                   $style .= self::style($v, null, false);
               }               
            }
            if($close){
                $style .= '}'. "\n";
            }
            
        }
        if($close){
            $style .= '</style>' . "\n";
        }        
        return $style;        
    }
    
    public static function buffer($callback){        
        if(!extension_loaded('zlib')){
            if (!ob_start("ob_gzhandler")){
                ob_start();
            } 
        }else{
            ob_start();
        }     
        call_user_func($callback);
        $file_content = ob_get_contents();
        ob_end_clean ();
        return $file_content;
    }
    
}
