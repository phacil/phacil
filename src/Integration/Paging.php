<?php

namespace Phacil\Integration;

use Phacil\HTML\HTML as Html;
use Phacil\Routing\Route as Route;

class Paging {
    
    private $container = array();
    private $list = array();
    
    private $limit = null;
    private $page = null;
    private $records = null;
    private $total_records = null;
    private $total_pages = null;
    
    private $out = null;
    
    public function __construct($container, $list, $paging_options = null) {
        $this->container = $container;
        $this->list = $list;
        
        $this->page = $paging_options['page'];
        $this->limit = $paging_options['limit'];
        $this->records = $paging_options['records'];
        $this->total_records = $paging_options['total_records'];
        $this->total_pages = ceil($paging_options['total_records']/$paging_options['limit']);
    }
    
    private function __linkRota($page) {
        return $rota = Route::url()->args(array('page'=>$page))->output();
    }
    
    public function first($text = '') {
        $class = ($this->page==1)?$this->list['classDisabled']:'';
        $this->out .= Html::{$this->list['tag']}(Html::a($text)->href($this->__linkRota(1)))->class($class)->output();
        return $this;
    }
    
    public function prev($text = '') {
        list($page, $class) = ($this->page==1)
               ?array(1, $this->list['classDisabled'])
               :array($this->page-1, '');
        $this->out .= Html::{$this->list['tag']}(Html::a($text)->href($this->__linkRota($page)))->class($class)->output();
        return $this;
    }
    /* TODO fazer com que limit a quantidade de links apareçam*/
    public function numbers(){
        for($i=1;$i<=$this->total_pages;$i++){
            $class = ($i!=$this->page)?'':$this->list['classActive'];
            $this->out .= Html::{$this->list['tag']}(Html::a($i)->href($this->__linkRota($i)))->class($class)->output();
        }
        return $this;
    }
    
    public function next($text = '') {
        list($page, $class) = ($this->page==$this->total_pages)
               ?array($this->total_pages, $this->list['classDisabled'])
               :array($this->page+1, '');
        $this->out .= Html::{$this->list['tag']}(Html::a($text)->href($this->__linkRota($page)))->class($class)->output();
        return $this;
    }
    
    public function last($text = '') {
        $class = ($this->page==$this->total_pages)?$this->list['classDisabled']:'';
        $this->out .= Html::{$this->list['tag']}(Html::a($text)->href($this->__linkRota($this->total_pages)))->class($class)->output();
        return $this;
    }
    
    public function info($textUnformated) {
        $vars = array(  ':page' => $this->page, 
                        ':totalpages' => $this->total_pages, 
                        ':records' => $this->records, 
                        ':totalrecords' => $this->total_records, 
                        ':firstrecord' => ($this->limit * ($this->page - 1)) + 1,
                        ':lastrecord' => ($this->page==$this->total_pages)?$this->total_records:$this->limit*$this->page
                    );
        
        
        return str_replace(array_keys($vars), $vars, $textUnformated);
    }
    
    public function __toString() {
        return Html::{$this->container['tag']}($this->out)->class($this->container['class'])->output();
    }
}
