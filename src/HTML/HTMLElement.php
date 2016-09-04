<?php
/**
* Source : http://davidwalsh.name/create-html-elements-php-htmlelement-class
* Adapted by: Alisson Nascimento <alisson.sa.nascimento@gmail.com>
*  
*/

namespace Phacil\HTML;

class HTMLElement{
    /** 
        @var $type
    */
    public $type = null;
    /** 
        @var $attributes
    */
    public $attributes = array();
    /** 
        @var $self_closers
    */
    public $self_closers =  array('input','img','hr','br','meta','link');
    /** 
        @var $style
    */
    public $style = null;
    /** 
        @var $list
    */
    public $list = null;
    /** 
        @var $listChecked
    */    
    public $listChecked = null;

    /* constructor */
    public function __construct($type, $self_closer = false, $list = array()){
        $this->type = strtolower($type);
        if($self_closer){
            $this->self_closers[] = $this->type;
        }		
    }

    /* get */
    public function get($attribute){
        return $this->attributes[$attribute];
    }

    /* set -- array or key,value */
    public function set($attribute,$value = ''){
        if(!is_array($attribute)){
            $this->attributes[$attribute] = $value;
        }else{
            $this->attributes = array_merge($this->attributes,$attribute);
        }
        return $this;
    }
    
    public function setText($text, $delimiter = " "){
        $texts = array();
        if(!is_array($text)){
            $texts[] = $text ;
        }else{
            $texts = $text;
        }
        $o = array();
        
        foreach ($texts as $t){
            $o[] = (@get_class($t) == __class__)?$t->build():$t;
        }
        $this->attributes['text'] = join($delimiter, $o);
        return $this;
    }
    
    public function content($callback){
        $texto = $callback; 
        if(is_callable($callback)){
            $texto = HTML::buffer($callback); 
        }
        return $this->setText($texto);
    }
	
    /* remove an attribute */
    public function remove($att){
        if(isset($this->attributes[$att]))
        {
            unset($this->attributes[$att]);
        }
    }

    /* clear */
    public function clear(){
        $this->attributes = array();
    }

    /* inject */
    public function inject($object){
        if(@get_class($object) == __class__)
        {
            $this->attributes['text'].= $object->build();
        }
        return $this;
    }

    /* build */
    private function build($build = ""){
            //start
            $build .= '<'.$this->type;

            //add attributes
            if(count($this->attributes))
            {
                    foreach($this->attributes as $key=>$value)
                    {
                            if(!in_array($key, array('text'))) { $build.= ' '.$key.'="'.$value.'"'; }
                    }
            }

            //closing
            if(!in_array($this->type,$this->self_closers))
            {
                    $build.= '>'.$this->attributes['text'].'</'.$this->type.'>';
            }
            else
            {
                    $build.= '>';
            }

            //return it
            return $build;
    }

    private function buildInputGroup($type){
            
            $this->type = 'input';
            $inputs = array();
            $this->attributes['type'] = $type;
            
            $id = null;
            
            if(isset($this->attributes['id'])){
                $id = $this->attributes['id'];
            }
            
            foreach ($this->list as $k => $v){
                
                $this->attributes['id'] = $id . '_' . $k;
                
                if(in_array($k, $this->listChecked)){
                    $this->attributes['checked'] = 'checked';
                }else{
                    unset($this->attributes['checked']);
                }
                
                $inputs[] = $this->build();
                $lable = new HTMLElement('label');
                $inputs[] = $lable->set('text', $v)->set('for', $this->attributes['id'])->output();
               
            }
            return join(' ', $inputs);
        }
	
    /* spit it out */
    public function output(){
        if(in_array($this->type, array('radio', 'checkbox'))){
            return $this->buildInputGroup($this->type);
        }
        return $this->build();
    }
        
    public function __toString() {
        if(in_array($this->type, array('radio', 'checkbox'))){
            return $this->buildInputGroup($this->type);
        }
        return $this->build();
    }
    
    public function escape() {
        return htmlspecialchars($this->build());
    }
        
    public function __call($name, $arguments) {            
        $this->set($name, $arguments[0]);            
        return $this;
    }
    
    /** 
        Especial Attributes Functions       
    */
    
    public function data($attr, $value) {
        $this->set("data-$attr", $value);
        return $this;
    }
    
    public function style($css, $value = null) {
        $style = null;            
        if(is_array($css)){
            foreach($css as $item => $value){
                $style .= "$item:$value;";
            }
        }else if(empty($value)){
            $style .= $css . ';';
        }else{
            $style .= "$css:$value;";
        }
        $this->set("style", isset($this->attributes['style'])?$this->get('style').$style:$style);
        return $this;
    }
    
    
}