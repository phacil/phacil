<?php

namespace Phacil\Integration;
use \PDO as PDO;
use \Phacil\Routing\Request as Request;

class QueryBuilderPagination extends QueryBuilder{
    
    public function __construct(Array $config) {
        parent::__construct($config);
    }
    
    private function total_records(){
        $this->limit = null;
        $this->offset = null;
        $this->orderBy = null;
        $this->groupBy = null;

        $total_records = $this->select('COUNT(*) as count')->getAll(false, false);
        
        return $total_records->count;		
    }
    
    public function get() {
        $args = Request::info('args');
        $page = isset($args['page'])?$args['page']:1;
        $limit = isset($args['limit'])?$args['limit']:Pagination::$limit;
        
        $direction = isset($args['direction'])?$args['direction']:'ASC';
        $this->orderBy = isset($args['order'])?$args['order']. ' ' . $direction:null;
        
        $records = parent::get($limit, ($page - 1)*$limit, false);
        //pr($this);
        Pagination::$records = $this->numRows;
        Pagination::$total_records = $this->total_records();
        
        
        $this->reset();        
        return $records;
    }
}
